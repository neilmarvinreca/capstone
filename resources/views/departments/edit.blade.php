@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Edit Department</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('departments.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Departments
        </a>
    </div>
</div>

<div class="intro-y box p-5 mt-5">
    <form method="POST" action="{{ route('departments.update', $department) }}" class="grid grid-cols-12 gap-6">
        @csrf
        @method('PUT')
        <div class="col-span-12">
            <div class="input-form">
                <label for="departmentID" class="form-label">Department ID</label>
                <input type="text" id="departmentID" name="departmentID" class="form-control w-full bg-gray-100" value="{{ $department->departmentID }}" readonly>
                <div class="text-xs text-gray-500 mt-1">
                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                    Department ID cannot be changed once created.
                </div>
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="locationcode" class="form-label">Location Code <span class="text-danger">*</span></label>
                <input type="text" id="locationcode" name="locationcode" class="form-control w-full @error('locationcode') border-danger @enderror" value="{{ old('locationcode', $department->locationcode) }}" required aria-required="true">
                @error('locationcode')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="officename" class="form-label">Office Name <span class="text-danger">*</span></label>
                <input type="text" id="officename" name="officename" class="form-control w-full @error('officename') border-danger @enderror" value="{{ old('officename', $department->officename) }}" required aria-required="true">
                @error('officename')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="accountableper" class="form-label">Accountable Person (Department User) <span class="text-danger">*</span></label>
                <select id="accountableper" name="accountableper" class="form-select w-full @error('accountableper') border-danger @enderror" required aria-required="true">
                    <option value="">Select Accountable Person</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ old('accountableper', $department->accountableper) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <div class="text-xs text-gray-500 mt-1">
                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                    Only Department Users are available for selection as accountable persons.
                </div>
                @error('accountableper')
                    <div class="message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control w-full @error('description') border-danger @enderror" rows="4">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12 flex items-center justify-center sm:justify-end mt-5">
            <button type="submit" class="btn btn-primary w-24 mr-2">Update</button>
            <a href="{{ route('departments.index') }}" class="btn btn-secondary w-24">Cancel</a>
        </div>
    </form>
</div>
@endsection 