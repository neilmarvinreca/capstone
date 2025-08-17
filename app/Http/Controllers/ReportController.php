<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\Category;
use App\Models\DeployedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Display inventory report.
     */
    public function inventory(Request $request)
    {
        $departments = \App\Models\Department::orderBy('officename')->get();
        $query = Supply::with(['category', 'department', 'addedBy']);
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        $supplies = $query->orderBy('name')->paginate(10);
        return view('reports.inventory', compact('supplies', 'departments'));
    }

    /**
     * Display deployed items report.
     */
    public function deployedItems(Request $request)
    {
        $departments = \App\Models\Department::orderBy('officename')->get();
        $query = DeployedItem::with('deployedBy');
        
        if ($request->filled('department_id')) {
            $query->where('departmentID', $request->department_id);
        }
        
        // Filter by date range if provided
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('dateDeployed', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        $deployedItems = $query->latest()->paginate(10);
        return view('reports.deployed-items', compact('deployedItems', 'departments'));
    }

    /**
     * Display low stock report.
     * Now uses a fixed threshold of 5 items to determine low stock
     */
    public function lowStock(Request $request)
    {
        $departments = \App\Models\Department::orderBy('officename')->get();
        $query = Supply::where('quantity', '<=', 5) // Using 5 as a general threshold
            ->with(['category', 'department.user']);
            
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $supplies = $query->orderBy('quantity')->paginate(10);
        return view('reports.low-stock', compact('supplies', 'departments'));
    }

    /**
     * Export reports to CSV.
     */
    public function export($type, Request $request)
    {
        switch ($type) {
            case 'inventory':
                return $this->exportInventory($request);
            case 'deployed-items':
                return $this->exportDeployedItems($request);
            case 'low-stock':
                return $this->exportLowStock($request);
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }
    }

    /**
     * Export inventory report to CSV.
     */
    private function exportInventory($request)
    {
        $query = Supply::with(['category', 'addedBy'])->orderBy('name');
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        $supplies = $query->get()->map(function ($supply) {
            return [
                'Item Name' => $supply->name,
                'Description' => $supply->description ?? 'N/A',
                'Category' => $supply->category->categoryName ?? 'N/A',
                'Unit Cost' => '₱' . number_format($supply->unit_cost ?? 0, 2),
                'Quantity' => $supply->quantity,
                'Amount' => '₱' . number_format(($supply->unit_cost ?? 0) * $supply->quantity, 2),
                'Added By' => $supply->addedBy ? $supply->addedBy->name : 'N/A',
                'Status' => $supply->quantity <= 5 ? 'Low Stock' : 'In Stock',
            ];
        });
        return $this->generateCsv('inventory.csv', $supplies);
    }

    /**
     * Export deployed items report to CSV.
     */
    private function exportDeployedItems($request)
    {
        $query = DeployedItem::with('deployedBy')->orderBy('itemName');
        if ($request->filled('department_id')) {
            $query->where('departmentID', $request->department_id);
        }
        $deployedItems = $query->get()->map(function ($item) {
            return [
                'Item Name' => $item->itemName ?? 'N/A',
                'Category' => $item->itemCategory ?? 'N/A',
                'Unit Cost' => '₱' . number_format($item->cost ?? 0, 2),
                'Quantity' => $item->quantity ?? 1,
                'Amount' => '₱' . number_format(($item->cost ?? 0) * ($item->quantity ?? 1), 2),
                'Date Deployed' => optional($item->dateDeployed)->format('Y-m-d') ?? 'N/A',
                'Status' => ucfirst($item->status ?? 'N/A'),
                'Added By' => $item->deployedBy ? $item->deployedBy->name : 'N/A',
            ];
        });
        return $this->generateCsv('deployed-items.csv', $deployedItems);
    }

    /**
     * Export low stock report to CSV.
     * Now shows items with quantity <= 5 as low stock
     */
    private function exportLowStock($request)
    {
        $query = Supply::where('quantity', '<=', 5) // Using 5 as a general threshold
            ->with(['category', 'department.user']);
            
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $supplies = $query->get()->map(function ($supply) {
            return [
                'Category' => $supply->category->categoryName ?? 'N/A',
                'Department' => $supply->department ? $supply->department->officename . ' (' . $supply->department->departmentID . ')' : 'N/A',
                'Name' => $supply->name,
                'Current Stock' => $supply->quantity,
                'Minimum Quantity' => 5,
                'Status' => 'Low Stock',
                'Accountable Person' => $supply->department && $supply->department->user ? $supply->department->user->name : 'N/A',
            ];
        });
        
        return $this->generateCsv('low-stock.csv', $supplies);
    }

    /**
     * Generate CSV file from data.
     */
    private function generateCsv($filename, $data)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_keys($data->first()));

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 