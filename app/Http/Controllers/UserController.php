<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();
        $roles = Role::all();
        return view('super_admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('super_admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->hasRole('super_admin') && $request->role !== 'super_admin' && User::role('super_admin')->count() <= 1) {
            return redirect()->route('super_admin.users.index')->with('error', 'Tidak dapat mengubah role satu-satunya Super Admin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => 'required|exists:roles,name',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();

        $user->syncRoles([$validated['role']]);

        return redirect()->route('super_admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('super_admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
            return redirect()->route('super_admin.users.index')->with('error', 'Tidak dapat menghapus satu-satunya Super Admin.');
        }

        $user->delete();

        return redirect()->route('super_admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
