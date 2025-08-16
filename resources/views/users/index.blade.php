@extends('layouts.app')

@section('title', 'Users')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col sm:flex-row items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mr-auto">Users Management</h2>
        <div class="flex mt-4 sm:mt-0">
            <a href="{{ route('users.archived') }}" class="btn btn-primary shadow-md mr-2">
                <i data-lucide="archive" class="w-4 h-4 mr-2"></i> View Archived
            </a>
            <a href="{{ route('register') }}" class="btn btn-primary shadow-md">
                <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Register User
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if($users->isEmpty())
            <div class="text-center py-8">
                <i data-lucide="users" class="w-16 h-16 mx-auto text-gray-400"></i>
                <h3 class="mt-2 text-lg font-medium">No users yet</h3>
                <p class="text-gray-500 mb-4">Get started by registering a new user.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-report w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
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
                                    <div class="flex justify-center space-x-3">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('users.archive', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" 
                                                    onclick="confirmArchiveUser('{{ $user->name }}', this.form)" 
                                                    class="btn btn-sm w-8 h-8 flex items-center justify-center p-0 mx-2"
                                                    style="background-color: #f59e0b; border-color: #f59e0b; color: white;"
                                                    title="Archive User">
                                                <i data-lucide="archive" class="w-4 h-4"></i>
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
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmArchiveUser(name, form) {
    Swal.fire({
        title: 'Archive User',
        html: `
            <div class="text-center py-2">
                <i data-lucide="archive" class="w-12 h-12 mx-auto text-yellow-500 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">
                    Archive user <span class="font-medium">${name}</span>?
                </p>
                <p class="text-xs text-gray-500">
                    This user will be moved to archived users.
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
