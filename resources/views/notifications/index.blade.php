@extends('layouts.app')

@section('title', 'Notifications')
@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">My Notifications</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Dashboard
        </a>
        @if($notifications->where('is_read', false)->count() > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-primary shadow-md">
                <i data-lucide="check" class="w-4 h-4 mr-2"></i> Mark All as Read
            </button>
        </form>
        @endif
    </div>
</div>

<div class="intro-y box p-5 mt-5">
    @if($notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notification)
            <div class="flex items-center p-4 border rounded-lg {{ $notification->is_read ? 'bg-gray-50' : ($notification->type === 'deployment_accountable' ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200') }}">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-10 h-10 rounded-full {{ $notification->is_read ? 'bg-gray-300' : ($notification->type === 'deployment_accountable' ? 'bg-yellow-500' : 'bg-blue-500') }} flex items-center justify-center">
                        @if($notification->type === 'deployment_accountable')
                            <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                        @else
                            <i data-lucide="package" class="w-5 h-5 {{ $notification->is_read ? 'text-gray-600' : 'text-white' }}"></i>
                        @endif
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium {{ $notification->is_read ? 'text-gray-600' : ($notification->type === 'deployment_accountable' ? 'text-yellow-900' : 'text-blue-900') }}">
                            {{ $notification->message }}
                        </h3>
                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($notification->deployedItem)
                    <div class="mt-1 text-sm text-gray-600">
                        <p><strong>Item:</strong> {{ $notification->deployedItem->itemName }}</p>
                        @if($notification->data && isset($notification->data['quantity']))
                        <p><strong>Quantity:</strong> {{ $notification->data['quantity'] }}</p>
                        @endif
                        @if($notification->data && isset($notification->data['deployed_by']))
                        <p><strong>Deployed by:</strong> {{ $notification->data['deployed_by'] }}</p>
                        @endif
                        @if($notification->deployedItem->department)
                        <p><strong>Department:</strong> {{ $notification->deployedItem->department->officename }}</p>
                        @endif
                        @if($notification->type === 'deployment_accountable' && isset($notification->data['deployment_value']))
                        <p><strong>Deployment Value:</strong> ₱{{ number_format($notification->data['deployment_value'], 2) }}</p>
                        @endif
                    </div>
                    @endif
                    
                    <div class="mt-2 flex space-x-2">
                        @if($notification->deployedItem)
                        <a href="{{ route('deployed-items.show', $notification->deployed_item_id) }}" 
                           class="text-xs text-blue-600 hover:text-blue-800 underline">
                            View Item Details
                        </a>
                        @endif
                        
                        @if(!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-green-600 hover:text-green-800 underline">
                                Mark as Read
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                
                @if(!$notification->is_read)
                <div class="flex-shrink-0 ml-4">
                    @if($notification->type === 'deployment_accountable')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Accountable Person
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            New
                        </span>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i data-lucide="bell-off" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
            <p class="text-gray-500">You don't have any notifications at the moment.</p>
        </div>
    @endif
</div>
@endsection
