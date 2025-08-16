<!-- BEGIN: Side Menu -->
@php
    $user = auth()->user();
    $userRole = $user ? $user->role : null;
    $isSuperAdmin = $userRole === 'Super Admin';
    $isInventoryManager = in_array($userRole, ['Inventory Manager', 'Super Admin']);
    $isInspectorOrAbove = in_array($userRole, ['Inspector', 'Inventory Manager', 'Super Admin']);
    $isDepartmentUserOrAbove = in_array($userRole, ['Department User', 'Inspector', 'Inventory Manager', 'Super Admin']);
@endphp

<nav class="side-nav">
    <ul>
        <!-- Dashboard - Visible to all authenticated users -->
        <li>
            <a href="{{ route('dashboard') }}" class="side-menu {{ request()->routeIs('dashboard') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="home"></i>
                </div>
                <div class="side-menu__title">Dashboard</div>
            </a>
        </li>

        <!-- Inventory - Visible to Inspector and above (not Department User) -->
        @if($isInspectorOrAbove)
        <li>
            <a href="{{ route('supplies.index') }}" class="side-menu {{ request()->routeIs('supplies.index', 'supplies.show') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="package"></i>
                </div>
                <div class="side-menu__title">Inventory</div>
            </a>
        </li>
        @endif

        <!-- Categories - Visible to Inventory Manager and above -->
        @if($isInventoryManager)
        <li>
            <a href="{{ route('categories.index') }}" class="side-menu {{ request()->routeIs('categories.*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="tag"></i>
                </div>
                <div class="side-menu__title">Categories</div>
            </a>
        </li>
        @endif

        <!-- Departments - Visible to Super Admin and Inventory Manager -->
        @if($isInventoryManager)
        <li>
            <a href="{{ route('departments.index') }}" class="side-menu {{ request()->routeIs('departments.*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="users"></i>
                </div>
                <div class="side-menu__title">Departments</div>
            </a>
        </li>
        @endif

        <!-- Deployed Items - Visible to Department User and above -->
        @if(hasAnyRole(['Department User', 'Inspector', 'Inventory Manager', 'Super Admin']))
        <li>
            <a href="{{ route('deployed-items.index') }}" class="side-menu {{ request()->routeIs('deployed-items.index', 'deployed-items.show') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="truck"></i>
                </div>
                <div class="side-menu__title">Deployed Items</div>
            </a>
        </li>
        @endif

        <!-- Notifications - Visible to Department User only -->
        @if($userRole === 'Department User')
        <li>
            <a href="{{ route('notifications.index') }}" class="side-menu {{ request()->routeIs('notifications.*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="bell"></i>
                </div>
                <div class="side-menu__title">Notifications</div>
            </a>
        </li>
        @endif

        <!-- Reports - Visible to Inventory Manager and above -->
        @if($isInventoryManager)
        <li>
            <a href="{{ route('reports.index') }}" class="side-menu {{ request()->routeIs('reports.*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="bar-chart-2"></i>
                </div>
                <div class="side-menu__title">Reports</div>
            </a>
        </li>
        @endif

        <!-- Users - Visible to Super Admin only -->
        @if($isSuperAdmin)
        <li>
            <a href="{{ route('users.index') }}" class="side-menu {{ request()->routeIs('users.*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">
                    <i data-lucide="user-check"></i>
                </div>
                <div class="side-menu__title">User Management</div>
            </a>
        </li>
        @endif
    </ul>
</nav>
<!-- END: Side Menu -->