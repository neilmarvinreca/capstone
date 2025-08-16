@extends('layouts.app')

@section('title', 'Deployed Items Overview')

@push('styles')
<link rel="icon" href="{{ asset('dist/images/logodssc.png') }}">
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
@endpush

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8 mb-8">
    <h2 class="text-lg font-medium mr-auto">Deployed Items Overview</h2>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="package-check" class="report-box__icon text-primary"></i>
                    <div class="ml-auto">
                        <div class="report-box__indicator bg-success">
                            <i data-lucide="trending-up" class="w-4 h-4 ml-0.5"></i>
                        </div>
                    </div>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">{{ number_format($totalDeployed) }}</div>
                <div class="text-base text-slate-500 mt-1">Total Items Deployed</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="peso-sign" class="report-box__icon text-success text-3xl">₱</i>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">₱{{ number_format($totalValue, 2) }}</div>
                <div class="text-base text-slate-500 mt-1">Total Value Deployed</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="building-2" class="report-box__icon text-warning"></i>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">{{ count($byDepartment) }}</div>
                <div class="text-base text-slate-500 mt-1">Departments</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="activity" class="report-box__icon text-pending"></i>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">
                    {{ number_format($deployedItems->where('status', 'active')->count()) }}
                </div>
                <div class="text-base text-slate-500 mt-1">Active Items</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="intro-y box p-5 mt-5">
    <div class="flex flex-col sm:flex-row sm:items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
        <div class="mr-auto">
            <h2 class="font-medium text-base">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @else
                    All Deployed Items
                @endif
            </h2>
            @if(request('search'))
                <div class="text-sm text-slate-500 mt-1">
                    Found {{ $deployedItems->total() }} item{{ $deployedItems->total() != 1 ? 's' : '' }}
                </div>
            @endif
        </div>
        <div class="flex items-center mt-3 sm:mt-0 space-x-2">
            <form method="GET" action="{{ route('deployed-items.index') }}" class="flex items-center">
                <div class="relative w-56">
                    <input type="text" 
                           name="search" 
                           class="form-control w-56 pr-10" 
                           placeholder="Supply name" 
                           value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('deployed-items.index') }}" 
                           class="absolute inset-y-0 right-0 flex items-center mr-3 text-slate-500 hover:text-danger"
                           title="Clear search">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary ml-2 mx-1">
                    <i data-lucide="search" class="w-4 h-4 mr-1"></i> Search
                </button>
            </form>

            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <a href="{{ route('deployed-items.archived') }}" 
                   class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <i data-lucide="archive" class="w-4 h-4 mr-2"></i> View Archived
                </a>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="table table-report table-auto">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Item Details</th>
                    <th class="whitespace-nowrap">Department User</th>
                    <th class="text-center whitespace-nowrap">Date Deployed</th>
                    <th class="text-center whitespace-nowrap">Quantity</th>
                    <th class="text-center whitespace-nowrap">Cost</th>
                    <th class="text-center whitespace-nowrap">Status</th>
                    <th class="text-center whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deployedItems as $item)
                    <!-- Debug: Item ID: {{ $item->id }}, DeployedID: {{ $item->deployedID }} -->
                    <tr class="intro-x">
                        <td>
                            <a href="{{ route('deployed-items.show', $item) }}" class="font-medium">{{ $item->itemName }}</a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                {{ $item->itemCategory }}
                                @if($item->itemDescription)
                                    • {{ Str::limit($item->itemDescription, 50) }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $item->department->user->name ?? 'No Accountable Person' }}</div>
                            <div class="text-slate-500 text-xs">{{ $item->department->officename ?? 'N/A' }} ({{ $item->department->departmentID ?? '' }})</div>
                        </td>
                        <td class="text-center">{{ optional($item->dateDeployed)->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">₱{{ number_format($item->cost, 2) }}</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('deployed-items.edit', $item) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <form id="archive-form-{{ $item->deployedID }}" 
                                      action="{{ route('deployed-items.archive', $item->deployedID) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to archive this item?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="btn btn-sm w-8 h-8 flex items-center justify-center p-0 mx-1" 
                                            style="background-color: #f59e0b; border-color: #f59e0b; color: white;"
                                            title="Archive">
                                        <i data-lucide="archive" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No deployed items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($deployedItems->hasPages())
            <div class="mt-5">
                {{ $deployedItems->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Recent Deployments -->
<div class="intro-y box p-5 mt-5">
    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
        <h2 class="font-medium text-base mr-auto">Recent Deployments</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="table table-report">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Item Name</th>
                    <th class="whitespace-nowrap">Department</th>
                    <th class="text-center whitespace-nowrap">Date Deployed</th>
                    <th class="text-center whitespace-nowrap">Quantity</th>
                    <th class="text-center whitespace-nowrap">Status</th>
                    <th class="text-center whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDeployments as $item)
                    <tr class="intro-x">
                        <td>
                            <a href="{{ route('deployed-items.show', $item) }}" class="font-medium">{{ $item->itemName }}</a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                {{ $item->itemCategory }}
                                @if($item->itemDescription)
                                    • {{ Str::limit($item->itemDescription, 30) }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $item->department->user->name ?? 'No Accountable Person' }}</div>
                            <div class="text-slate-500 text-xs">{{ $item->department->officename ?? 'N/A' }} ({{ $item->department->departmentID ?? '' }})</div>
                        </td>
                        <td class="text-center">{{ optional($item->dateDeployed)->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('deployed-items.edit', $item) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No recent deployments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function confirmArchive(id, name) {
    Swal.fire({
        title: 'Archive Item',
        html: `
            <div class="text-center py-2">
                <i data-lucide="archive" class="w-12 h-12 mx-auto text-yellow-500 mb-4"></i>
                <p class="text-sm text-gray-600">
                    Archive <span class="font-semibold">${name}</span>?
                    <br>
                    This item will be moved to the archive.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Archive',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-sm btn-warning px-4 py-1 text-xs mx-2',
            cancelButton: 'btn btn-sm btn-outline-secondary px-4 py-1 text-xs',
            popup: 'text-sm',
            actions: 'mt-3 flex-row-reverse justify-start'
        },
        buttonsStyling: false,
        width: '20rem',
        padding: '1rem',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                const confirmButton = document.querySelector('.swal2-confirm');
                if (confirmButton) {
                    confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Archiving...';
                    confirmButton.disabled = true;
                }
                document.getElementById(`archive-form-${id}`).submit();
            });
        }
    });
}
    // Add any necessary JavaScript here
    function showQRCode(qrData) {
        const target = document.getElementById('qrcode');
        if (!target || typeof QRCode === 'undefined') return;
        target.innerHTML = '';
        new QRCode(target, {
            text: qrData,
            width: 128,
            height: 128,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }
</script>
@endpush

@endsection
