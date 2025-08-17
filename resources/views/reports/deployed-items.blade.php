@extends('layouts.app')

@section('title', 'Deployed Items Report')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Deployed Items Report</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Reports
        </a>
        <form action="{{ route('reports.export', ['type' => 'deployed-items']) }}" method="GET" class="inline-block">
            <input type="hidden" name="department_id" value="{{ request('department_id') }}">
            <button type="submit" class="btn btn-success shadow-md">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export Report
            </button>
        </form>
    </div>
</div>

<!-- Department Filter -->
<div class="mt-4 mb-2">
    <form method="GET" action="{{ route('reports.deployed-items') }}" class="flex flex-wrap items-center gap-2">
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

<!-- BEGIN: Deployed Items Report -->
<div class="intro-y box p-5 mt-5">
    <div class="overflow-x-auto">
        <table class="table table-report -mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap text-center">Item Name</th>
                    <th class="whitespace-nowrap text-center">Category</th>
                    <th class="whitespace-nowrap text-center">Unit Cost</th>
                    <th class="whitespace-nowrap text-center">Quantity</th>
                    <th class="whitespace-nowrap text-center">Amount</th>
                    <th class="whitespace-nowrap text-center">Date Deployed</th>
                    <th class="whitespace-nowrap text-center">Status</th>
                    <th class="whitespace-nowrap text-center">Added By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deployedItems as $item)
                    <tr class="intro-x">
                        <td class="text-center">{{ $item->itemName ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->itemCategory ?? 'N/A' }}</td>
                        <td class="text-center">₱{{ number_format($item->cost ?? 0, 2) }}</td>
                        <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                        <td class="text-center">₱{{ number_format(($item->cost ?? 0) * ($item->quantity ?? 1), 2) }}</td>
                        <td class="text-center">{{ optional($item->dateDeployed)->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="text-center">
                            <span class="px-2 py-1 rounded-full text-xs {{ 
                                $item->status === 'active' ? 'bg-success text-white' : 'bg-warning text-white' 
                            }}">
                                {{ ucfirst($item->status ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="text-center">{{ $item->deployedBy ? $item->deployedBy->name : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No deployed items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- BEGIN: Pagination -->
    @if(method_exists($deployedItems, 'links'))
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
            {{ $deployedItems->links() }}
        </div>
    @endif
    <!-- END: Pagination -->
</div>
<!-- END: Deployed Items Report -->
@endsection
