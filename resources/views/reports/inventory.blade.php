@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Inventory Report</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Reports
        </a>
        <form action="{{ route('reports.export', ['type' => 'inventory']) }}" method="GET" class="inline-block">
            <input type="hidden" name="department_id" value="{{ request('department_id') }}">
            <button type="submit" class="btn btn-success shadow-md">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export Report
            </button>
        </form>
    </div>
</div>

<!-- Department Filter -->
<div class="mt-4 mb-2">
    <form method="GET" action="{{ route('reports.inventory') }}" class="flex flex-wrap items-center gap-2">
        <label for="department_id" class="form-label mr-2">Filter by Department:</label>
        <select name="department_id" id="department_id" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Departments</option>
            @foreach($departments as $department)
                <option value="{{ $department->departmentID }}" {{ request('department_id') == $department->departmentID ? 'selected' : '' }}>
                    {{ $department->officename }} ({{ $department->departmentID }})
                </option>
            @endforeach
        </select>
    </form>
</div>

<!-- BEGIN: Inventory Report -->
<div class="intro-y box p-5 mt-5">
    <div class="overflow-x-auto">
        <table class="table table-report -mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap text-center">Item Name</th>
                    <th class="whitespace-nowrap text-center">Description</th>
                    <th class="whitespace-nowrap text-center">Category</th>
                    <th class="whitespace-nowrap text-center">Unit Cost</th>
                    <th class="whitespace-nowrap text-center">Quantity</th>
                    <th class="whitespace-nowrap text-center">Amount</th>
                    <th class="whitespace-nowrap text-center">Added By</th>
                    <th class="whitespace-nowrap text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplies as $supply)
                    <tr class="intro-x">
                        <td class="text-center">{{ $supply->name }}</td>
                        <td class="text-center">{{ $supply->description ?? 'N/A' }}</td>
                        <td class="text-center">{{ $supply->category->categoryName ?? 'N/A' }}</td>
                        <td class="text-center">₱{{ number_format($supply->unit_cost ?? 0, 2) }}</td>
                        <td class="text-center">{{ $supply->quantity }}</td>
                        <td class="text-center">₱{{ number_format(($supply->unit_cost ?? 0) * $supply->quantity, 2) }}</td>
                        <td class="text-center">{{ $supply->addedBy ? $supply->addedBy->name : 'N/A' }}</td>
                        <td class="text-center">
                            @if($supply->quantity <= 5)
                                <div class="flex items-center justify-center text-danger">
                                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i> Low Stock
                                </div>
                            @else
                                <div class="flex items-center justify-center text-success">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> In Stock
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No supplies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- BEGIN: Pagination -->
    @if(method_exists($supplies, 'links'))
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
            {{ $supplies->links() }}
        </div>
    @endif
    <!-- END: Pagination -->
</div>
<!-- END: Inventory Report -->
@endsection
