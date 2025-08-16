@extends('layouts.app')

@section('title', 'Edit Supply')

@section('content')
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
    });
</script>
@endpush
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Edit Supply</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('supplies.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Supplies
        </a>
    </div>
</div>

<!-- BEGIN: Edit Supply Form -->
<div class="intro-y box p-5 mt-5">
    <form method="POST" action="{{ route('supplies.update', $supply) }}" class="grid grid-cols-12 gap-6">
        @csrf
        @method('PUT')
        
        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control w-full @error('name') border-danger @enderror" value="{{ old('name', $supply->name) }}" required aria-required="true">
                @error('name')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                <select id="category_id" name="category_id" class="form-select w-full @error('category_id') border-danger @enderror" required aria-required="true">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryID }}" {{ old('category_id', $supply->category_id) == $category->categoryID ? 'selected' : '' }}>
                            {{ $category->categoryName }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="fund_code" class="form-label">Fund Code <span class="text-danger">*</span></label>
                <input type="text" id="fund_code" name="fund_code" class="form-control w-full @error('fund_code') border-danger @enderror" 
                    value="{{ old('fund_code', $supply->fund_code) }}" required aria-required="true">
                @error('fund_code')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="pp_sub_account" class="form-label">PPE Sub Account <span class="text-danger">*</span></label>
                <input type="text" id="pp_sub_account" name="pp_sub_account" class="form-control w-full @error('pp_sub_account') border-danger @enderror" 
                    value="{{ old('pp_sub_account', $supply->pp_sub_account) }}" required aria-required="true">
                @error('pp_sub_account')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        
        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="unit_cost" class="form-label">Unit Cost <span class="text-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                    <input type="number" id="unit_cost" name="unit_cost" 
                           class="form-control w-full pl-8 @error('unit_cost') border-danger @enderror" 
                           value="{{ old('unit_cost', $supply->unit_cost) }}" 
                           min="0" 
                           step="0.01" 
                           required>
                </div>
                @error('unit_cost')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" id="quantity" name="quantity" 
                       class="form-control w-full @error('quantity') border-danger @enderror" 
                       value="{{ old('quantity', $supply->quantity) }}" 
                       min="1" 
                       required>
                @error('quantity')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                    <input type="number" id="amount" name="amount" 
                           class="form-control w-full pl-8 @error('amount') border-danger @enderror" 
                           value="{{ old('amount', $supply->amount) }}" 
                           required 
                           min="0" 
                           step="0.01"
                           readonly>
                </div>
                @error('amount')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <input type="hidden" name="added_by" value="{{ auth()->id() }}">
        
        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="minimum_stock" class="form-label">Minimum Stock <span class="text-danger">*</span></label>
                <input type="number" id="minimum_stock" name="minimum_stock" 
                       class="form-control w-full @error('minimum_stock') border-danger @enderror" 
                       value="{{ old('minimum_stock', $supply->minimum_stock) }}" 
                       min="0" 
                       required>
                @error('minimum_stock')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="gl_code" class="form-label">GL Code <span class="text-danger">*</span></label>
                <input type="text" id="gl_code" name="gl_code" class="form-control w-full @error('gl_code') border-danger @enderror" value="{{ old('gl_code', $supply->gl_code) }}" required aria-required="true">
                @error('gl_code')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="input-form">
                <label for="acquired_at" class="form-label">Acquired At <span class="text-danger">*</span></label>
                <input type="date" id="acquired_at" name="acquired_at" class="form-control w-full @error('acquired_at') border-danger @enderror" value="{{ old('acquired_at', optional($supply->acquired_at)->format('Y-m-d')) }}" required aria-required="true">
                @error('acquired_at')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12">
            <div class="input-form">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control w-full @error('description') border-danger @enderror" 
                    rows="4">{{ old('description', $supply->description) }}</textarea>
                @error('description')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-span-12 flex items-center justify-center sm:justify-end mt-5">
            <button type="submit" class="btn btn-primary w-24 mr-2">Update</button>
            <a href="{{ route('supplies.index') }}" class="btn btn-secondary w-24">Cancel</a>
        </div>
    </form>
</div>
<!-- END: Edit Supply Form -->
@endsection
