@extends('layouts.app')

<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">
@section('title', 'Dashboard')

@push('styles')
<style>
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-active { background-color: #10b981; color: white; }
    .status-inactive { background-color: #6b7280; color: white; }
    .status-maintenance { background-color: #f59e0b; color: white; }
    .status-retired { background-color: #ef4444; color: white; }
</style>
    <link rel="stylesheet" href="{{ asset('dist/css/apexcharts.css') }}">
@endpush

@section('content')
<!-- BEGIN: Main Content -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- BEGIN: Descriptive Analytics -->
    <div class="col-span-12">
        <div class="intro-y flex items-center h-10 mb-5">
            <h2 class="text-2xl font-medium truncate mr-5">Descriptive Data Analytics</h2>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <!-- Inventory Items per Department (Total Quantity) -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 intro-y">
                <div class="intro-y box p-5 h-full">
                    <div class="flex items-center justify-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <h2 class="font-medium text-base text-center">Items in Stock per Department</h2>
                    </div>
                    <canvas id="deptQuantityBarChart" style="width: 100%; max-width: 560px; height: 340px; margin: 0 auto;"></canvas>
                </div>
            </div>
            
            <!-- Number of Items per Department (Distinct Items Count) -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 intro-y">
                <div class="intro-y box p-5 h-full">
                    <div class="flex items-center justify-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <h2 class="font-medium text-base text-center">Number of Item Types per Department</h2>
                    </div>
                    <canvas id="deptItemCountBarChart" style="width: 100%; max-width: 560px; height: 340px; margin: 0 auto;"></canvas>
                </div>
            </div>
            
            <!-- Category Distribution (Pie) -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 intro-y">
                <div class="intro-y box p-5 h-full">
                    <div class="flex items-center justify-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <h2 class="font-medium text-base text-center">Inventory Category Distribution</h2>
                    </div>
                    <!-- Legend at the top -->
                    <div class="mb-6">
                        <div id="categoryLegend" class="flex flex-wrap justify-center gap-3"></div>
                    </div>
                    <!-- Centered chart -->
                    <div class="flex justify-center items-center py-6">
                        <div style="width: 100%; max-width: 300px; height: 300px;">
                            <canvas id="categoryDistributionChart" width="400" height="300"></canvas>
                        </div>
                    </div>  
                </div>
            </div>
            
            <!-- Fund Code Distribution (Pie) -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 intro-y">
        <div class="intro-y box p-5 h-full">
            <div class="flex items-center justify-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <h2 class="font-medium text-base text-center">Fund Code Distribution</h2>
            </div>
            <canvas id="fundCodeChart" style="width: 100%; max-width: 560px; height: 340px; margin: 0 auto;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Descriptive Analytics -->
    <div class="col-span-12">
        <!-- BEGIN: General Report -->
        <div class="intro-y">
            <div class="flex items-center mt-8 mb-4">
                <h2 class="text-2xl font-medium truncate mr-5">General Report</h2>
            </div>
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="package" class="report-box__icon text-primary"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">{{ $totalSupplies }}</div>
                            <div class="text-base text-slate-500 mt-1">Total Supplies</div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="alert-triangle" class="report-box__icon text-warning"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">{{ $lowStockCount }}</div>
                            <div class="text-base text-slate-500 mt-1">Low Stock Items</div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="folder" class="report-box__icon text-success"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">{{ $totalCategories }}</div>
                            <div class="text-base text-slate-500 mt-1">Categories</div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="package-check" class="report-box__icon text-info"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">{{ $todayDeployments }}</div>
                            <div class="text-base text-slate-500 mt-1">Today's Deployments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: General Report -->

        <!-- BEGIN: Recent Deployments -->
        <div class="intro-y col-span-12 mt-8">
            <div class="flex items-center h-10 mb-5">
                <h2 class="text-2xl font-medium truncate mr-5">Recent Deployments</h2>
            </div>
            <div class="box p-5">
                <div class="overflow-x-auto">
                    <table class="table table-report">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Item</th>
                                <th class="whitespace-nowrap">Department</th>
                                <th class="text-center whitespace-nowrap">Status</th>
                                <th class="text-center whitespace-nowrap">Deployment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDeployments as $deployment)
                            <tr class="intro-x hover:bg-slate-100 cursor-pointer" onclick="window.location='{{ route('deployed-items.show', $deployment) }}'">
                                <td class="border-b dark:border-darkmode-600">
                                    <div class="font-medium whitespace-nowrap">{{ $deployment->itemName }}</div>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ $deployment->itemCategory }}</div>
                                </td>
                                <td class="border-b dark:border-darkmode-600">
                                    <div class="font-medium">
                                        {{ $deployment->department->officename ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="text-center border-b dark:border-darkmode-600">
                                    <span class="status-badge status-{{ $deployment->status }}">
                                        {{ ucfirst($deployment->status) }}
                                    </span>
                                </td>
                                <td class="text-center border-b dark:border-darkmode-600">
                                    <div class="text-slate-500 text-xs">{{ $deployment->created_at->diffForHumans() }}</div>
                                    <div class="font-medium">{{ optional($deployment->dateDeployed)->format('M d, Y') ?? 'N/A' }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center !py-4">No recent deployments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(is_a($recentDeployments, 'Illuminate\Pagination\AbstractPaginator') && $recentDeployments->hasPages())
                <div class="mt-4 flex justify-end">
                    {{ $recentDeployments->links() }}
                </div>
                @endif
            </div>
        </div>
        <!-- END: Recent Deployments -->

        <!-- BEGIN: Low Stock Alerts -->
        <div class="intro-y col-span-12 mt-8">
            <div class="flex items-center h-10 mb-5">
                <h2 class="text-2xl font-medium truncate mr-5">Low Stock Alerts</h2>
            </div>
            <div class="box p-5">
                <div class="overflow-x-auto">
                    <table class="table table-report">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Supply</th>
                                <th class="text-center whitespace-nowrap">Current Stock</th>
                                <th class="text-center whitespace-nowrap">Minimum Stock</th>
                                <th class="text-center whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $supply)
                            <tr class="intro-x">
                                <td class="w-40 !py-4">{{ $supply->name }}</td>
                                <td class="text-center !py-4">{{ $supply->quantity }}</td>
                                <td class="text-center !py-4">5</td>
                                <td class="text-center !py-4">
                                    <div class="flex items-center justify-center text-danger">
                                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-1"></i> Low Stock
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="intro-x">
                                <td colspan="4" class="text-center !py-4">No low stock items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END: Low Stock Alerts -->
    </div>
</div>
<!-- END: Main Content -->

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderCharts() {
            // Data sources
            const departments = @json($supplyAnalytics['departments']);
            const categoryNames = @json($supplyAnalytics['categoryDistribution']->pluck('name'));
            const categoryCounts = @json($supplyAnalytics['categoryDistribution']->pluck('count'));
            const supplies = @json($supplyAnalytics['supplies']);

            const departmentNames = departments.map(d => d.officename);
            const departmentQuantities = departments.map(d => (d.supplies || []).reduce((sum, s) => sum + (Number(s.quantity) || 0), 0));
            const departmentItemCounts = departments.map(d => (d.supplies || []).length);

            // Department Quantity (Bar)
            const deptQtyCtx = document.getElementById('deptQuantityBarChart').getContext('2d');
            const deptQtyData = {
                labels: departmentNames,
                datasets: [{
                    label: 'Items in Stock',
                    data: departmentQuantities,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3
                }]
            };
            const deptQtyOptions = {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            };
            new Chart(deptQtyCtx, { type: 'bar', data: deptQtyData, options: deptQtyOptions });

            // Department Item Types Bar
            const deptCntCtx = document.getElementById('deptItemCountBarChart').getContext('2d');
            new Chart(deptCntCtx, {
                type: 'bar',
                data: {
                    labels: departmentNames,
                    datasets: [{
                        label: 'Distinct Item Types',
                        data: departmentItemCounts,
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                    options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });

            // Category Distribution (Donut) with custom legend
            const catCtx = document.getElementById('categoryDistributionChart').getContext('2d');
            const categoryData = {
                labels: categoryNames,
                datasets: [{
                    data: categoryCounts,
                    backgroundColor: [
                        'rgba(100, 200, 255, 0.8)',  // Bright Sky Blue
                        'rgba(100, 255, 150, 0.8)',  // Bright Mint
                        'rgba(255, 200, 100, 0.8)',  // Peach
                        'rgba(255, 150, 200, 0.8)',  // Pink
                        'rgba(200, 150, 255, 0.8)',  // Lavender
                        'rgba(255, 255, 100, 0.8)',  // Bright Yellow
                        'rgba(100, 240, 255, 0.8)',  // Cyan
                        'rgba(255, 180, 100, 0.8)',  // Light Orange
                        'rgba(200, 255, 200, 0.8)'   // Very Light Green
                    ],
                    borderWidth: 1,
                    borderColor: '#fff',
                    hoverOffset: 8,
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(99, 102, 241, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(6, 182, 212, 1)'
                    ],
                    borderWidth: 1
                }]
            };
            // Reset the canvas size to ensure proper rendering
            const container = document.getElementById('categoryDistributionChart').parentNode;
            container.style.width = '100%';
            container.style.height = '300px';
            
            const categoryChart = new Chart(catCtx, {
                type: 'doughnut',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    onHover: (event, chartElement) => {
                        if (event.native && event.native.target) {
                            event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                        }
                    }
                }
            });
            const legendEl = document.getElementById('categoryLegend');
            if (legendEl) {
                const total = categoryCounts.reduce((a, b) => a + b, 0);
                const colors = categoryData.datasets[0].backgroundColor;
                legendEl.innerHTML = categoryNames.map((name, idx) => {
                    const count = categoryCounts[idx] || 0;
                    const pct = total > 0 ? Math.round((count / total) * 100) : 0;
                    const color = colors[idx % colors.length];
                    return `<div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded" style="background:${color}"></span>
                            <span class="text-slate-700 dark:text-slate-200">${name}</span>
                        </div>
                        <span class="text-slate-600 dark:text-slate-400">${count} • ${pct}%</span>
                    </div>`;
                }).join('');
            }

            // Fund Code Distribution Column (vertical bar) with custom legend
            const fundCtx = document.getElementById('fundCodeChart').getContext('2d');
            const fundMap = new Map();
            (supplies || []).forEach(s => {
                const key = s.fund_code || 'Unspecified';
                fundMap.set(key, (fundMap.get(key) || 0) + 1);
            });
            const fundLabels = Array.from(fundMap.keys());
            const fundCounts = Array.from(fundMap.values());
            new Chart(fundCtx, {
        type: 'bar',
        data: {
                    labels: fundLabels,
            datasets: [{
                        data: fundCounts,
                        backgroundColor: 'rgba(14, 165, 233, 0.6)',
                        borderColor: 'rgba(14, 165, 233, 1)',
                        borderWidth: 1,
                        label: 'Fund Code Count'
            }]
        },
        options: {
            responsive: true,
                    plugins: { legend: { display: false } },
            scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Count' } },
                        x: { title: { display: true, text: 'Fund Code' } }
                    }
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', renderCharts);
        } else {
            renderCharts();
        }
</script>
@endpush

@endsection