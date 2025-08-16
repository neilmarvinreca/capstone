<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeployedItemController;
use App\Http\Controllers\DeploymentRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\RoleMiddleware;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Password Reset Routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Deployment Requests
    Route::resource('deployment-requests', DeploymentRequestController::class)->only(['index', 'show', 'store', 'update']);
    Route::get('deployment-requests/approvers', [DeploymentRequestController::class, 'getApprovers'])->name('deployment-requests.approvers');
    
    // Deployed Items - Archive routes must be defined before the resource route
    Route::prefix('deployed-items')->name('deployed-items.')->group(function () {
        Route::get('archived', [DeployedItemController::class, 'archived'])->name('archived');
        Route::put('{id}/archive', [DeployedItemController::class, 'archive'])->name('archive');
        Route::post('{id}/restore', [DeployedItemController::class, 'restore'])->name('restore');
        Route::delete('{id}/force-delete', [DeployedItemController::class, 'forceDelete'])->name('force-delete');
    });
    
    // Deployed Items Resource Route
    Route::resource('deployed-items', DeployedItemController::class);
    
    // Archive routes for supplies - Placing these before the resource route to avoid conflicts
    Route::get('supplies/archived', [SupplyController::class, 'archived'])->name('supplies.archived');
    Route::put('supplies/{supply}/archive', [SupplyController::class, 'archive'])->name('supplies.archive');
    Route::put('supplies/{supply}/restore', [SupplyController::class, 'restore'])->name('supplies.restore');
    Route::delete('supplies/{supply}/force-delete', [SupplyController::class, 'forceDelete'])->name('supplies.force-delete');
    
    // Deployment form route with simpler path
    Route::get('deploy-supplies', [SupplyController::class, 'deployForm'])->name('supplies.deploy');
    Route::post('deploy-supplies', [SupplyController::class, 'deploy'])->name('supplies.deploy.submit');
    
    // Supplies Resource Route
    Route::resource('supplies', SupplyController::class);
    
    // Restock route
    Route::post('supplies/{supply}/restock', [SupplyController::class, 'restock'])->name('supplies.restock');

    // Archive routes - Placing these before the resource route to avoid conflicts
    Route::get('departments/archived', [DepartmentController::class, 'archived'])->name('departments.archived');
    Route::get('categories/archived', [CategoryController::class, 'archived'])->name('categories.archived');
    Route::put('categories/{category}/archive', [CategoryController::class, 'archive'])->name('categories.archive');
    Route::put('categories/{category}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    
    // Categories - Using route model binding with explicit parameter name
    Route::resource('categories', CategoryController::class)->parameters([
        'categories' => 'category'
    ]);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/deployed-items', [ReportController::class, 'deployedItems'])->name('reports.deployed-items');
    Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
    Route::get('reports/export/{type}', [ReportController::class, 'export'])
        ->whereIn('type', ['inventory', 'low-stock', 'deployed-items'])
        ->name('reports.export');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications'])->name('index');
        Route::patch('{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::patch('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Users Management Routes
    Route::prefix('users')->name('users.')->middleware('admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/archived', [UserController::class, 'archived'])->name('archived');
        Route::put('/{user}/archive', [UserController::class, 'archive'])->name('archive');
        Route::put('/{user}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{user}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
    });

    // Departments
    Route::resource('departments', DepartmentController::class);
    Route::get('departments/archived', [DepartmentController::class, 'archived'])->name('departments.archived');
    Route::put('departments/{department}/archive', [DepartmentController::class, 'archive'])->name('departments.archive');
    Route::put('departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
    Route::delete('departments/{department}/force-delete', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
    // Routes for Super Admin only
    Route::middleware(['auth', \App\Http\Middleware\CheckRoleMiddleware::class . ':Super Admin'])->group(function () {
        // User management
        Route::get('users/archived', [UserController::class, 'archived'])->name('users.archived');
        Route::put('users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');
        Route::put('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
        Route::resource('users', UserController::class);
    });
    
    // Departments - Accessible to both Super Admin and Inventory Manager
    Route::middleware(['auth', \App\Http\Middleware\CheckRoleMiddleware::class . ':Inventory Manager,Super Admin'])->group(function () {
        Route::get('departments/archived', [DepartmentController::class, 'archived'])->name('departments.archived');
        Route::put('departments/{department}/archive', [DepartmentController::class, 'archive'])->name('departments.archive');
        Route::put('departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
        Route::delete('departments/{department}/force-delete', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
        Route::resource('departments', DepartmentController::class);
    });
});

// Redirect root to login if not authenticated
Route::get('/', function () {
    return redirect()->route('login');
});
