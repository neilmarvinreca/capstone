@extends('layouts.app')

@section('title', 'Archived Categories')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 mb-6">
        <h2 class="text-lg font-medium mr-auto">Archived Categories</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary shadow-md mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if($categories->isEmpty())
            <div class="text-center py-8">
                <i data-lucide="archive-x" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No archived categories</h3>
                <p class="text-gray-500">There are no archived categories to display.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">ID</th>
                            <th class="min-w-[250px] text-center">Category Name</th>
                            <th class="text-center">Description</th>
                            <th class="w-32 text-center">Archived Date</th>
                            <th class="w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td class="text-center">{{ $category->categoryID ?? $category->id }}</td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="font-medium">
                                        {{ $category->categoryName }}
                                    </div>
                                </td>
                                <td class="max-w-sm text-center">
                                    <div class="text-gray-600 text-sm line-clamp-2">
                                        {{ $category->description ?? 'No description' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-xs text-gray-500">
                                        {{ $category->deleted_at->format('M d, Y') }}
                                        <div class="text-gray-400">{{ $category->deleted_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('categories.restore', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button"
                                                    onclick="confirmRestore('{{ $category->categoryName }}', this.form)" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0" 
                                                    style="background-color: #10b981; border-color: #10b981; color: white;"
                                                    title="Restore Category">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('categories.force-delete', $category) }}" method="POST" class="inline mx-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmPermanentDelete('{{ $category->categoryName }}', this.form, {{ $category->supplies_count ?? 0 }})" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0" 
                                                    style="background-color: #800000; border-color: #800000; color: white;">
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
            @if($categories->hasPages())
                <div class="mt-5">
                    {{ $categories->withQueryString()->links() }}
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
        title: 'Restore Category',
        html: `
            <div class="text-center py-2">
                <i data-lucide="rotate-ccw" class="w-10 h-10 mx-auto text-green-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Restore <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This category will be moved back to active categories.
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

function confirmPermanentDelete(name, form, suppliesCount) {
    if (suppliesCount > 0) {
        Swal.fire({
            title: 'Cannot Delete Category',
            html: `
                <div class="text-center py-2">
                    <i data-lucide="alert-triangle" class="w-10 h-10 mx-auto text-yellow-500 mb-2"></i>
                    <p class="text-sm text-gray-600 mb-1">
                        Category in Use
                    </p>
                    <p class="text-xs text-gray-500">
                        Cannot delete <span class="font-medium">${name}</span> because it has ${suppliesCount} 
                        ${suppliesCount === 1 ? 'supply' : 'supplies'} associated with it.
                    </p>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'I Understand',
            customClass: {
                confirmButton: 'btn btn-sm btn-primary px-4 py-1 text-xs',
                popup: 'text-sm',
                actions: 'mt-3'
            },
            buttonsStyling: false,
            width: '20rem',
            padding: '1rem'
        });
    } else {
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
}

// Initialize Lucide icons after the page loads
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endpush
