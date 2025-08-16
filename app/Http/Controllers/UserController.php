<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user has permission to view users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        // Get all users except superadmin
        $users = User::where('role', '!=', 'Super Admin')
                     ->orderBy('name')
                     ->paginate(10);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This is handled by the registration process
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Check if user has permission to edit users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Check if user has permission to update users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:Inventory Manager,Inspector,Department User',
        ]);

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if user has permission to delete users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('users.index')
                ->with('error', 'You do not have permission to access this area.');
        }

        // Prevent deletion of superadmin
        if ($user->role === 'Super Admin') {
            return redirect()
                ->route('users.index')
                ->with('error', 'Cannot delete Super Admin user.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Archive the specified user.
     */
    public function archive(User $user)
    {
        // Check if user has permission to archive users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('users.index')
                ->with('error', 'You do not have permission to access this area.');
        }

        // Prevent archiving of superadmin
        if ($user->role === 'Super Admin') {
            return redirect()
                ->route('users.index')
                ->with('error', 'Cannot archive Super Admin user.');
        }

        $user->delete(); // This will soft delete if the model uses SoftDeletes

        return redirect()
            ->route('users.index')
            ->with('success', 'User archived successfully.');
    }

    /**
     * Display a listing of archived users.
     */
    public function archived()
    {
        // Check if user has permission to view archived users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        $users = User::onlyTrashed()
                     ->orderBy('deleted_at', 'desc')
                     ->paginate(10);
        
        return view('users.archived', compact('users'));
    }

    /**
     * Restore the specified archived user.
     */
    public function restore($id)
    {
        // Check if user has permission to restore users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('users.index')
                ->with('error', 'You do not have permission to access this area.');
        }

        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            
            return redirect()
                ->route('users.archived')
                ->with('success', 'User restored successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('users.archived')
                ->with('error', 'An error occurred while restoring the user.');
        }
    }

    /**
     * Permanently delete the specified archived user.
     */
    public function forceDelete($id)
    {
        // Check if user has permission to permanently delete users
        if (!Auth::user() || !in_array(Auth::user()->role, ['Super Admin', 'Inventory Manager'])) {
            return redirect()->route('users.index')
                ->with('error', 'You do not have permission to access this area.');
        }

        try {
            $user = User::onlyTrashed()->findOrFail($id);
            
            // Prevent permanent deletion of superadmin
            if ($user->role === 'Super Admin') {
                return redirect()
                    ->route('users.archived')
                    ->with('error', 'Cannot permanently delete Super Admin user.');
            }
            
            $user->forceDelete();
            
            return redirect()
                ->route('users.archived')
                ->with('success', 'User permanently deleted.');
        } catch (\Exception $e) {
            return redirect()
                ->route('users.archived')
                ->with('error', 'An error occurred while permanently deleting the user.');
        }
    }
}
