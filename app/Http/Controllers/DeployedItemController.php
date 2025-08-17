<?php

namespace App\Http\Controllers;

use App\Models\DeployedItem;
use App\Models\Department;
use App\Models\Supply;
use App\Services\DeploymentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class DeployedItemController extends Controller
{
    /**
     * Display a listing of the deployed items.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        
        // Start building the query
        $query = DeployedItem::with(['department', 'supply', 'activities.causer']);
        
        // Apply department filter for Department Users
        if ($user->role === 'Department User' && $user->department_id) {
            $query->where('department_id', $user->department_id);
        }
        
        // Apply search filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('itemName', 'like', "%{$search}%")
                  ->orWhere('itemDescription', 'like', "%{$search}%")
                  ->orWhere('itemCategory', 'like', "%{$search}%")
                  ->orWhere('deployedID', 'like', "%{$search}%")
                  ->orWhereHas('department', function($q) use ($search) {
                      $q->where('officename', 'like', "%{$search}%")
                        ->orWhere('departmentID', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supply', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Get the results
        $deployedItems = $query->latest()
                             ->paginate(10)
                             ->withQueryString();
            
        // Get summary statistics
        $totalDeployed = DeployedItem::count();
        $totalValue = DeployedItem::sum('cost');
        $byDepartment = DeployedItem::selectRaw('departmentID, count(*) as count, sum(cost) as total_value')
            ->with('department')
            ->groupBy('departmentID')
            ->get();
            
        $recentDeployments = DeployedItem::with(['department', 'supply'])
            ->latest()
            ->take(5)
            ->get();
            
        return view('deployed_items.index', [
            'deployedItems' => $deployedItems,
            'totalDeployed' => $totalDeployed,
            'totalValue' => $totalValue,
            'byDepartment' => $byDepartment,
            'recentDeployments' => $recentDeployments
        ]);
    }

    /**
     * Show the form for creating a new deployed item.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = Department::orderBy('officename')->get();
        $supplies = Supply::orderBy('name')->get();
        
        // Get all users for approvers dropdown
        $approvers = \App\Models\User::orderBy('name')
            ->get(['id', 'name', 'email']);
        
        return view('deployed_items.create', [
            'departments' => $departments,
            'supplies' => $supplies,
            'approvers' => $approvers
        ]);
    }

    /**
     * Store a newly created deployed item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created deployed item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Handle bulk deployment from supplies index
        if ($request->has('items')) {
            return $this->storeBulk($request);
        }
        
        // If deploying from a supply (single item)
        if ($request->filled('supply_id')) {
            return $this->deploySingleItem($request);
        }
        
        // Manual entry validation
        $validated = $request->validate([
            // Remove deployedID validation since it's auto-generated
            // 'deployedID' => 'required|string|unique:deployed_items,deployedID',
            'itemName' => 'required|string|max:255',
            'itemDescription' => 'nullable|string',
            'dateAcquired' => 'required|date|before_or_equal:today',
            'cost' => 'required|numeric|min:0',
            'itemCategory' => 'required|string|max:255',
            'qr_code' => 'required|string|max:255|unique:deployed_items,qr_code',
            'departmentID' => 'required|exists:departments,departmentID',
            'dateDeployed' => 'required|date',
            'status' => 'required|in:active,inactive,under_maintenance,disposed',
            'remarks' => 'nullable|string',
        ], [
            'departmentID.exists' => 'The selected department is invalid.',
            'status.in' => 'The status must be one of: active, inactive, under maintenance, or disposed.',
            'dateAcquired.before_or_equal' => 'The date acquired must be a date before or equal to today.',
            'cost.min' => 'The cost must be a positive number.',
            'qr_code.unique' => 'This QR code is already in use. Please generate a new one.',
        ]);

        // Start database transaction
        return DB::transaction(function () use ($validated, $request) {
            try {
                // Set default values
                $validated['deployed_by'] = Auth::id();
                
                // Ensure QR code is unique
                if (empty($validated['qr_code'])) {
                    $validated['qr_code'] = 'DEP-' . time() . '-' . strtoupper(Str::random(6));
                }

                // Create the deployed item
                $deployedItem = DeployedItem::create($validated);

                // Generate and store QR code image
                $deployedItem->generateQrCodeImage();

                // Log the creation activity
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($deployedItem)
                    ->withProperties($validated)
                    ->log('created');

                // Create notifications for department users
                DeploymentNotificationService::createDeploymentNotifications($deployedItem);

                // If this is an AJAX request, return JSON response
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Item deployed successfully!',
                        'data' => $deployedItem->load('department')
                    ], 201);
                }

                return redirect()
                    ->route('deployed-items.show', $deployedItem)
                    ->with('success', 'Item deployed successfully!');
                    
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Error in DeployedItemController@store: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                
                // If this is an AJAX request, return JSON error
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to deploy item. Please try again.',
                        'error' => config('app.debug') ? $e->getMessage() : null
                    ], 500);
                }
                
                return back()
                    ->withInput()
                    ->with('error', 'Failed to deploy item. Please try again.');
            }
        });
    }
    
    /**
     * Handle bulk deployment of multiple items
     */
    protected function storeBulk(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'deployed_date' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        $department = Department::findOrFail($request->department_id);
        $deployedItems = [];
        
        // Start database transaction
        \DB::beginTransaction();
        
        try {
            foreach ($request->items as $item) {
                $supply = Supply::findOrFail($item['supply_id']);
                
                // Validate available quantity
                if ($supply->quantity < $item['quantity']) {
                    throw new \Exception("Not enough quantity available for {$supply->name}. Available: {$supply->quantity}, Requested: {$item['quantity']}");
                }
                
                // Create deployed item
                $deployedItem = DeployedItem::create([
                    'deployedID' => 'DP-' . strtoupper(Str::random(8)),
                    'itemName' => $supply->name,
                    'itemDescription' => $supply->description,
                    'dateAcquired' => now(),
                    'dateDeployed' => $request->deployed_date,
                    'cost' => $supply->unit_cost * $item['quantity'],
                    'status' => 'active',
                    'department_id' => $department->id,
                    'departmentID' => $department->departmentID,
                    'quantity' => $item['quantity'],
                    'supply_id' => $supply->id,
                    'itemCategory' => $supply->category->name ?? 'Uncategorized',
                    'qr_code' => 'DP-' . strtoupper(Str::random(10)),
                ]);
                
                // Generate and store QR code image
                $deployedItem->generateQrCodeImage();
                
                // Update supply quantity and recalculate amount
                $supply->decrement('quantity', $item['quantity']);
                
                // Recalculate and update the total amount based on remaining quantity
                $supply->update([
                    'amount' => $supply->unit_cost * $supply->quantity
                ]);
                
                // Log the deployment
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($deployedItem)
                    ->withProperties([
                        'supply_id' => $supply->id,
                        'quantity' => $item['quantity'],
                        'department_id' => $department->id,
                        'remaining_quantity' => $supply->quantity,
                        'updated_amount' => $supply->amount
                    ])
                    ->log('Deployed from bulk supply and updated total amount');
                
                // Create notifications for department users
                DeploymentNotificationService::createDeploymentNotifications($deployedItem);
                
                $deployedItems[] = $deployedItem;
            }
            
            // Commit transaction if all items processed successfully
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($deployedItems) . ' items deployed successfully!',
                'redirect' => route('deployed-items.index')
            ]);
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to deploy items: ' . $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Handle single item deployment from supply
     */
    protected function deploySingleItem(Request $request)
    {
        $supply = Supply::where('itemID', $request->supply_id)->firstOrFail();
        
        // Validate available quantity
        if ($supply->quantity < $request->quantity) {
            return back()->with('error', 'Not enough quantity available in stock.');
        }
        
        // Build attributes using the correct column names from the migration
        $attrs = [
            'itemName' => $supply->name,
            'itemDescription' => $supply->description,
            'dateAcquired' => $request->dateAcquired ?? now(),
            'dateDeployed' => $request->dateDeployed ?? now(),
            'cost' => $supply->unit_cost * $request->quantity,
            'itemCategory' => $supply->category ? ($supply->category->categoryName ?? $supply->category->name) : 'Uncategorized',
            'qrCode' => $request->qr_code ?? 'DP-' . strtoupper(Str::random(10)), // Map qr_code from form to qrCode in DB
            'departmentID' => $request->departmentID,
            'status' => $request->status ?? 'active',
            'quantity' => $request->quantity,
            'condition' => 'new',
            'supply_id' => $supply->itemID,
            'purpose' => $request->purpose,
            'deployed_by' => auth()->id(),
            'remarks' => $request->remarks ?? 'Deployed from supply #' . $supply->itemID,
        ];

        $deployedItem = DeployedItem::create($attrs);
        
        // Generate and store QR code image
        $deployedItem->generateQrCodeImage();
        
        // Update the supply quantity and recalculate amount
        $supply->decrement('quantity', $request->quantity);
        
        // Recalculate and update the total amount based on remaining quantity
        $supply->update([
            'amount' => $supply->unit_cost * $supply->quantity
        ]);
        
        // Log the deployment
        activity()
            ->causedBy(auth()->user())
            ->performedOn($deployedItem)
            ->withProperties([
                'supply_id' => $supply->itemID,
                'quantity' => $request->quantity,
                'departmentID' => $request->departmentID,
                'remaining_quantity' => $supply->quantity,
                'updated_amount' => $supply->amount
            ])
            ->log('Deployed from supply and updated total amount');
        
        // Create notifications for department users
        DeploymentNotificationService::createDeploymentNotifications($deployedItem);
            
        return redirect()->route('deployed-items.index')
            ->with('success', 'Item deployed successfully!');
    }

    /**
     * Display the specified deployed item.
     *
     * @param  \App\Models\DeployedItem  $deployedItem
     * @return \Illuminate\View\View
     */
    public function show(DeployedItem $deployedItem)
    {
        $deployedItem->load([
            'department',
            'activities' => function ($query) {
                return $query->latest();
            },
            'activities.causer'
        ]);
        
        return view('deployed_items.show', compact('deployedItem'));
    }

    /**
     * Show the form for editing the specified deployed item.
     *
     * @param  \App\Models\DeployedItem  $deployedItem
     * @return \Illuminate\View\View
     */
    public function edit(DeployedItem $deployedItem)
    {
        $departments = Department::with('user')->orderBy('officename')->get();
        
        return view('deployed_items.edit', [
            'deployedItem' => $deployedItem,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified deployed item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeployedItem  $deployedItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeployedItem $deployedItem)
    {
        $validated = $request->validate([
            'cost' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'itemCategory' => 'required|string|max:255',
            'qrCode' => 'required|string|max:255|unique:deployed_items,qrCode,' . $deployedItem->deployedID . ',deployedID',
            'departmentID' => 'required|exists:departments,departmentID',
            'dateDeployed' => 'required|date',
            'status' => 'required|in:active,inactive,maintenance,retired',
            'remarks' => 'nullable|string',
            'condition' => 'required|in:excellent,good,fair,poor',
            'purpose' => 'nullable|string',
            'itemDescription' => 'nullable|string',
        ]);

        // Format dateDeployed properly
        if (isset($validated['dateDeployed'])) {
            $validated['dateDeployed'] = \Carbon\Carbon::parse($validated['dateDeployed'])->format('Y-m-d');
        }

        try {
            // Store old values for activity log
            $oldValues = $deployedItem->getOriginal();
            
            // Update the deployed item
            $deployedItem->update($validated);
            
            // Log the update activity with changed values
            activity()
                ->causedBy(Auth::user())
                ->performedOn($deployedItem)
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => $validated
                ])
                ->log('updated');

            return redirect()
                ->route('deployed-items.show', $deployedItem)
                ->with('success', 'Item updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified deployed item from storage.
     * Note: This is included for completeness but should be used with caution
     * as it affects the audit trail. Consider using soft deletes instead.
     *
     * @param  \App\Models\DeployedItem  $deployedItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeployedItem $deployedItem)
    {
        try {
            // Log the deletion activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($deployedItem)
                ->log('deleted');
                
            $deployedItem->delete();
            
            return redirect()
                ->route('deployed-items.index')
                ->with('success', 'Item archived successfully! It can be restored from the archived items list.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to archive item: ' . $e->getMessage());
        }
    }

    /**
     * Archive the specified deployed item.
     *
     * @param  string  $id  The deployedID of the item to archive
     * @return \Illuminate\Http\Response
     */
    /**
     * Archive the specified deployed item.
     *
     * @param  string  $deployedID  The deployedID of the item to archive
     * @return \Illuminate\Http\Response
     */
    public function archive($deployedID)
    {
        try {
            $deployedItem = DeployedItem::where('deployedID', $deployedID)->firstOrFail();
            
            // Check if the item is already archived
            if ($deployedItem->trashed()) {
                return back()->with('error', 'Item is already archived.');
            }
            
            // Soft delete the item
            $deployedItem->delete();
            
            // Log the archive activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($deployedItem)
                ->log('archived');
            
            return redirect()->route('deployed-items.index')
                ->with('success', 'Item has been archived successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error archiving deployed item', [
                'deployedID' => $deployedID,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to archive item: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of archived deployed items.
     *
     * @return \Illuminate\View\View
     */
    public function archived()
    {
        \Log::info('Accessing archived items', [
            'user_id' => auth()->id(),
            'authenticated' => auth()->check(),
            'url' => request()->fullUrl()
        ]);

        try {
            $deployedItems = DeployedItem::onlyTrashed()
                ->with(['department', 'supply'])
                ->latest('deleted_at')
                ->paginate(10);
                
            \Log::info('Archived items query results', [
                'count' => $deployedItems->total(),
                'items' => $deployedItems->pluck('id')
            ]);
                
            return view('deployed_items.archived', compact('deployedItems'));
            
        } catch (\Exception $e) {
            \Log::error('Error in archived method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to see the error in the browser
        }
    }

    /**
     * Restore the specified archived deployed item.
     *
     * @param  \App\Models\DeployedItem  $deployed_item
     * @return \Illuminate\Http\Response
     */
    public function restore($deployedID)
    {
        $deployedItem = DeployedItem::withTrashed()->where('deployedID', $deployedID)->firstOrFail();
        
        if (!$deployedItem->trashed()) {
            return redirect()->route('deployed-items.archived')
                ->with('error', 'Item is not in the trash.');
        }
        
        try {
            $deployedItem->restore();
            
            // Log the restoration activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($deployedItem)
                ->log('restored');
            
            return redirect()
                ->route('deployed-items.archived')
                ->with('success', 'Item restored successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to restore item: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete the specified archived deployed item.
     *
     * @param  \App\Models\DeployedItem  $deployed_item
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($deployedID)
    {
        $deployedItem = DeployedItem::withTrashed()->where('deployedID', $deployedID)->firstOrFail();
        
        if (!$deployedItem->trashed()) {
            return redirect()->route('deployed-items.archived')
                ->with('error', 'Item is not in the trash.');
        }
        
        try {
            // Log the permanent deletion activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($deployedItem)
                ->log('permanently deleted');
                
            $deployedItem->forceDelete();
            
            return redirect()
                ->route('deployed-items.archived')
                ->with('success', 'Item permanently deleted successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to permanently delete item: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code image for a deployed item
     *
     * @param  \App\Models\DeployedItem  $deployedItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateQrCode(DeployedItem $deployedItem)
    {
        try {
            $qrCodePath = $deployedItem->generateQrCodeImage();
            
            if ($qrCodePath) {
                return response()->json([
                    'success' => true,
                    'message' => 'QR code image generated successfully!',
                    'qr_code_image_url' => $deployedItem->fresh()->qr_code_image_url,
                    'qr_code_text' => $deployedItem->qrCode
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code image. Please check if QR code text exists.'
                ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('Error generating QR code image: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code image: ' . $e->getMessage()
            ], 500);
        }
    }
}