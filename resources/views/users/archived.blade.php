@extends('layouts.app')

@section('title', 'Archived Users')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col sm:flex-row items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mr-auto">Archived Users</h2>
        <div class="flex mt-4 sm:mt-0">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary shadow-md">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if($users->isEmpty())
            <div class="text-center py-8">
                <i data-lucide="archive" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No archived users</h3>
                <p class="text-gray-500 mb-4">All users are currently active.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Archived Date</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3 text-center">
                                    <div class="font-medium">{{ $user->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-gray-600">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($user->role === 'Inventory Manager')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Inventory Manager
                                        </span>
                                    @elseif($user->role === 'Inspector')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Inspector
                                        </span>
                                    @elseif($user->role === 'Department User')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Department User
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $user->role ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-xs text-gray-500">
                                        {{ optional($user->deleted_at)->format('M d, Y') }}
                                        <div class="text-gray-400">{{ $user->deleted_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center space-x-3">
                                        <form action="{{ route('users.restore', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    onclick="confirmRestoreUser('{{ $user->name }}', this.form)" 
                                                    class="btn btn-sm btn-success w-8 h-8 flex items-center justify-center p-0 mx-2"
                                                    title="Restore User">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('users.force-delete', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmForceDeleteUser('{{ $user->name }}', this.form)" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0 mx-2"
                                                    style="background-color: #dc2626; border-color: #dc2626; color: white;"
                                                    title="Permanently Delete User">
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
            @if($users->hasPages())
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmRestoreUser(name, form) {
    Swal.fire({
        title: 'Restore User',
        html: `
            <div class="text-center py-2">
                <i data-lucide="rotate-ccw" class="w-12 h-12 mx-auto text-green-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Restore user <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This user will be reactivated and can log in again.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Restore',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-sm btn-success px-3 py-1 text-xs',
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
                    confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Restoring...';
                    confirmButton.disabled = true;
                }
                form.submit();
            });
        }
    });
}

function confirmForceDeleteUser(name, form) {
    Swal.fire({
        title: 'Permanently Delete User',
        html: `
            <div class="text-center py-2">
                <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto text-red-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Permanently delete user <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This action cannot be undone. All user data will be lost permanently.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Delete Permanently',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-sm btn-danger px-3 py-1 text-xs',
            cancelButton: 'btn btn-sm btn-outline-secondary px-3 py-1 text-xs mr-2',
            popup: 'text-sm',
            actions: 'mt-3'
        },
        buttonsStyling: false,
        width: '20rem',
        padding: '1rem'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

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
