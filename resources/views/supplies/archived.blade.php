@extends('layouts.app')

@section('title', 'Archived Supplies')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 mb-6">
        <h2 class="text-lg font-medium mr-auto">Archived Supplies</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <a href="{{ route('supplies.index') }}" class="btn btn-secondary shadow-md mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Supplies
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if($supplies->isEmpty())
            <div class="text-center py-8">
                <i data-lucide="archive-x" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No archived supplies</h3>
                <p class="text-gray-500">There are no archived supplies to display.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">ID</th>
                            <th class="min-w-[200px] text-center">Item Name</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Status</th>
                            <th class="w-32 text-center">Archived Date</th>
                            <th class="w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplies as $supply)
                            <tr>
                                <td class="text-center">#{{ str_pad($supply->itemID, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="font-medium">
                                        {{ $supply->name }}
                                    </div>
                                </td>
                                <td class="max-w-sm text-center">
                                    <div class="text-gray-600 text-sm line-clamp-2">
                                        {{ $supply->description ?? 'No description' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-sm text-gray-600">
                                        {{ $supply->category->categoryName ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-sm text-gray-600">
                                        {{ $supply->quantity }} {{ $supply->unit ?? 'pcs' }}
                                    </div>
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
                                    <div class="text-xs text-gray-500">
                                        {{ $supply->deleted_at->format('M d, Y') }}
                                        <div class="text-gray-400">{{ $supply->deleted_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('supplies.restore', $supply) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button"
                                                    onclick="confirmRestore('{{ $supply->name }}', this.form)" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0" 
                                                    style="background-color: #10b981; border-color: #10b981; color: white;"
                                                    title="Restore Supply">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('supplies.force-delete', $supply) }}" method="POST" class="inline mx-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmPermanentDelete('{{ $supply->name }}', this.form)" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0" 
                                                    style="background-color: #800000; border-color: #800000; color: white;"
                                                    title="Permanently Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($supplies->hasPages())
                <div class="mt-5">
                    {{ $supplies->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmRestore(name, form) {
    Swal.fire({
        title: 'Restore Supply',
        html: `
            <div class="text-center py-2">
                <i data-lucide="rotate-ccw" class="w-10 h-10 mx-auto text-green-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Restore <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This supply will be moved back to active supplies.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Restore',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-sm btn-success px-4 py-1 text-xs',
            cancelButton: 'btn btn-sm btn-outline-secondary px-4 py-1 text-xs mr-2',
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
                    confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Restoring...';
                    confirmButton.disabled = true;
                }
                form.submit();
            });
        }
    });
}

function confirmPermanentDelete(name, form) {
    Swal.fire({
        title: 'Permanently Delete',
        html: `
            <div class="text-center py-2">
                <i data-lucide="trash-2" class="w-10 h-10 mx-auto text-red-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Delete <span class="font-medium">${name}</span> permanently?
                </p>
                <p class="text-xs text-gray-500">
                    This action cannot be undone.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-sm btn-danger px-4 py-1 text-xs',
            cancelButton: 'btn btn-sm btn-outline-secondary px-4 py-1 text-xs mr-2',
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
                    confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Deleting...';
                    confirmButton.disabled = true;
                }
                form.submit();
            });
        }
    });
}
</script>
@endpush
