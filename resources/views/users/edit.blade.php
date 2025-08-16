@extends('layouts.app')

@section('title', 'Edit User')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Edit User</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Users
        </a>
    </div>
</div>

<div class="intro-y box p-5 mt-5">
    <form method="POST" action="{{ route('users.update', $user) }}" class="grid grid-cols-12 gap-6">
        @csrf
        @method('PUT')
        
        <!-- Name -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" 
                       class="form-control w-full @error('name') border-danger @enderror" 
                       value="{{ old('name', $user->name) }}" 
                       required aria-required="true">
                @error('name')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Email -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" 
                       class="form-control w-full @error('email') border-danger @enderror" 
                       value="{{ old('email', $user->email) }}" 
                       required aria-required="true">
                @error('email')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Role -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                <select id="role" name="role" 
                        class="form-select w-full @error('role') border-danger @enderror" 
                        required aria-required="true">
                    <option value="">Select Role</option>
                    <option value="Inventory Manager" {{ old('role', $user->role) == 'Inventory Manager' ? 'selected' : '' }}>Inventory Manager</option>
                    <option value="Inspector" {{ old('role', $user->role) == 'Inspector' ? 'selected' : '' }}>Inspector</option>
                    <option value="Department User" {{ old('role', $user->role) == 'Department User' ? 'selected' : '' }}>Department User</option>
                </select>
                @error('role')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 flex items-center justify-center sm:justify-end mt-5">
            <button type="submit" class="btn btn-primary w-24 mr-2">Update</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary w-24">Cancel</a>
        </div>
    </form>
</div>
@endsection
