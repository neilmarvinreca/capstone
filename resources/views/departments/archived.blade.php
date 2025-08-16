@extends('layouts.app')

@section('title', 'Archived Departments')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 mb-6">
        <h2 class="text-lg font-medium mr-auto">Archived Departments</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <a href="{{ route('departments.index') }}" class="btn btn-secondary shadow-md mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Departments
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if($departments->isEmpty())
            <div class="text-center py-8">
                <i data-lucide="archive-x" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No archived departments</h3>
                <p class="text-gray-500">There are no archived departments to display.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">ID</th>
                            <th class="min-w-[200px] text-center">Office Name</th>
                            <th class="text-center">Location Code</th>
                            <th class="text-center">Contact Person</th>
                            <th class="text-center">Archived Date</th>
                            <th class="w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                            <tr>
                                <td class="text-center">{{ $department->departmentID }}</td>
                                <td class="whitespace-nowrap text-center">
                                    <div class="font-medium">
                                        {{ $department->officename }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        {{ $department->locationcode }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="text-gray-600 text-sm">
                                        {{ $department->contactperson }}
                                        @if($department->contactnumber)
                                            <div class="text-gray-500">{{ $department->contactnumber }}</div>
                                        @endif
                                        @if($department->email)
                                            <div class="text-blue-600">{{ $department->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-xs text-gray-500">
                                        {{ $department->deleted_at->format('M d, Y') }}
                                        <div class="text-gray-400">{{ $department->deleted_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('departments.restore', $department) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button"
                                                    onclick="confirmRestore('{{ $department->officename }}', this.form)" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0" 
                                                    style="background-color: #10b981; border-color: #10b981; color: white;"
                                                    title="Restore Department">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('departments.force-delete', $department) }}" method="POST" class="inline mx-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmPermanentDelete('{{ $department->officename }}', this.form)" 
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
            
            @if($departments->hasPages())
                <div class="mt-5">
                    {{ $departments->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal" id="restore-confirmation-modal">
    <div class="modal__content">
        <div class="p-5 text-center">
            <i data-lucide="refresh-ccw" class="w-16 h-16 text-warning mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-slate-500 mt-2">
                You are about to restore <span id="restore-department-name" class="font-medium"></span>.
                This department will be returned to the active departments list.
            </div>
        </div>
        <form id="restore-department-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="px-5 pb-8 text-center">
                <button type="button" class="btn btn-outline-secondary w-24 mr-1" data-tw-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-warning w-24">Restore</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="delete-confirmation-modal">
    <div class="modal__content">
        <div class="p-5 text-center">
            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-slate-500 mt-2">
                You are about to permanently delete <span id="delete-department-name" class="font-medium"></span>.
                This action cannot be undone and all associated data will be lost.
            </div>
        </div>
        <form id="delete-department-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="px-5 pb-8 text-center">
                <button type="button" class="btn btn-outline-secondary w-24 mr-1" data-tw-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger w-24">Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Restore department confirmation
    function confirmRestore(departmentName, form) {
        Swal.fire({
            title: 'Restore Department',
            html: `
                <div class="text-center py-2">
                    <i data-lucide="refresh-ccw" class="w-12 h-12 mx-auto text-green-500 mb-4"></i>
                    <p class="text-sm text-gray-600">
                        Restore <span class="font-semibold">${departmentName}</span>?
                        <br>
                        This department will be returned to the active departments list.
                    </p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Restore',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-sm btn-success px-4 py-1 text-xs mx-2',
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
                        confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Restoring...';
                        confirmButton.disabled = true;
                    }
                    form.submit();
                });
            }
        });
    }

    // Permanently delete department confirmation
    function confirmPermanentDelete(departmentName, form) {
        Swal.fire({
            title: 'Permanently Delete',
            html: `
                <div class="text-center py-2">
                    <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto text-red-500 mb-4"></i>
                    <p class="text-sm text-gray-600">
                        Delete <span class="font-semibold">${departmentName}</span> permanently?
                        <br>
                        This action cannot be undone and all associated data will be lost.
                    </p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-sm btn-danger px-4 py-1 text-xs mx-2',
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
