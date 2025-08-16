@extends('layouts.app')

@section('title', 'Create Supply')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">
@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Create New Supply</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('supplies.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Supplies
        </a>
    </div>
</div>

<div class="intro-y box p-5 mt-5">

    <form method="POST" action="{{ route('supplies.store') }}" class="grid grid-cols-12 gap-6">
        @csrf
        <input type="hidden" name="added_by" value="{{ auth()->id() }}">
        
        <!-- Item Name -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" 
                       class="form-control w-full @error('name') border-danger @enderror" 
                       value="{{ old('name') }}" 
                       required>
                @error('name')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Category -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                <select id="category_id" name="category_id" 
                        class="form-select w-full @error('category_id') border-danger @enderror" 
                        required>
                    <option value="" disabled selected>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryID }}" {{ old('category_id') == $category->categoryID ? 'selected' : '' }}>
                            {{ $category->categoryName }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" 
                          class="form-control w-full @error('description') border-danger @enderror" 
                          rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Date Acquired -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="acquired_at" class="form-label">Date Acquired <span class="text-danger">*</span></label>
                <input type="date" id="acquired_at" name="acquired_at" 
                       class="form-control w-full @error('acquired_at') border-danger @enderror" 
                       value="{{ old('acquired_at', now()->format('Y-m-d')) }}" 
                       required>
                @error('acquired_at')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Estimated Life -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="estimated_life" class="form-label">Estimated Life (Years)</label>
                <input type="number" id="estimated_life" name="estimated_life" 
                       class="form-control w-full @error('estimated_life') border-danger @enderror" 
                       value="{{ old('estimated_life') }}" 
                       min="0" 
                       step="1">
                @error('estimated_life')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Unit Cost -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="unit_cost" class="form-label">Unit Cost <span class="text-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                    <input type="number" id="unit_cost" name="unit_cost" 
                           class="form-control w-full pl-8 @error('unit_cost') border-danger @enderror" 
                           value="{{ old('unit_cost') }}" 
                           min="0" 
                           step="0.01" 
                           required>
                </div>
                @error('unit_cost')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Quantity -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" id="quantity" name="quantity" 
                       class="form-control w-full @error('quantity') border-danger @enderror" 
                       value="{{ old('quantity', 1) }}" 
                       min="1" 
                       required>
                @error('quantity')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Minimum Stock -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="minimum_stock" class="form-label">Minimum Stock <span class="text-danger">*</span></label>
                <input type="number" id="minimum_stock" name="minimum_stock" 
                       class="form-control w-full @error('minimum_stock') border-danger @enderror" 
                       value="{{ old('minimum_stock', 0) }}" 
                       min="0" 
                       required>
                @error('minimum_stock')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        

        <!-- Fund Code -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="fund_code" class="form-label">Fund Code <span class="text-danger">*</span></label>
                <select id="fund_code" name="fund_code" 
                        class="form-select w-full @error('fund_code') border-danger @enderror" 
                        required>
                    <option value="" disabled selected>Select Fund Code</option>
                    <option value="FUND 101" {{ old('fund_code') == 'FUND 101' ? 'selected' : '' }}>FUND 101</option>
                    <option value="FUND 161" {{ old('fund_code') == 'FUND 161' ? 'selected' : '' }}>FUND 161</option>
                    <option value="FUND 162" {{ old('fund_code') == 'FUND 162' ? 'selected' : '' }}>FUND 162</option>
                    <option value="FUND 163" {{ old('fund_code') == 'FUND 163' ? 'selected' : '' }}>FUND 163</option>
                    <option value="FUND 164" {{ old('fund_code') == 'FUND 164' ? 'selected' : '' }}>FUND 164</option>
                </select>
                @error('fund_code')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- PPE Sub Account -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="pp_sub_account" class="form-label">PPE Sub Account <span class="text-danger">*</span></label>
                <input type="text" id="pp_sub_account" name="pp_sub_account" 
                       class="form-control w-full @error('pp_sub_account') border-danger @enderror" 
                       value="{{ old('pp_sub_account') }}" 
                       required>
                @error('pp_sub_account')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- GL Code -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="gl_code" class="form-label">GL Code <span class="text-danger">*</span></label>
                <input type="text" id="gl_code" name="gl_code" 
                       class="form-control w-full @error('gl_code') border-danger @enderror" 
                       value="{{ old('gl_code') }}" 
                       required>
                @error('gl_code')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Total Amount -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                    <input type="number" id="amount" name="amount" 
                           class="form-control w-full pl-8 @error('amount') border-danger @enderror" 
                           value="{{ old('amount') }}" 
                           min="0" 
                           step="0.01" 
                           required>
                </div>
                @error('amount')
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
<!-- END: Create Supply Form -->
@endsection

@push('scripts')
<script>
    // Function to calculate total amount
    function calculateTotal() {
        const unitCost = parseFloat(document.getElementById('unit_cost').value) || 0;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const totalAmount = (unitCost * quantity).toFixed(2);
        document.getElementById('amount').value = totalAmount;
    }

    // Add event listeners for unit cost and quantity changes
    document.addEventListener('DOMContentLoaded', function() {
        const unitCostInput = document.getElementById('unit_cost');
        const quantityInput = document.getElementById('quantity');
        
        if (unitCostInput) {
            unitCostInput.addEventListener('input', calculateTotal);
        }
        
        if (quantityInput) {
            quantityInput.addEventListener('input', calculateTotal);
        }
        
        // Calculate initial total if values exist
        calculateTotal();
        
        // Show success/error messages
        @if(session('success'))
            Swal.fire({
                title: 'Success',
                html: `
                    <div class="text-center py-2">
                        <i data-lucide="check-circle" class="w-10 h-10 mx-auto text-green-500 mb-2"></i>
                        <p class="text-sm text-gray-600">
                            Successfully added Supply
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
                            Warning, An error occurred while adding a supply
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
