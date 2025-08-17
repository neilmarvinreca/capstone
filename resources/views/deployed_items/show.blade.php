@extends('layouts.app')

@section('title', 'Deployed Item Details')

@push('styles')
<style>
    .qr-code-image {
        width: 128px;
        height: 128px;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .qr-code-image:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div class="mt-8">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Deployed Item Details</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <button type="button" class="btn btn-secondary" onclick="showQRCodeModal()">
                <i data-lucide="qrcode" class="w-4 h-4 mr-2"></i> Show QR Code
            </button>
            <a href="{{ route('deployed-items.index') }}" class="btn btn-outline-secondary ml-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Overview
            </a>
        </div>
    </div>

    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <!-- Item Information -->
            <div class="col-span-12 md:col-span-6">
                <div class="border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="text-lg font-medium">Item Information</h2>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <div class="text-slate-500">Item Name</div>
                        <div class="font-medium text-lg">{{ $deployedItem->itemName }}</div>
                    </div>
                
                    <div class="md:col-span-2">
                        <div class="text-slate-500">Description</div>
                        <div class="font-medium">{{ $deployedItem->itemDescription ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Category</div>
                        <div class="font-medium">{{ $deployedItem->itemCategory }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Date Acquired</div>
                        <div class="font-medium">{{ optional($deployedItem->dateAcquired)->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Cost</div>
                        <div class="font-medium">₱{{ number_format($deployedItem->cost, 2) }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Status</div>
                        <div>
                            <span class="px-2 py-1 rounded-full text-xs {{ 
                                $deployedItem->status === 'active' ? 'bg-success text-white' : 'bg-warning text-white' 
                            }}">
                                {{ ucfirst($deployedItem->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="text-slate-500">QR Code</div>
                        <div class="font-mono text-xs bg-slate-100 dark:bg-darkmode-800 p-2 rounded inline-block">{{ $deployedItem->qrCode }}</div>
                        @if($deployedItem->qr_code_image)
                            <div class="mt-2">
                                <img src="{{ $deployedItem->qr_code_image_url }}" 
                                     alt="QR Code" 
                                     class="qr-code-image cursor-pointer" 
                                     onclick="showQRCodeModal()" 
                                     title="Click to view larger"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="hidden mt-2">
                                    <div class="text-sm text-red-500 mb-2">QR Code image not found in storage</div>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="generateQrCodeImage()">
                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Regenerate QR Image
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-primary" onclick="generateQrCodeImage()">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Generate QR Image
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Horizontal Line -->
            <div class="col-span-12">
                <hr class="border-slate-200/60 dark:border-darkmode-400 my-6">
            </div>
        
            <!-- Deployment Information -->
            <div class="col-span-12 md:col-span-6">
                <div class="border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="text-lg font-medium">Deployment Information</h2>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-slate-500">Deployment ID</div>
                        <div class="font-medium">{{ $deployedItem->deployedID }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Department</div>
                        <div class="font-medium">{{ optional($deployedItem->department)->officename ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Date Deployed</div>
                        <div class="font-medium">{{ optional($deployedItem->dateDeployed)->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-slate-500">Deployed By</div>
                        <div class="font-medium">{{ optional($deployedItem->deployedBy)->name ?? 'System' }}</div>
                    </div>
                    
                    @if($deployedItem->checkedBy)
                    <div>
                        <div class="text-slate-500">Checked By</div>
                        <div class="font-medium">{{ $deployedItem->checkedBy->name }}</div>
                    </div>
                    @endif
                    
                    @if($deployedItem->remarks)
                    <div class="md:col-span-2">
                        <div class="text-slate-500">Remarks</div>
                        <div class="font-medium bg-slate-50 dark:bg-darkmode-700 p-3 rounded">{{ $deployedItem->remarks }}</div>
                    </div>
                    @endif
                    
                    <div class="md:col-span-2 pt-4 border-t border-slate-200/60 dark:border-darkmode-400 mt-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-slate-500">Created At</div>
                                <div class="font-medium">{{ $deployedItem->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Last Updated</div>
                                <div class="font-medium">{{ $deployedItem->updated_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Activity Log -->
        @if($deployedItem->activities->count() > 0)
        <div class="mt-8">
            <div class="border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                <h2 class="text-lg font-medium">Activity Log</h2>
            </div>
        
            <div class="relative">
                <div class="absolute left-5 top-0 h-full border-l-2 border-slate-200 dark:border-darkmode-400"></div>
                
                @foreach($deployedItem->activities as $activity)
                <div class="relative mb-6 ml-10">
                    
                    <div class="bg-slate-50 dark:bg-darkmode-600 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="font-medium">{{ $activity->description }}</div>
                            <div class="text-xs text-slate-500">{{ $activity->created_at->diffForHumans() }}</div>
                        </div>
                        @if($activity->properties->has('attributes'))
                            <div class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                @foreach($activity->properties['attributes'] as $key => $value)
                                    @if(!in_array($key, ['updated_at', 'created_at', 'deleted_at']))
                                        <div class="grid grid-cols-3 gap-2 py-1">
                                            <div class="col-span-1 font-medium">{{ str_replace('_', ' ', ucfirst($key)) }}:</div>
                                            <div class="col-span-2">{{ $value ?? 'N/A' }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            function generateQrCodeImage() {
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Generating...';
                button.disabled = true;

                // Make AJAX request to generate QR code image
                fetch(`{{ route('deployed-items.generate-qr', $deployedItem) }}`, {
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
                        Swal.fire({
                            title: 'Success!',
                            text: 'QR Code image generated successfully',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Reload the page to show the new QR code image
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to generate QR code image: ' + data.message,
                            icon: 'error'
                        });
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to generate QR code image. Please try again.',
                        icon: 'error'
                    });
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }

            function showQRCodeModal() {
                // Check if we have a stored QR code image
                @if($deployedItem->qr_code_image)
                    // Use the stored image
                    const qrImageUrl = '{{ $deployedItem->qr_code_image_url }}';
                    if (qrImageUrl) {
                        showQrCodeModalWithImage(qrImageUrl);
                    } else {
                        // Fall back to generating QR code on the fly
                        showQrCodeModalGenerated();
                    }
                @else
                    // Generate QR code on the fly
                    showQrCodeModalGenerated();
                @endif
            }
            
            function showQrCodeModalWithImage(qrImageUrl) {
                // First check if the image actually exists
                const img = new Image();
                img.onload = function() {
                    // Image exists, show modal
                    Swal.fire({
                        title: 'QR Code for {{ $deployedItem->itemName }}',
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <img src="${qrImageUrl}" 
                                         alt="QR Code" 
                                         class="mx-auto border rounded" 
                                         style="width: 200px; height: 200px; object-fit: contain;">
                                </div>
                                <div class="text-sm text-gray-600 mb-2">QR Code Text:</div>
                                <div class="font-mono text-xs bg-gray-100 p-2 rounded inline-block">{{ $deployedItem->qrCode }}</div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#3085d6',
                        width: '400px'
                    });
                };
                img.onerror = function() {
                    // Image doesn't exist, show error and offer to regenerate
                    Swal.fire({
                        title: 'QR Code Image Not Found',
                        text: 'The stored QR code image could not be loaded. Would you like to regenerate it?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Regenerate',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            generateQrCodeImage();
                        }
                    });
                };
                img.src = qrImageUrl;
            }
            
            function showQrCodeModalGenerated() {
                Swal.fire({
                    title: 'QR Code for {{ $deployedItem->itemName }}',
                    html: `
                        <div class="text-center">
                            <div class="mb-4" id="qrcode-container">
                                <div id="qrcode"></div>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">QR Code Text:</div>
                            <div class="font-mono text-xs bg-gray-100 p-2 rounded inline-block">{{ $deployedItem->qrCode }}</div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#3085d6',
                    width: '400px',
                    didOpen: () => {
                        // Generate QR code after modal opens
                        document.getElementById('qrcode').innerHTML = '';
                        new QRCode(document.getElementById("qrcode"), {
                            text: '{{ $deployedItem->qrCode }}',
                            width: 200,
                            height: 200,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    }
                });
            }
            
            function printQRCode() {
                const printWindow = window.open('', '_blank');
                const qrCodeContent = document.getElementById('qrcode').innerHTML;
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>QR Code - {{ $deployedItem->itemName }}</title>
                        <style>
                            body { text-align: center; padding: 20px; }
                            .qrcode { margin: 0 auto; }
                            .item-name { font-size: 18px; font-weight: bold; margin: 10px 0; }
                            .item-code { font-family: monospace; margin: 10px 0; }
                            @media print {
                                @page { margin: 0; }
                                body { padding: 15mm; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="item-name">{{ $deployedItem->itemName }}</div>
                        <div class="item-code">{{ $deployedItem->qrCode }}</div>
                        <div class="qrcode">${qrCodeContent}</div>
                        <script>window.onload = () => window.print()<\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            }
        </script>
        @endpush
    </div>
@endsection
