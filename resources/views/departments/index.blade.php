@extends('layouts.app')

@section('title', 'Departments')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex flex-col sm:flex-row items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mr-auto">Department Management</h2>
        <div class="flex mt-4 sm:mt-0">
            <form action="{{ route('departments.index') }}" method="GET" class="flex items-center">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search departments..."
                    class="form-control w-64 search-box h-10 px-3 border border-gray-300 rounded-md"
                >
                @if(request('search'))
                    <a href="{{ route('departments.index') }}"
                       class="btn btn-secondary ml-2 h-10 px-3 flex items-center justify-center rounded-md bg-gray-200 hover:bg-gray-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
                <button type="submit"
                        class="btn btn-primary ml-2 h-10 px-3 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
            <a href="{{ route('departments.create') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-green-600 text-white hover:bg-green-700">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Department
            </a>
            <a href="{{ route('departments.archived') }}"
               class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                <i data-lucide="archive" class="w-4 h-4 mr-2"></i> View Archived
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if($departments->isEmpty() && !request('search'))
            <div class="text-center py-8">
                <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No departments yet</h3>
                <p class="text-gray-500 mb-4">Get started by creating a new department.</p>
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Department
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">ID</th>
                            <th class="min-w-[200px] text-center">Department</th>
                            <th class="text-center">Location</th>
                            <th class="text-center">Accountable Person</th>
                            <th class="w-24 text-center">Supplies</th>
                            <th class="w-32 text-center">Created</th>
                            <th class="w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td class="text-center">{{ $department->departmentID }}</td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="font-medium">
                                        <a href="{{ route('departments.show', $department) }}" class="text-primary hover:underline">
                                            {{ $department->officename }}
                                        </a>
                                    </div>
                                    <div class="text-gray-600 text-sm mt-0.5">
                                        {{ $department->description ?? 'No description' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="text-sm">{{ $department->locationcode }}</span>
                                </td>
                                <td class="text-center">
                                    @if($department->user)
                                        <div class="flex items-center justify-center">
                                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-2">
                                                {{ substr($department->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm">{{ $department->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $department->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Not assigned</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        {{ $department->supplies_count }} {{ Str::plural('item', $department->supplies_count) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="text-xs text-gray-500">
                                        {{ optional($department->created_at)->format('M d, Y') }}
                                        <div class="text-gray-400">{{ $department->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('departments.edit', $department) }}" 
                                           class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2" 
                                           title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('departments.show', $department) }}" 
                                           class="btn btn-sm btn-info w-8 h-8 flex items-center justify-center p-0 mx-2 " 
                                           title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <button type="button" 
                                                onclick="confirmArchive('{{ $department->departmentID }}', '{{ addslashes($department->officename) }}')" 
                                                class="btn btn-sm w-8 h-8 flex items-center justify-center p-0 mx-2" 
                                                style="background-color: #f59e0b; border-color: #f59e0b; color: white;"
                                                title="Archive">
                                            <i data-lucide="archive" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <form id="archive-form-{{ $department->departmentID }}" 
                                          action="{{ route('departments.archive', $department) }}" 
                                          method="POST" 
                                          class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <i data-lucide="search-x" class="w-12 h-12 mx-auto text-gray-400"></i>
                                <p class="mt-2 text-gray-500">No departments found matching your search.</p>
                                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary mt-4">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Reset Search
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($departments->hasPages())
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
                {{ $departments->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

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
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tooltip]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Archive confirmation
function confirmArchive(id, name) {
    const form = document.getElementById(`archive-form-${id}`);
    
    Swal.fire({
        title: 'Archive Department',
        html: `
            <div class="text-center py-2">
                <i data-lucide="archive" class="w-10 h-10 mx-auto text-blue-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Archive <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This department will be moved to the archive.
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

// Delete confirmation (kept for reference but not used in this view)
function confirmDelete(id, name) {
    const form = document.getElementById(`delete-form-${id}`);
    
    Swal.fire({
        title: 'Delete Department',
        html: `
            <div class="text-center py-2">
                <i data-lucide="alert-triangle" class="w-10 h-10 mx-auto text-red-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Permanently delete <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This action cannot be undone and all associated data will be lost!
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
@endsection