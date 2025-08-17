@extends('layouts.app')

@section('title', 'Deployed Item Details')

@section('content')
<div class="mt-8">
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Deployed Item Details</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <button type="button" class="btn btn-secondary" onclick="showQRCode('{{ $deployedItem->qrCode }}')">
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
                                <img src="{{ $deployedItem->qr_code_image_url }}" alt="QR Code" class="w-32 h-32 border rounded">
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

        <!-- QR Code Modal -->
        <div class="modal" id="qr-modal">
            <div class="modal__content">
                <div class="p-5 text-center">
                    <div id="qrcode" class="mx-auto"></div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary mt-3" onclick="closeModal()">Close</button>
                        <button type="button" class="btn btn-primary mt-3" onclick="printQRCode()">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                        // Reload the page to show the new QR code image
                        window.location.reload();
                    } else {
                        alert('Failed to generate QR code image: ' + data.message);
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to generate QR code image. Please try again.');
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }

            function showQRCode(qrData) {
                // Check if we have a stored QR code image
                @if($deployedItem->qr_code_image)
                    // Use the stored image
                    const qrImage = '{{ $deployedItem->qr_code_image_url }}';
                    document.getElementById('qrcode').innerHTML = `<img src="${qrImage}" alt="QR Code" class="w-full h-full">`;
                @else
                    // Generate QR code on the fly
                    document.getElementById('qrcode').innerHTML = '';
                    new QRCode(document.getElementById("qrcode"), {
                        text: qrData,
                        width: 200,
                        height: 200,
                        colorDark : "#000000",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                @endif
                
                document.getElementById('qr-modal').classList.add('show');
            }
            
            function closeModal() {
                document.getElementById('qr-modal').classList.remove('show');
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
