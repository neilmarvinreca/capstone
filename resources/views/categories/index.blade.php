@extends('layouts.app')

@section('title', 'Categories')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">



@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex flex-col sm:flex-row items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mr-auto">Category Management</h2>
        <div class="flex mt-4 sm:mt-0">
            <form action="{{ route('categories.index') }}" method="GET" class="flex items-center">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search categories..."
                    class="form-control w-64 search-box h-10 px-3 border border-gray-300 rounded-md"
                >
                @if(request('search'))
                    <a href="{{ route('categories.index') }}"
                       class="btn btn-secondary ml-2 h-10 px-3 flex items-center justify-center rounded-md bg-gray-200 hover:bg-gray-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
                <button type="submit"
                        class="btn btn-primary ml-2 h-10 px-3 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
            <a href="{{ route('categories.create') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-green-600 text-white hover:bg-green-700">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add New Category
            </a>
            <a href="{{ route('categories.archived') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <i data-lucide="archive" class="w-4 h-4 mr-2"></i> View Archived
            </a>
        </div>
    </div>


    <div class="bg-white shadow rounded-lg p-6">
        @if($categories->isEmpty() && !request('search'))
            <div class="text-center py-8">
                <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No categories yet</h3>
                <p class="text-gray-500 mb-4">Get started by creating a new category.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">ID</th>
                            <th class="min-w-[250px] text-center">Category Name</th>
                            <th class="text-center">Description</th>
                            <th class="w-24 text-center">Supplies</th>
                            <th class="w-32 text-center">Created</th>
                            <th class="w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
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
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        {{ $category->supplies_count }} {{ Str::plural('item', $category->supplies_count) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="text-xs text-gray-500">
                                        {{ optional($category->created_at)->format('M d, Y') }}
                                        <div class="text-gray-400">{{ $category->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center space-x-3">
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-info w-8 h-8 flex items-center justify-center p-0 mx-2" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('categories.archive', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    data-category-name="{{ $category->categoryName }}"
                                                    data-supplies-count="{{ $category->supplies_count }}"
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0 mx-2 archive-btn"
                                                    style="background-color: #f59e0b; border-color: #f59e0b; color: white;"
                                                    title="Archive Category">
                                                <i data-lucide="archive" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <i data-lucide="search-x" class="w-12 h-12 mx-auto text-gray-400"></i>
                                    <p class="mt-2 text-gray-500">No categories found matching your search.</p>
                                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mt-4">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Reset Search
                                    </a>
                                </td>
                            </tr>
                        @endforelse
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
// Debug: Check if SweetAlert2 is loaded
console.log('SweetAlert2 loaded:', typeof Swal !== 'undefined' ? 'Yes' : 'No');

// Add event listener to all archive buttons when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log when the DOM is fully loaded
    console.log('DOM fully loaded');
    
    // Add click event listener to all archive forms
    document.querySelectorAll('form[action*="/archive"] button[type="button"]').forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Archive button clicked');
            const form = this.closest('form');
            const categoryName = this.getAttribute('data-category-name');
            const suppliesCount = parseInt(this.getAttribute('data-supplies-count') || '0');
            console.log('Button data:', { categoryName, suppliesCount, form });
            
            // Call confirmArchive with the correct parameters
            confirmArchive(categoryName, form, suppliesCount);
        });
    });
});
function confirmArchive(name, form, suppliesCount) {
    console.log('confirmArchive called with:', { name, form, suppliesCount });
    if (suppliesCount > 0) {
        Swal.fire({
            title: 'Category in Use',
            html: `
                <div class="text-center py-2">
                    <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto text-yellow-500 mb-2"></i>
                    <p class="text-sm text-gray-600 mb-1">
                        Cannot archive <span class="font-medium">${name}</span>.
                    </p>
                    <p class="text-xs text-gray-500">
                        ${suppliesCount} ${suppliesCount === 1 ? 'supply' : 'supplies'} associated.
                    </p>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Got it',
            customClass: {
                confirmButton: 'btn btn-sm btn-warning px-3 py-1 text-xs',
                popup: 'text-sm'
            },
            buttonsStyling: false,
            width: '20rem',
            padding: '1rem'
        });
        return;
    }
    
    Swal.fire({
        title: 'Archive Category',
        html: `
            <div class="text-center py-2">
                <i data-lucide="archive" class="w-10 h-10 mx-auto text-blue-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Archive <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This category will be moved to the archive.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Archive',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-sm btn-warning px-4 py-1 text-xs',
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
                    confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Archiving...';
                    confirmButton.disabled = true;
                }
                form.submit();
            });
        }
    });
}

// Archive function remains - delete functionality is handled in archived.blade.php

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
</script>
@endpush
