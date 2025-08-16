@extends('layouts.app')

@section('title', 'Deploy Supplies')
<link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">
@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Deploy New Item</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('supplies.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Supplies
        </a>
    </div>
</div>

<div class="intro-y box p-5 mt-5">
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="flex items-center">
                <div class="mr-3">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500"></i>
                </div>
                <div>
                    <h4 class="font-medium">There {{ $errors->count() === 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} with your submission</h4>
                    <ul class="mt-2 list-disc list-inside text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('deployed-items.store') }}" method="POST" id="deployForm" class="grid grid-cols-12 gap-6" onsubmit="return handleFormSubmit(event)">
        @csrf
        <input type="hidden" name="deployedID" value="{{ $deployedID }}">
        
        <!-- Select Supply (drives item details below) -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="supply_id" class="form-label">Select Supply <span class="text-danger">*</span></label>
                <select id="supply_id" name="supply_id" class="form-select w-full" required>
                    <option value="" disabled>Choose from available supplies</option>
                    @foreach($supplies as $supply)
                        <option 
                            value="{{ $supply->itemID }}"
                            data-name="{{ $supply->name }}"
                            data-description="{{ $supply->description ?? '' }}"
                            data-category="{{ $supply->category->categoryName ?? 'Uncategorized' }}"
                            data-unit-cost="{{ $supply->unit_cost ?? 0 }}"
                            data-acquired="{{ optional($supply->acquired_at)->format('Y-m-d') }}"
                            data-available="{{ $supply->quantity ?? 0 }}"
                            {{ (string)old('supply_id') === (string)$supply->itemID ? 'selected' : (!$errors->any() && $loop->first && !old('supply_id') ? 'selected' : '') }}
                        >
                            {{ $supply->name }} (Available: {{ $supply->quantity ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Quantity -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="quantity" class="form-label">Quantity to Deploy <span class="text-danger">*</span></label>
                <input type="number" id="quantity" name="quantity" 
                       class="form-control w-full @error('quantity') border-danger @enderror" 
                       min="1" 
                       max="1" 
                       value="1"
                       required
                       onchange="validateQuantity(this)">
                <div id="available-quantity" class="text-xs text-slate-500 mt-1">
                    Available: <span id="available-stock">1</span>
                </div>
                @error('quantity')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Item Name -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="itemName" class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" id="itemName" name="itemName" 
                       class="form-control w-full @error('itemName') border-danger @enderror" 
                       value="{{ old('itemName') }}" 
                       required readonly>
                <input type="hidden" name="itemName_hidden" id="itemName_hidden" value="{{ old('itemName') }}">
                @error('itemName')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Item Description -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="itemDescription" class="form-label">Description</label>
                <textarea id="itemDescription" name="itemDescription" 
                          class="form-control w-full @error('itemDescription') border-danger @enderror" 
                          rows="3" readonly>{{ old('itemDescription') }}</textarea>
                @error('itemDescription')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- Date Acquired -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="dateAcquired" class="form-label">Date Acquired <span class="text-danger">*</span></label>
                <input type="date" id="dateAcquired" name="dateAcquired" 
                       class="form-control w-full @error('dateAcquired') border-danger @enderror" 
                       value="{{ old('dateAcquired') }}" 
                       required readonly>
                <input type="hidden" name="dateAcquired_hidden" id="dateAcquired_hidden" value="{{ old('dateAcquired') }}">
                @error('dateAcquired')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Cost (auto-computed) -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="cost" class="form-label">Cost <span class="text-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                    <input type="number" id="cost" name="cost" 
                           placeholder="0.00" 
                           step="0.01" min="0" 
                           class="form-control w-full pl-8 @error('cost') border-danger @enderror" 
                           value="{{ old('cost') }}" 
                           required readonly>
                    <input type="hidden" name="cost_hidden" id="cost_hidden" value="{{ old('cost') }}">
                    <!-- Ensure quantity defaults to 1 for backend single deploy -->
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="qr_code" class="form-label">QR Code <span class="text-danger">*</span></label>
                <div class="flex rounded-md shadow-sm">
                    <input type="text" id="qr_code" name="qr_code" 
                           class="form-control w-full rounded-r-none @error('qr_code') border-danger @enderror" 
                           value="{{ old('qr_code', 'DEP-' . strtoupper(Str::random(10))) }}"
                           required>
                    <button type="button" onclick="generateQRCode()"
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                    </button>
                </div>
                @error('qr_code')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Department -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="departmentID" class="form-label">Department (Accountable Person) <span class="text-danger">*</span></label>
                <select id="departmentID" name="departmentID" 
                        class="form-select w-full @error('departmentID') border-danger @enderror" 
                        required>
                    <option value="" disabled>Select a department</option>
                    @foreach($departments as $department)
                        @php
                            $accountablePerson = $department->user ? $department->user->name : 'No Accountable Person';
                        @endphp
                        <option value="{{ $department->departmentID }}" 
                                data-accountable="{{ $accountablePerson }}"
                                {{ old('departmentID') == $department->departmentID ? 'selected' : '' }}>
                            {{ $department->officename }} ({{ $department->departmentID }}) - {{ $accountablePerson }}
                        </option>
                    @endforeach
                </select>
                @error('departmentID')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Date Deployed -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="dateDeployed" class="form-label">Date Deployed <span class="text-danger">*</span></label>
                <input type="date" id="dateDeployed" name="dateDeployed" 
                       class="form-control w-full @error('dateDeployed') border-danger @enderror" 
                       value="{{ old('dateDeployed', \Illuminate\Support\Carbon::now()->toDateString()) }}" 
                       required>
                @error('dateDeployed')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Item Category (from supply) -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="itemCategoryText" class="form-label">Item Category <span class="text-danger">*</span></label>
                <input type="text" id="itemCategoryText" class="form-control w-full" value="{{ old('itemCategory') }}" readonly>
                <input type="hidden" id="itemCategory" name="itemCategory" value="{{ old('itemCategory') }}">
                <input type="hidden" id="itemCategory_hidden" name="itemCategory_hidden" value="{{ old('itemCategory') }}">
                @error('itemCategory')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="col-span-12 sm:col-span-6">
            <div class="input-form">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select id="status" name="status" 
                        class="form-select w-full @error('status') border-danger @enderror" 
                        required>
                    <option value="" disabled selected>Select status</option>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="under_maintenance" {{ old('status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                </select>
                @error('status')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remarks -->
        <div class="col-span-12">
            <div class="input-form">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" 
                          class="form-control w-full @error('remarks') border-danger @enderror" 
                          >{{ old('remarks') }}</textarea>
                </div>
                @error('remarks')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="col-span-12 flex items-center justify-center sm:justify-end mt-5">
            <button type="button" onclick="window.history.back()" class="btn btn-secondary w-24 mr-2">
                <i data-lucide="x" class="w-4 h-4 mr-2"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary w-40">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Deploy Item
            </button>
        </div>
    </form>
    </div>
</div>

<!-- Item Template (Hidden) -->
<template id="item-template">
    <div class="item-row border-b border-gray-200 pb-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Supply Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <select name="items[][supply_id]" required
                        class="supply-select w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Item</option>
                    @foreach($supplies as $supply)
                        <option value="{{ $supply->id }}" 
                                data-quantity="{{ $supply->quantity }}"
                                data-unit="{{ $supply->unit }}">
                            {{ $supply->name }} (Available: {{ $supply->quantity }} {{ $supply->unit }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <div class="flex rounded-md shadow-sm">
                    <input type="number" name="items[][quantity]" required min="1" value="1"
                           class="quantity-input flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <span class="quantity-unit inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Available: <span class="available-quantity">0</span>
                </p>
            </div>
            
            <!-- Condition -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                <select name="items[][condition]" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="new">New</option>
                    <option value="good" selected>Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            
            <!-- Remove Button -->
            <div class="flex items-end">
                <button type="button" 
                        class="remove-item-btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Remove
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
// Function to update form fields when supply is selected
function validateQuantity(input) {
    const maxQuantity = parseInt(input.max);
    const value = parseInt(input.value);
    
    if (isNaN(value) || value < 1) {
        input.value = 1;
    } else if (value > maxQuantity) {
        input.value = maxQuantity;
        alert(`Cannot deploy more than ${maxQuantity} items. Available stock is limited.`);
    }
    
    // Update the cost based on quantity
    updateCost();
}

function updateFormFields() {
    console.log('--- updateFormFields called ---');
    const supplySelect = document.getElementById('supply_id');
    if (!supplySelect) {
        console.error('Supply select element not found');
        return;
    }
    
    if (supplySelect.selectedIndex === 0) {
        console.log('No supply selected');
        return;
    }
    
    const selectedOption = supplySelect.options[supplySelect.selectedIndex];
    if (!selectedOption) {
        console.error('No option selected');
        return;
    }
    
    console.log('Selected option data:', {
        name: selectedOption.dataset.name,
        description: selectedOption.dataset.description,
        category: selectedOption.dataset.category,
        unitCost: selectedOption.dataset.unitCost,
        acquired: selectedOption.dataset.acquired,
        available: selectedOption.dataset.available
    });
    
    // Compute today's date in YYYY-MM-DD for fallbacks
    const today = new Date();
    const todayStr = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
        .toISOString().slice(0, 10);

    // Define all fields to update with correct data attribute names
    const fields = [
        { id: 'itemName', value: selectedOption.dataset.name || '' },
        { id: 'itemDescription', value: selectedOption.dataset.description || '' },
        { id: 'itemCategoryText', value: selectedOption.dataset.category || '' },
        { id: 'itemCategory', value: selectedOption.dataset.category || '' },
        { id: 'dateAcquired', value: selectedOption.dataset.acquired || todayStr },
        { id: 'cost', value: selectedOption.dataset.unitCost || '0.00' }
    ];
    
    // Debug: Log the selected option's dataset
    console.log('Selected option dataset:', selectedOption.dataset);
    
    // Update each field with logging
    fields.forEach(field => {
        console.log(`Updating field ${field.id} with value:`, field.value);
        const element = document.getElementById(field.id);
        
        if (element) {
            console.log(`Found element ${field.id}, setting value to:`, field.value);
            element.value = field.value;
            
            // Also update hidden field if it exists
            const hiddenElement = document.getElementById(`${field.id}_hidden`);
            if (hiddenElement) {
                console.log(`Updating hidden field ${field.id}_hidden`);
                hiddenElement.value = field.value;
            }
            
            // Trigger change event to ensure any listeners are notified
            const event = new Event('change');
            element.dispatchEvent(event);
            if (hiddenElement) {
                hiddenElement.dispatchEvent(event);
            }
        } else {
            console.error(`Element not found: ${field.id}`);
        }
    });
    
    // Update available quantity
    const availableText = document.getElementById('available-text');
    if (availableText) {
        availableText.textContent = `Available: ${selectedOption.dataset.available || 0}`;
    }
    
    // Update quantity field max
    const qtyInput = document.getElementById('quantity');
    if (qtyInput) {
        qtyInput.max = selectedOption.dataset.available || 0;
        qtyInput.value = '1';
    }
}

// Handle form submission
function handleFormSubmit(event) {
    const supplySelect = document.getElementById('supply_id');
    if (!supplySelect) {
        // If the select is missing, let server-side validation handle it
        return true;
    }
    if (!supplySelect.value) {
        const firstValidIndex = Array.from(supplySelect.options).findIndex(o => o.value);
        if (firstValidIndex > 0) {
            supplySelect.selectedIndex = firstValidIndex;
            updateFormFields();
        }
    }
    
    // Get the selected supply data
    const selectedOption = supplySelect.options[supplySelect.selectedIndex];
    if (!selectedOption) {
        // Let server-side validation handle if somehow missing
        return true;
    }
    
    // Update all form fields with supply data
    const fields = {
        'itemName': selectedOption.dataset.name || '',
        'itemDescription': selectedOption.dataset.description || '',
        'itemCategoryText': selectedOption.dataset.category || '',
        'itemCategory': selectedOption.dataset.category || '',
        'dateAcquired': selectedOption.dataset.acquired || '',
        'cost': selectedOption.dataset.unitCost || '0.00'
    };
    
    // Update each field and its hidden counterpart
    Object.entries(fields).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.value = value;
            const hiddenElement = document.getElementById(`${id}_hidden`);
            if (hiddenElement) {
                hiddenElement.value = value;
            }
        }
    });
    
    // Ensure department is selected (attempt auto-select first real option)
    const departmentSelect = document.getElementById('departmentID');
    if (departmentSelect && !departmentSelect.value) {
        const firstDeptIndex = Array.from(departmentSelect.options).findIndex(o => o.value);
        if (firstDeptIndex > 0) {
            departmentSelect.selectedIndex = firstDeptIndex;
        }
    }
    // If still empty, allow backend validation to handle instead of blocking with alert
    
    return true;
}

// Initialize the form when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('--- DOM fully loaded ---');

    const supplySelect = document.getElementById('supply_id');
    if (supplySelect) {
        // When user changes supply, refresh dependent fields
        supplySelect.addEventListener('change', function() {
            updateFormFields();
        });

        // If nothing is selected (placeholder), auto-select first actual supply
        if (!supplySelect.value) {
            const firstValidIndex = Array.from(supplySelect.options).findIndex(o => o.value);
            if (firstValidIndex > 0) {
                supplySelect.selectedIndex = firstValidIndex;
            }
        }

        // Populate the form once on load
        updateFormFields();
        // And once more after a short delay to ensure hydration
        setTimeout(updateFormFields, 100);
    }

    // Auto-select first department if none chosen
    const departmentSelect = document.getElementById('departmentID');
    if (departmentSelect && !departmentSelect.value && departmentSelect.options.length > 1) {
        const firstDeptIndex = Array.from(departmentSelect.options).findIndex(o => o.value);
        if (firstDeptIndex > 0) {
            departmentSelect.selectedIndex = firstDeptIndex;
        }
    }
});
// Add event listener for quantity changes to update cost
const qtyInput = document.getElementById('quantity');
if (qtyInput) {
    document.getElementById('supply_id')?.addEventListener('change', updateFormFields);
    
    // Add input event for quantity to update cost in real-time
    document.getElementById('quantity')?.addEventListener('input', function() {
        updateCost();
    });
    qtyInput.addEventListener('input', function() {
        const supplySelect = document.getElementById('supply_id');
        if (!supplySelect || supplySelect.selectedIndex === 0) return;
        
        const selectedOption = supplySelect.options[supplySelect.selectedIndex];
        const unitCost = parseFloat(selectedOption.dataset.unitCost) || 0;
        const quantity = parseInt(this.value) || 0;
        const maxQuantity = parseInt(selectedOption.dataset.available) || 0;
        
        // Ensure quantity doesn't exceed available
        if (quantity > maxQuantity) {
            this.value = maxQuantity;
        }
        
        // Update the available quantity and max value
        const availableQuantity = parseInt(selectedOption.getAttribute('data-available') || 1);
        const quantityInput = document.getElementById('quantity');
        quantityInput.max = availableQuantity;
        document.getElementById('available-stock').textContent = availableQuantity;
        
        // Update the cost field
        const costPerUnit = parseFloat(selectedOption.getAttribute('data-unit-cost') || 0);
        updateCost(costPerUnit);
    });
}
</script>
@endpush

<style>
.item-row {
    transition: all 0.3s ease;
}

.item-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
</style>
@endsection
