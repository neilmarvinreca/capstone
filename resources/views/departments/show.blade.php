@extends('layouts.app')

@section('title', 'View Department')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Department Details
    </h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('departments.index') }}" class="btn btn-secondary shadow-md">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Departments
        </a>
    </div>
</div>

<!-- BEGIN: Department Details -->
<div class="intro-y box p-5 mt-5">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12">
            <div class="border-b border-gray-200 dark:border-dark-5 pb-5">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Department ID:</h3>
                    <p class="font-medium">{{ $department->departmentID }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Location Code:</h3>
                    <p class="font-medium">{{ $department->locationcode }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Office Name:</h3>
                    <p class="font-medium">{{ $department->officename }}</p>
                </div>
                @if($department->user)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Accountable Person:</h3>
                    <p class="font-medium">{{ $department->user->name }} ({{ $department->user->email }})</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="col-span-12">
            <div class="mt-4">
                <h3 class="text-gray-600 dark:text-gray-400 font-medium">Statistics</h3>
                <div class="mt-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary">
                        {{ $department->deployed_items_count ?? 0 }} {{ Str::plural('item', $department->deployed_items_count ?? 0) }} deployed in this department
                    </span>
                </div>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Created: {{ $department->created_at->format('M d, Y') }} ({{ $department->created_at->diffForHumans() }})
                </div>
                @if($department->updated_at != $department->created_at)
                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Last Updated: {{ $department->updated_at->format('M d, Y') }} ({{ $department->updated_at->diffForHumans() }})
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- END: Department Details -->

<!-- Department Deployed Items -->
@if(isset($department->deployedItems) && $department->deployedItems->count() > 0)
<div class="intro-y box p-5 mt-5">
    <h3 class="text-lg font-medium mb-4">Deployed Items in Department</h3>
    <div class="overflow-x-auto">
        <table class="table table-report">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Date Deployed</th>
                    <th>Status</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($department->deployedItems as $item)
                <tr>
                    <td>{{ $item->itemName ?? 'N/A' }}</td>
                    <td>{{ $item->itemDescription ?? 'N/A' }}</td>
                    <td>{{ $item->dateDeployed ? $item->dateDeployed->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $item->status ?? 'N/A' }}</td>
                    <td>{{ $item->quantity ?? '1' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection