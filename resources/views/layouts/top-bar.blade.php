<!-- BEGIN: Top Bar -->
<div class="top-bar-boxed h-[70px] z-[51] relative border-b border-white/[0.08] -mx-3 sm:-mx-8 px-3 sm:px-8 md:pt-0">
    <div class="h-full flex items-center justify-between">
        <!-- BEGIN: Logo -->
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}" class="-intro-x flex items-center">
                <img alt="DSSC Logo" class="w-60" src="http://202.137.126.204:4455/uploads/settings/1_theG1690183835.gif">
                <span class="text-white text-lg ml-3">Supply and Property Inventory Management System</span>
            </a>
        </div>
        <!-- END: Logo -->

        <!-- BEGIN: Breadcrumb -->
        <nav aria-label="breadcrumb" class="hidden sm:flex">
            <ol class="breadcrumb breadcrumb-light">
                <li class="breadcrumb-item"><a href="#">Application</a></li>
                <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
            </ol>
        </nav>
        <!-- END: Breadcrumb -->

        <div class="flex items-center ml-auto">
            <!-- BEGIN: Notifications -->
            @php
                $pendingRequests = \App\Models\DeploymentRequest::with(['deployedItem', 'requester'])
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                $unreadCount = $pendingRequests->count();
                
                // Get deployment notifications for department users
                $deploymentNotifications = [];
                $deploymentNotificationCount = 0;
                $accountablePersonNotificationCount = 0;
                
                if (auth()->user()->role === 'Department User' && auth()->user()->department_id) {
                    $deploymentNotifications = \App\Services\DeploymentNotificationService::getUserNotifications(auth()->id(), 10);
                    $deploymentNotificationCount = \App\Services\DeploymentNotificationService::getUnreadCount(auth()->id());
                    
                    // Count accountable person notifications separately
                    $accountablePersonNotificationCount = $deploymentNotifications
                        ->where('is_read', false)
                        ->where('type', 'deployment_accountable')
                        ->count();
                }
            @endphp
            
            <!-- Deployment Notifications for Department Users -->
            @if(auth()->user()->role === 'Department User' && $deploymentNotificationCount > 0)
            <div class="intro-x dropdown mr-4">
                <div class="dropdown-toggle notification notification--bullet cursor-pointer" 
                    role="button" 
                    aria-expanded="false" 
                    data-tw-toggle="dropdown">
                    <i data-lucide="package" class="w-5 h-5 dark:text-slate-500"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-success rounded-full"></span>
                </div>
                <div class="dropdown-menu w-80">
                    <div class="dropdown-content bg-success border border-slate-200/60 rounded-md shadow-lg">
                        <div class="p-3 border-b border-slate-200/60">
                            <div class="flex items-center justify-between">
                                <h2 class="font-medium text-base text-white">New Deployments</h2>
                                <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs text-white">{{ $deploymentNotificationCount }} New</span>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto">
                            @forelse($deploymentNotifications as $notification)
                                @if(!$notification->is_read)
                                <a href="{{ route('deployed-items.show', $notification->deployed_item_id) }}" 
                                   class="block p-3 hover:bg-white/5 transition duration-300 ease-in-out"
                                   onclick="markNotificationAsRead({{ $notification->id }})">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 text-white mr-3">
                                            @if($notification->type === 'deployment_accountable')
                                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                            @else
                                                <i data-lucide="package" class="w-4 h-4"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-medium text-white">{{ $notification->deployedItem->itemName ?? 'Item' }}</h3>
                                                <span class="text-xs text-white/60">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-white/80 text-xs mt-0.5">
                                                @if($notification->type === 'deployment_accountable')
                                                    <span class="bg-yellow-500/20 text-yellow-300 text-xs px-2 py-0.5 rounded-full mr-2">Accountable Person</span>
                                                    Deployed by {{ $notification->data['deployed_by'] ?? 'System' }}
                                                @else
                                                    Deployed by {{ $notification->data['deployed_by'] ?? 'System' }}
                                                @endif
                                            </p>
                                            <div class="flex items-center mt-1">
                                                <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">
                                                    Quantity: {{ $notification->data['quantity'] ?? 1 }}
                                                </span>
                                                @if($notification->type === 'deployment_accountable' && isset($notification->data['deployment_value']))
                                                <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full ml-2">
                                                    Value: ₱{{ number_format($notification->data['deployment_value'], 2) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endif
                            @empty
                                <div class="p-4 text-center">
                                    <i data-lucide="package" class="w-8 h-8 text-white/60 mx-auto mb-2"></i>
                                    <p class="text-white/60 text-sm">No new deployments</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-slate-200/60">
                            <a href="{{ route('deployed-items.index') }}" class="block text-center text-white text-sm font-medium hover:underline">
                                View All Deployments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Accountable Person Notifications (Special) -->
            @if(auth()->user()->role === 'Department User' && $accountablePersonNotificationCount > 0)
            <div class="intro-x dropdown mr-4">
                <div class="dropdown-toggle notification notification--bullet cursor-pointer" 
                    role="button" 
                    aria-expanded="false" 
                    data-tw-toggle="dropdown">
                    <i data-lucide="shield-check" class="w-5 h-5 dark:text-slate-500"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-warning rounded-full"></span>
                </div>
                <div class="dropdown-menu w-80">
                    <div class="dropdown-content bg-warning border border-slate-200/60 rounded-md shadow-lg">
                        <div class="p-3 border-b border-slate-200/60">
                            <div class="flex items-center justify-between">
                                <h2 class="font-medium text-base text-white">Accountable Person Alerts</h2>
                                <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs text-white">{{ $accountablePersonNotificationCount }} New</span>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto">
                            @forelse($deploymentNotifications->where('type', 'deployment_accountable')->where('is_read', false) as $notification)
                                <a href="{{ route('deployed-items.show', $notification->deployed_item_id) }}" 
                                   class="block p-3 hover:bg-white/5 transition duration-300 ease-in-out"
                                   onclick="markNotificationAsRead({{ $notification->id }})">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 text-white mr-3">
                                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-medium text-white">{{ $notification->deployedItem->itemName ?? 'Item' }}</h3>
                                                <span class="text-xs text-white/60">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-white/80 text-xs mt-0.5">
                                                <span class="bg-yellow-500/20 text-yellow-300 text-xs px-2 py-0.5 rounded-full mr-2">Accountable Person</span>
                                                Deployed by {{ $notification->data['deployed_by'] ?? 'System' }}
                                            </p>
                                            <div class="flex items-center mt-1">
                                                <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">
                                                    Quantity: {{ $notification->data['quantity'] ?? 1 }}
                                                </span>
                                                @if(isset($notification->data['deployment_value']))
                                                <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full ml-2">
                                                    Value: ₱{{ number_format($notification->data['deployment_value'], 2) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center">
                                    <i data-lucide="shield-check" class="w-8 h-8 text-white/60 mx-auto mb-2"></i>
                                    <p class="text-white/60 text-sm">No accountable person alerts</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-slate-200/60">
                            <a href="{{ route('deployed-items.index') }}" class="block text-center text-white text-sm font-medium hover:underline">
                                View All Deployments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Deployment Requests for Inventory Managers -->
            @if(auth()->user()->role === 'Inventory Manager' || auth()->user()->role === 'Super Admin')
            <div class="intro-x dropdown mr-4">
                <div class="dropdown-toggle notification {{ $unreadCount > 0 ? 'notification--bullet' : '' }} cursor-pointer" 
                    role="button" 
                    aria-expanded="false" 
                    data-tw-toggle="dropdown">
                    <i data-lucide="bell" class="w-5 h-5 dark:text-slate-500"></i>
                    @if($unreadCount > 0)
                        <span class="absolute top-0 right-0 w-2 h-2 bg-danger rounded-full"></span>
                    @endif
                </div>
                <div class="dropdown-menu w-80">
                    <div class="dropdown-content bg-primary border border-slate-200/60 rounded-md shadow-lg">
                        <div class="p-3 border-b border-slate-200/60">
                            <div class="flex items-center justify-between">
                                <h2 class="font-medium text-base text-white">Deployment Requests</h2>
                                @if($unreadCount > 0)
                                    <span class="bg-danger rounded-full px-2 py-0.5 text-xs text-white">{{ $unreadCount }} New</span>
                                @endif
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto">
                            @forelse($pendingRequests as $request)
                                <a href="{{ route('deployment-requests.show', $request->requestID) }}" class="block p-3 hover:bg-white/5 transition duration-300 ease-in-out">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-primary/20 text-primary mr-3">
                                            <i data-lucide="package" class="w-4 h-4"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-medium text-white">{{ $request->deployedItem->itemName ?? 'Item' }}</h3>
                                                <span class="text-xs text-slate-400">{{ $request->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-slate-300 text-xs mt-0.5">
                                                Requested by {{ $request->requester->name ?? 'User' }}
                                            </p>
                                            <div class="flex items-center mt-1">
                                                <span class="bg-warning/10 text-warning text-xs px-2 py-0.5 rounded-full">
                                                    {{ ucfirst($request->requestType) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center">
                                    <i data-lucide="bell-off" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                                    <p class="text-slate-400 text-sm">No new deployment requests</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-slate-200/60">
                            <a href="{{ route('deployment-requests.index') }}" class="block text-center text-primary text-sm font-medium hover:underline">
                                View All Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- END: Notifications -->

            <!-- BEGIN: Account Menu -->
            <div class="intro-x dropdown">
                <div class="dropdown-toggle flex items-center" role="button" aria-expanded="false" data-tw-toggle="dropdown">
                    <div class="w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in">
                        <img alt="{{ auth()->user()->name }}" src="{{ asset('dist/images/logodssc.png') }}">
                    </div>
                    <div class="hidden sm:block ml-3 text-white">
                        <div class="font-medium">{{ auth()->user()->name }}</div>
                </div>      
            </div>
            <div class="dropdown-menu w-56">
                <ul class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                    <li class="p-2">
                        <div class="font-medium">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">{{ auth()->user()->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider border-white/[0.08]"></li>
                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item hover:bg-white/5">
                            <i data-lucide="user" class="w-4 h-4 mr-2"></i> Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider border-white/[0.08]"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item hover:bg-white/5 w-full text-left">
                                <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <!-- END: Account Menu -->
    </div>
</div>
<!-- END: Top Bar -->

<script>
    function markNotificationAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the notification from the UI or mark it as read
                const notificationElement = document.querySelector(`[onclick="markNotificationAsRead(${notificationId})"]`);
                if (notificationElement) {
                    notificationElement.style.opacity = '0.5';
                    notificationElement.style.pointerEvents = 'none';
                }
                
                // Update the notification count
                updateNotificationCount();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    function updateNotificationCount() {
        // Refresh the page to update notification counts
        // Alternatively, you could make an AJAX call to get updated counts
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
</script>