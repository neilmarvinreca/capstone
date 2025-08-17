@extends('layouts.app')

@section('title', 'Deployed Items Overview')

@push('styles')
<link rel="icon" href="{{ asset('dist/images/logodssc.png') }}">
<style>
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-active { background-color: #10b981; color: white; }
    .status-inactive { background-color: #6b7280; color: white; }
    .status-maintenance { background-color: #f59e0b; color: white; }
    .status-retired { background-color: #ef4444; color: white; }
    
    /* Fix button visibility and styling */
    .btn {
        transition: all 0.3s ease;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid transparent !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        cursor: pointer !important;
        user-select: none !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
    }
    
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Ensure button colors are visible */
    .btn-primary {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
    }
    
    .btn-secondary {
        background-color: #6b7280 !important;
        border-color: #6b7280 !important;
        color: white !important;
    }
    
    .btn-success {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
        color: white !important;
    }
    
    .btn-info {
        background-color: #06b6d4 !important;
        border-color: #06b6d4 !important;
        color: white !important;
    }
    
    .btn-warning {
        background-color: #f59e0b !important;
        border-color: #f59e0b !important;
        color: white !important;
    }
    
    .btn-danger {
        background-color: #ef4444 !important;
        border-color: #ef4444 !important;
        color: white !important;
    }
    
    /* Fix button sizes */
    .btn-sm {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        border-radius: 0.375rem !important;
    }
    
    .w-8 {
        width: 2rem !important;
    }
    
    .h-8 {
        height: 2rem !important;
    }
    
    /* Ensure icons are visible */
    .btn i {
        color: inherit !important;
    }
</style>
@endpush

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8 mb-8">
    <h2 class="text-lg font-medium mr-auto">Deployed Items Overview</h2>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="package-check" class="report-box__icon text-primary"></i>
                    <div class="ml-auto">
                        <div class="report-box__indicator bg-success">
                            <i data-lucide="trending-up" class="w-4 h-4 ml-0.5"></i>
                        </div>
                    </div>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">{{ number_format($totalDeployed) }}</div>
                <div class="text-base text-slate-500 mt-1">Total Items Deployed</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="peso-sign" class="report-box__icon text-success text-3xl">₱</i>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">₱{{ number_format($totalValue, 2) }}</div>
                <div class="text-base text-slate-500 mt-1">Total Value Deployed</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="building-2" class="report-box__icon text-warning"></i>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">{{ count($byDepartment) }}</div>
                <div class="text-base text-slate-500 mt-1">Departments</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in h-full">
            <div class="box p-5 h-full">
                <div class="flex">
                    <i data-lucide="activity" class="report-box__icon text-pending"></i>
                </div>
                <div class="text-3xl font-bold leading-10 mt-4">
                    {{ number_format($deployedItems->where('status', 'active')->count()) }}
                </div>
                <div class="text-base text-slate-500 mt-1">Active Items</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="intro-y box p-5 mt-5">
    <div class="flex flex-col sm:flex-row sm:items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
        <div class="mr-auto">
            <h2 class="font-medium text-base">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @else
                    All Deployed Items
                @endif
            </h2>
            @if(request('search'))
                <div class="text-sm text-slate-500 mt-1">
                    Found {{ $deployedItems->total() }} item{{ $deployedItems->total() != 1 ? 's' : '' }}
                </div>
            @endif
        </div>
        <div class="flex items-center mt-3 sm:mt-0 space-x-2">
            <form method="GET" action="{{ route('deployed-items.index') }}" class="flex items-center">
                <div class="relative w-56">
                    <input type="text" 
                           name="search" 
                           class="form-control w-56 pr-10" 
                           placeholder="Supply name" 
                           value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('deployed-items.index') }}" 
                           class="absolute inset-y-0 right-0 flex items-center mr-3 text-slate-500 hover:text-danger"
                           title="Clear search">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary ml-2 mx-1">
                    <i data-lucide="search" class="w-4 h-4 mr-1"></i> Search
                </button>
            </form>

            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <a href="{{ route('deployed-items.archived') }}" 
                   class="btn btn-primary shadow-md ml-2 h-10 px-4 flex items-center justify-center rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <i data-lucide="archive" class="w-4 h-4 mr-2"></i> View Archived
                </a>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="table table-report table-auto">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Item Details</th>
                    <th class="whitespace-nowrap">Department User</th>
                    <th class="text-center whitespace-nowrap">Date Deployed</th>
                    <th class="text-center whitespace-nowrap">Quantity</th>
                    <th class="text-center whitespace-nowrap">Cost</th>
                    <th class="text-center whitespace-nowrap">Status</th>
                    <th class="text-center whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deployedItems as $item)
                    <!-- Debug: Item ID: {{ $item->id }}, DeployedID: {{ $item->deployedID }} -->
                    <tr class="intro-x">
                        <td>
                            <a href="{{ route('deployed-items.show', $item) }}" class="font-medium">{{ $item->itemName }}</a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                {{ $item->itemCategory }}
                                @if($item->itemDescription)
                                    • {{ Str::limit($item->itemDescription, 50) }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $item->department->user->name ?? 'No Accountable Person' }}</div>
                            <div class="text-slate-500 text-xs">{{ $item->department->officename ?? 'N/A' }} ({{ $item->department->departmentID ?? '' }})</div>
                        </td>
                        <td class="text-center">{{ optional($item->dateDeployed)->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">₱{{ number_format($item->cost, 2) }}</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('deployed-items.edit', $item) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                @if($item->qr_code_image)
                                    <button type="button" 
                                            class="btn btn-sm btn-info w-8 h-8 flex items-center justify-center p-0 mx-2" 
                                            onclick="viewAndDownloadQrCode('{{ $item->deployedID }}', '{{ $item->qrCode }}', '{{ $item->qr_code_image_url }}')"
                                            title="View & Download QR Code">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn btn-sm btn-secondary w-8 h-8 flex items-center justify-center p-0 mx-2" 
                                            onclick="generateQrCodeForItem('{{ $item->deployedID }}')"
                                            title="Generate QR Code">
                                        <i data-lucide="qrcode" class="w-4 h-4"></i>
                                    </button>
                                @endif
                                <form id="archive-form-{{ $item->deployedID }}" 
                                      action="{{ route('deployed-items.archive', $item->deployedID) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" 
                                            onclick="confirmArchive('{{ $item->deployedID }}', '{{ $item->itemName }}')"
                                            class="btn btn-sm w-8 h-8 flex items-center justify-center p-0 mx-2" 
                                            style="background-color: #f59e0b; border-color: #f59e0b; color: white;"
                                            title="Archive">
                                        <i data-lucide="archive" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No deployed items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($deployedItems->hasPages())
            <div class="mt-5">
                {{ $deployedItems->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Recent Deployments -->
<div class="intro-y box p-5 mt-5">
    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
        <h2 class="font-medium text-base mr-auto">Recent Deployments</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="table table-report">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Item Name</th>
                    <th class="whitespace-nowrap">Department</th>
                    <th class="text-center whitespace-nowrap">Date Deployed</th>
                    <th class="text-center whitespace-nowrap">Quantity</th>
                    <th class="text-center whitespace-nowrap">Status</th>
                    <th class="text-center whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDeployments as $item)
                    <tr class="intro-x">
                        <td>
                            <a href="{{ route('deployed-items.show', $item) }}" class="font-medium">{{ $item->itemName }}</a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                {{ $item->itemCategory }}
                                @if($item->itemDescription)
                                    • {{ Str::limit($item->itemDescription, 30) }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $item->department->user->name ?? 'No Accountable Person' }}</div>
                            <div class="text-slate-500 text-xs">{{ $item->department->officename ?? 'N/A' }} ({{ $item->department->departmentID ?? '' }})</div>
                        </td>
                        <td class="text-center">{{ optional($item->dateDeployed)->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('deployed-items.edit', $item) }}" class="btn btn-sm btn-primary w-8 h-8 flex items-center justify-center p-0 mx-2" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No recent deployments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function confirmArchive(id, name) {
    Swal.fire({
        title: 'Archive Item',
        html: `
            <div class="text-center py-2">
                <i data-lucide="archive" class="w-12 h-12 mx-auto text-yellow-500 mb-4"></i>
                <p class="text-sm text-gray-600">
                    Archive <span class="font-semibold">${name}</span>?
                    <br>
                    This item will be moved to the archive.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Archive',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-sm btn-warning px-4 py-1 text-xs mx-2',
            cancelButton: 'btn btn-sm btn-outline-secondary px-4 py-1 text-xs',
            popup: 'text-sm',
            actions: 'mt-3 flex-row-reverse justify-start'
        },
        buttonsStyling: false,
        width: '20rem',
        padding: '1rem',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                const confirmButton = document.querySelector('.swal2-confirm');
                if (confirmButton) {
                    confirmButton.innerHTML = '<i class="animate-spin -ml-1 mr-1 h-3 w-3">↻</i> Archiving...';
                    confirmButton.disabled = true;
                }
                document.getElementById(`archive-form-${id}`).submit();
            });
        }
    });
}
    // Add any necessary JavaScript here
    function showQRCode(qrData) {
        const target = document.getElementById('qrcode');
        if (!target || typeof QRCode === 'undefined') return;
        target.innerHTML = '';
        new QRCode(target, {
            text: qrData,
            width: 128,
            height: 128,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function generateQrCodeForItem(deployedID) {
        // Show loading state
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>';
        button.disabled = true;

        // Make AJAX request to generate QR code image
        fetch(`/deployed-items/${deployedID}/generate-qr`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                button.innerHTML = '<i data-lucide="check" class="w-3 h-3 text-green-500"></i>';
                button.disabled = true;
                
                // Show success notification
                Swal.fire({
                    title: 'Success!',
                    text: 'QR Code generated successfully!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
                // After 3 seconds, change to View QR Code button
                setTimeout(() => {
                    // Change button to View & Download QR Code button
                    button.className = button.className.replace('btn-secondary', 'btn-info');
                    button.innerHTML = '<i data-lucide="eye" class="w-4 h-4"></i>';
                    button.disabled = false;
                    button.title = 'View & Download QR Code';
                    
                    // Update onclick to view and download QR code
                    const qrCodeText = data.qr_code_text || 'Generated';
                    const qrImageUrl = data.qr_code_image_url;
                    button.setAttribute('onclick', `viewAndDownloadQrCode('${deployedID}', '${qrCodeText}', '${qrImageUrl}')`);
                }, 3000);
            } else {
                alert('Failed to generate QR code image: ' + data.message);
                // Reset button
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to generate QR code image. Please try again.');
            // Reset button
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    function viewQrCode(qrData, qrImageUrl) {
        // Debug logging
        console.log('QR Data:', qrData);
        console.log('QR Image URL:', qrImageUrl);
        
        // Extract deployedID from the qrImageUrl to use the view route
        const deployedID = qrImageUrl.match(/deployed_item_(\d+)_/)?.[1];
        const viewUrl = deployedID ? `/deployed-items/view/qr-code/${deployedID}` : qrImageUrl;
        
        // Show QR code in a modal
        Swal.fire({
            title: 'QR Code',
            html: `
                <div class="text-center">
                    <div class="mb-4">
                        <img src="${viewUrl}" 
                             alt="QR Code" 
                             class="mx-auto border rounded" 
                             style="width: 200px; height: 200px;"
                             onerror="console.error('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';"
                             onload="console.log('Image loaded successfully:', this.src);">
                        <div style="display: none; padding: 20px; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px;">
                            <p class="text-red-500 mb-2">Image failed to load</p>
                            <p class="text-sm text-gray-600">URL: ${viewUrl}</p>
                            <p class="text-sm text-gray-600">QR Code Text: ${qrData}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">QR Code Text:</div>
                    <div class="font-mono text-xs bg-gray-100 p-2 rounded inline-block">${qrData}</div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Close',
            confirmButtonColor: '#3085d6',
            width: '400px'
        });
    }

    function downloadQrCode(deployedID, qrCodeText, qrImageUrl) {
        // Extract deployedID from the qrImageUrl to use the view route for preview
        const extractedID = qrImageUrl.match(/deployed_item_(\d+)_/)?.[1];
        const viewUrl = extractedID ? `/deployed-items/view/qr-code/${extractedID}` : qrImageUrl;
        
        // Show download modal with QR code preview
        Swal.fire({
            title: 'Download QR Code',
            html: `
                <div class="text-center">
                    <div class="mb-4">
                        <img src="${viewUrl}" 
                             alt="QR Code" 
                             class="mx-auto border rounded" 
                             style="width: 200px; height: 200px;"
                             onerror="console.error('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';"
                             onload="console.log('Image loaded successfully:', this.src);">
                        <div style="display: none; padding: 20px; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px;">
                            <p class="text-red-500 mb-2">Image failed to load</p>
                            <p class="text-sm text-gray-600">URL: ${viewUrl}</p>
                            <p class="text-sm text-gray-600">QR Code Text: ${qrCodeText}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">QR Code Text:</div>
                    <div class="font-mono text-xs bg-gray-100 p-2 rounded inline-block">${qrCodeText}</div>
                    <div class="mt-4 text-sm text-gray-500">
                        Click "Download" to save the QR code image
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Download',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6b7280',
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Use the server download route directly for reliability
                const downloadUrl = `/deployed-items/download/qr-code/${deployedID}`;
                
                // Create a temporary link and trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `qr_code_${deployedID}_${qrCodeText}.png`;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message
                Swal.fire({
                    title: 'Download Started!',
                    text: 'QR Code download has begun',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    }

    function viewAndDownloadQrCode(deployedID, qrCodeText, qrImageUrl) {
        // Extract deployedID from the qrImageUrl to use the view route for preview
        const extractedID = qrImageUrl.match(/deployed_item_(\d+)_/)?.[1];
        const viewUrl = extractedID ? `/deployed-items/view/qr-code/${extractedID}` : qrImageUrl;
        
        // Show QR code in a modal with download option
        Swal.fire({
            title: 'QR Code',
            html: `
                <div class="text-center">
                    <div class="mb-4">
                        <img src="${viewUrl}" 
                             alt="QR Code" 
                             class="mx-auto border rounded" 
                             style="width: 200px; height: 200px;"
                             onerror="console.error('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';"
                             onload="console.log('Image loaded successfully:', this.src);">
                        <div style="display: none; padding: 20px; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px;">
                            <p class="text-red-500 mb-2">Image failed to load</p>
                            <p class="text-sm text-gray-600">URL: ${viewUrl}</p>
                            <p class="text-sm text-gray-600">QR Code Text: ${qrCodeText}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">QR Code Text:</div>
                    <div class="font-mono text-xs bg-gray-100 p-2 rounded inline-block">${qrCodeText}</div>
                    <div class="mt-4 text-sm text-gray-500">
                        Click "Download" to save the QR code image
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Download',
            cancelButtonText: 'Close',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6b7280',
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Use the server download route directly for reliability
                const downloadUrl = `/deployed-items/download/qr-code/${deployedID}`;
                
                // Create a temporary link and trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `qr_code_${deployedID}_${qrCodeText}.png`;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message
                Swal.fire({
                    title: 'Download Started!',
                    text: 'QR Code download has begun',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    }
</script>
@endpush

@endsection
