<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * UserManagementController — Admin-exclusive user administration.
 *
 * Provides:
 *  - index()   → List all users with their roles
 *  - edit()    → Show the role/password edit form for a user
 *  - update()  → Change a user's role (and optionally password)
 *  - destroy() → Delete a user (cannot self-delete)
 */
class UserManagementController extends Controller
{
    /**
     * List all users in the system.
     */
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the edit form for a user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update a user's role and optionally their password.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role'     => ['required', Rule::in(['admin', 'manager', 'staff'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $updateData = ['role' => $validated['role']];

        // Only update password if a new one was supplied
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', "User [{$user->name}] has been updated successfully.");
    }

    /**
     * Delete a user. Admin cannot delete their own account.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User [{$name}] has been permanently removed.");
    }
}
