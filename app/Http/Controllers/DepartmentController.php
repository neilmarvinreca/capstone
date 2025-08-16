<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource with search and pagination.
     */
    public function index(Request $request)
    {
        $query = Department::withCount('supplies')
            ->with('user')
            ->orderBy('departmentID', 'asc');
        
        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('officename', 'like', "%{$search}%")
                  ->orWhere('departmentID', 'like', "%{$search}%")
                  ->orWhere('locationcode', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Only show non-archived departments
        $departments = $query->paginate(10)->withQueryString();
        
        // Get supplies for the request supply modal
        $supplies = \App\Models\Supply::orderBy('name')->get(['itemID as id', 'name', 'quantity']);
        
        return view('departments.index', compact('departments', 'supplies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'Department User')
            ->orderBy('name')
            ->pluck('name', 'id');
        return view('departments.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log the input for debugging
            Log::debug('Department creation attempt', [
                'input' => $request->all(),
                'user' => auth()->id()
            ]);
            
            // Validate the request
            $validated = $this->validateDepartment($request);
            
            // Log before creating the department
            Log::debug('Creating department with data:', $validated);
            
            // Create the department (without slug since the column doesn't exist)
            $department = Department::create($validated);
            
            if (!$department) {
                throw new \RuntimeException('Failed to create department. No exception was thrown, but department was not created.');
            }
            
            Log::info('Department created successfully', [
                'department_id' => $department->departmentID,
                'by_user' => auth()->id()
            ]);
            
            return redirect()
                ->route('departments.index')
                ->with('success', 'Department created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to let Laravel handle them
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (\Exception $e) {
            // Log the full error with trace
            Log::error('Error creating department', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except('_token')
            ]);
            
            // Return back with input and a more detailed error message
            return back()
                ->withInput()
                ->with('error', 'Error creating department: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        // Eager load the user relationship and count of supplies
        $department->load(['user', 'supplies' => function($query) {
            $query->with('category')->latest()->take(10);
        }])->loadCount('supplies');
        
        return view('departments.show', compact('department'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $users = User::where('role', 'Department User')
            ->orderBy('name')
            ->pluck('name', 'id');
        return view('departments.edit', compact('department', 'users'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        try {
            $validated = $this->validateDepartment($request, $department->departmentID);
            
            // No need to update slug since we're not using it
            
            $department->update($validated);
            
            Log::info('Department updated', [
                'department_id' => $department->departmentID,
                'by_user' => auth()->id()
            ]);
            
            return redirect()
                ->route('departments.edit', $department)
                ->with('success', 'Department updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error updating department. Please try again.');
        }
    }
    
    /**
     * Archive the specified department.
     */
    public function archive(Department $department)
    {
        try {
            if ($department->supplies()->exists()) {
                return back()->with('error', 
                    'Cannot archive department with associated supplies. ' . 
                    'Please reassign or delete the supplies first.');
            }
            
            $department->delete();
            
            return redirect()->route('departments.index')
                ->with('success', 'Department archived successfully');
        } catch (\Exception $e) {
            Log::error('Error archiving department: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error archiving department. Please try again.');
        }
    }
    
    /**
     * Validate department data.
     */
    /**
     * Display a listing of archived departments.
     */
    public function archived(Request $request)
    {
        $query = Department::onlyTrashed()
            ->withCount('supplies')
            ->with('user')
            ->orderBy('deleted_at', 'desc');
            
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('officename', 'like', "%{$search}%")
                  ->orWhere('departmentID', 'like', "%{$search}%")
                  ->orWhere('locationcode', 'like', "%{$search}%");
            });
        }
        
        $departments = $query->paginate(10)->withQueryString();
        
        return view('departments.archived', compact('departments'));
    }
    
    /**
     * Restore the specified archived department.
     */
    public function restore($id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->restore();
        
        return redirect()->route('departments.archived')
            ->with('success', 'Department restored successfully');
    }
    
    /**
     * Permanently delete the specified department.
     */
    public function forceDelete($id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        
        // Check if department has any supplies
        if ($department->supplies()->withTrashed()->exists()) {
            return back()->with('error', 
                'Cannot permanently delete department with associated supplies. ' . 
                'Please delete the supplies first.');
        }
        
        $department->forceDelete();
        
        return redirect()->route('departments.archived')
            ->with('success', 'Department permanently deleted');
    }
    
    protected function validateDepartment(Request $request, $departmentId = null)
    {
        $rules = [
            'locationcode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'locationcode')
                    ->ignore($departmentId, 'departmentID')
            ],
            'officename' => 'required|string|max:100',
            'accountableper' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || $user->role !== 'Department User') {
                        $fail('The selected accountable person must be a Department User.');
                    }
                }
            ],
            'description' => 'nullable|string|max:255',
        ];
        
        return $request->validate($rules);
    }
}
