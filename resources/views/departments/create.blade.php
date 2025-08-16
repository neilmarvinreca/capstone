@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Create New Department</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('departments.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Departments
        </a>
    </div>
</div>

<div class="intro-y box p-5 mt-5">
    <form method="POST" action="{{ route('departments.store') }}" class="grid grid-cols-12 gap-6">
        @csrf
        <div class="col-span-12">
            <div class="input-form">
                <label for="departmentID" class="form-label">Department ID <span class="text-danger">*</span></label>
                <input type="text" id="departmentID" name="departmentID" class="form-control w-full @error('departmentID') border-danger @enderror" value="{{ old('departmentID') }}" required aria-required="true">
                @error('departmentID')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="locationcode" class="form-label">Location Code <span class="text-danger">*</span></label>
                <input type="text" id="locationcode" name="locationcode" class="form-control w-full @error('locationcode') border-danger @enderror" value="{{ old('locationcode') }}" required aria-required="true">
                @error('locationcode')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="officename" class="form-label">Office Name <span class="text-danger">*</span></label>
                <input type="text" id="officename" name="officename" class="form-control w-full @error('officename') border-danger @enderror" value="{{ old('officename') }}" required aria-required="true">
                @error('officename')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="accountableper" class="form-label">Accountable Person <span class="text-danger">*</span></label>
                <select id="accountableper" name="accountableper" class="form-select w-full @error('accountableper') border-danger @enderror" required aria-required="true">
                    <option value="">Select Accountable Person</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ old('accountableper') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('accountableper')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12">
            <div class="input-form">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control w-full @error('description') border-danger @enderror" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-span-12 flex items-center justify-center sm:justify-end mt-5">
        <button type="submit" class="btn btn-primary w-24 mr-2">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save
            </button>
            <button type="reset" class="btn btn-secondary w-24">
                <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i> Reset
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success/error messages
        @if(session('success'))
            Swal.fire({
                title: 'Success',
                html: `
                    <div class="text-center py-2">
                        <i data-lucide="check-circle" class="w-10 h-10 mx-auto text-green-500 mb-2"></i>
                        <p class="text-sm text-gray-600">
                            Successfully added Department
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
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("departments.index") }}';
                }
            });
        @elseif(session('error'))
            Swal.fire({
                title: 'Error',
                html: `
                    <div class="text-center py-2">
                        <i data-lucide="x-circle" class="w-10 h-10 mx-auto text-red-500 mb-2"></i>
                        <p class="text-sm text-gray-600">
                            Warning, An error occurred while adding a department
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
    });
</script>
@endpush

@endsection 