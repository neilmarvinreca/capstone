<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\Category;
use App\Models\DeployedItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalSupplies = Supply::count();
        $totalCategories = Category::count();
        $totalDepartments = Department::count();
        
        // Get items with low quantity (less than or equal to 5 as a general threshold)
        $lowStockItems = Supply::with('category')
            ->where('quantity', '<=', 5) // Using 5 as a general threshold
            ->orderBy('quantity')
            ->take(5)
            ->get();
            
        $lowStockCount = Supply::where('quantity', '<=', 5)->count();
        
        // Deployed items statistics
        $totalDeployedItems = DeployedItem::count();
        $todayDeployments = DeployedItem::whereDate('created_at', Carbon::today())->count();
        
        // Recent deployments with department and activity log
        $recentDeployments = DeployedItem::with(['department', 'activities.causer'])
            ->withCount('activities')
            ->latest()
            ->paginate(5);  // Changed from take(5)->get() to paginate(5)
            
        // Deployment status distribution
        $deploymentStatuses = DeployedItem::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
            
        // Calendar events
        $calendarEvents = collect();

        // Get all departments with their deployed items for department-based analytics
        $departments = Department::with(['deployedItems' => function($query) {
            $query->select('deployedID', 'departmentID', 'quantity', 'supply_id');
        }])->get();

        // Get all supplies for fund code distribution
        $supplies = Supply::select('itemID', 'fund_code', 'quantity')->get();

        // Calculate total inventory value
        $totalInventoryValue = Supply::sum(DB::raw('quantity * unit_cost'));

        // Supply Analytics Data
        $supplyAnalytics = [
            // Basic Metrics
            'totalItems' => $totalSupplies,
            'totalValue' => $totalInventoryValue,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => Supply::where('quantity', 0)->count(),
            
            // Department Data with Deployed Items
            'departments' => $departments->map(function($dept) {
                return (object)[
                    'id' => $dept->departmentID,
                    'officename' => $dept->officename,
                    'deployedItems' => $dept->deployedItems->map(function($item) {
                        return (object)[
                            'quantity' => $item->quantity ?? 1,
                            'deployedID' => $item->deployedID,
                            'supply_id' => $item->supply_id
                        ];
                    })
                ];
            }),
            
            // All supplies for fund code distribution
            'supplies' => $supplies,
            
            // Category Data
            'categories' => Category::withCount('supplies as items_count')
                ->withSum('supplies as total_quantity', 'quantity')
                ->orderBy('items_count', 'desc')
                ->get(),
                
            // Stock Level Distribution
            'stockLevels' => [
                'critical' => Supply::where('quantity', '<=', 2)->count(),
                'low' => Supply::where('quantity', '>', 2)->where('quantity', '<=', 5)->count(),
                'moderate' => Supply::where('quantity', '>', 5)->where('quantity', '<=', 20)->count(),
                'good' => Supply::where('quantity', '>', 20)->count(),
            ],
            
            // Recent Activity
            'recentlyAdded' => Supply::with('category')
                ->latest()
                ->take(5)
                ->get(),
                
            // Top Supplies
            'topSupplies' => Supply::with('category')
                ->orderBy('quantity', 'desc')
                ->take(5)
                ->get(),
                
            // Stock Movement (last 30 days)
            'stockMovement' => [
                'labels' => collect(range(29, 0))->map(function ($daysAgo) {
                    return now()->subDays($daysAgo)->format('M d');
                }),
                'data' => collect(range(29, 0))->map(function ($daysAgo) {
                    $date = now()->subDays($daysAgo);
                    return Supply::whereDate('created_at', $date->format('Y-m-d'))->count();
                })
            ],
            
            // Low Stock Alerts
            'lowStockAlerts' => Supply::with('category')
                ->where('quantity', '<=', 5)
                ->orderBy('quantity')
                ->take(5)
                ->get(),
                
            // Category Distribution - Full list without limit
            'categoryDistribution' => Category::withCount('supplies')
                ->orderBy('supplies_count', 'desc')
                ->get()
                ->map(function($category) use ($totalSupplies) {
                    return [
                        'name' => $category->categoryName,
                        'count' => $category->supplies_count,
                        'percentage' => $totalSupplies > 0 ? 
                            round(($category->supplies_count / $totalSupplies) * 100, 1) : 0
                    ];
                }),
        ];

        return view('dashboard', [
            'totalSupplies' => $totalSupplies,
            'totalCategories' => $totalCategories,
            'totalDepartments' => $totalDepartments,
            'lowStockItems' => $lowStockItems,
            'lowStockCount' => $lowStockCount,
            'recentDeployments' => $recentDeployments,
            'todayDeployments' => $todayDeployments,
            'supplyAnalytics' => $supplyAnalytics,
            'deploymentStatuses' => $deploymentStatuses,
            'calendarEvents' => $calendarEvents
        ]);
    }
}
