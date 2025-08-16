@extends('layouts.app')

@section('title', 'Supplies')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col sm:flex-row items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mr-auto">Supplies Management</h2>
        <div class="flex mt-4 sm:mt-0">
            <form action="{{ route('supplies.index') }}" method="GET" class="flex items-center">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search supplies..."
                    class="form-control w-64 search-box h-10 px-3 border border-gray-300 rounded-md"
                >
                @if(request('search'))
                    <a href="{{ route('supplies.index') }}"
                       class="btn btn-secondary ml-2 h-10 px-3 flex items-center justify-center rounded-md bg-gray-200 hover:bg-gray-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
                <button type="submit"
                        class="btn btn-primary ml-2 h-10 px-3 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
            <a href="{{ route('supplies.deploy') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <i data-lucide="truck" class="w-4 h-4 mr-2"></i> Deploy Items
            </a>
            <a href="{{ route('supplies.create') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-green-600 text-white hover:bg-green-700">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add New Supply
            </a>
            <a href="{{ route('supplies.archived') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <i data-lucide="archive" class="w-4 h-4 mr-2"></i> View Archived
            </a>
        </div>
    </div>


    <div class="bg-white shadow rounded-lg p-6">
        @if($supplies->isEmpty() && !request('search') && !request('department_id'))
            <div class="text-center py-8">
                <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No supplies yet</h3>
                <p class="text-gray-500 mb-4">Get started by adding a new supply.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">ID</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[180px]">Item Name</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">Description</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Date Acquired</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Est. Life</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Unit Cost</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Qty</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Amount</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Category</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Fund Code</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">PPE Sub Account</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">GL Code</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Added By</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplies as $supply)
                            <tr>
                            <td class="text-center">#{{ str_pad($supply->itemID, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="font-medium">
                                        <a href="{{ route('supplies.show', $supply) }}" class="text-primary hover:underline">
                                            {{ $supply->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="max-w-sm text-center">
                                    <div class="text-gray-600 text-sm line-clamp-2">
                                        {{ $supply->description ?? 'No description' }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="text-sm">{{ optional($supply->acquired_at)->format('M d, Y') ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $supply->estimated_life ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="text-sm">₱{{ number_format($supply->unit_cost, 2) }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        {{ $supply->quantity }}
                                    </span>
                                </td>
                                <td class="font-medium text-center">
                                    ₱{{ number_format($supply->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($supply->category)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $supply->category->categoryName }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-sm text-gray-600">{{ $supply->fund_code ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-sm text-gray-600">{{ $supply->pp_sub_account ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-sm text-gray-600">{{ $supply->gl_code ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-sm text-gray-600">{{ $supply->addedBy->name ?? 'System' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($supply->quantity <= ($supply->minimum_stock ?? 5))
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            In Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('supplies.show', $supply) }}" class="btn btn-sm btn-info w-8 h-8 flex items-center justify-center p-0 mx-2" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('supplies.archive', $supply) }}" method="POST" class="inline">
                                            @csrf   
                                            @method('PUT')
                                            <button type="button" 
                                                    onclick="confirmArchive('{{ $supply->name }}', this.form)" 
                                                    class="btn btn-sm btn-warning w-8 h-8 flex items-center justify-center p-0 mx-2" 
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
                                <td colspan="15" class="text-center py-8">
                                    <i data-lucide="search-x" class="w-12 h-12 mx-auto text-gray-400"></i>
                                    <p class="mt-2 text-gray-500">No supplies found matching your search.</p>
                                    <a href="{{ route('supplies.index') }}" class="btn btn-outline-secondary mt-4">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Reset Search
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($supplies->hasPages())
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $supplies->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle form submission response
@if(session('success'))
    Swal.fire({
        title: 'Success',
        html: `
            <div class="text-center py-2">
                <i data-lucide="check-circle" class="w-10 h-10 mx-auto text-green-500 mb-2"></i>
                <p class="text-sm text-gray-600">
                    {{ session('success') }}
                </p>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-sm btn-warning px-4 py-1 text-xs',
            popup: 'text-sm',
            actions: 'mt-3'
        },
        buttonsStyling: false,
        width: '20rem',
        padding: '1rem'
    });
@elseif(session('error'))
    Swal.fire({
        title: 'Error',
        html: `
            <div class="text-center py-2">
                <i data-lucide="x-circle" class="w-10 h-10 mx-auto text-red-500 mb-2"></i>
                <p class="text-sm text-gray-600">
                    {{ session('error') }}
                </p>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-sm btn-warning px-4 py-1 text-xs',
            popup: 'text-sm',
            actions: 'mt-3'
        },
        buttonsStyling: false,
        width: '20rem',
        padding: '1rem'
    });
@endif
    function confirmArchive(name, form) {
        Swal.fire({
            title: 'Archive Supply',
            html: `
                <div class="text-center py-2">
                    <i data-lucide="archive" class="w-12 h-12 mx-auto text-yellow-500 mb-2"></i>
                    <p class="text-sm text-gray-600 mb-1">
                        Archive <span class="font-medium">${name}</span>?
                    </p>
                    <p class="text-xs text-gray-500">
                        This supply will be moved to archived supplies.
                    </p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Archive',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-sm btn-warning px-3 py-1 text-xs',
                cancelButton: 'btn btn-sm btn-outline-secondary px-3 py-1 text-xs mr-2',
                popup: 'text-sm',
                actions: 'mt-3'
            },
            buttonsStyling: false,
            width: '20rem',
            padding: '1rem',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    const confirmButton = document.querySelector('.swal2-confirm');
                    if (confirmButton) {
                        confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Archiving...';
                        confirmButton.disabled = true;
                    }
                    form.submit();
                });
            }
        });
    }
</script>
@endpush
