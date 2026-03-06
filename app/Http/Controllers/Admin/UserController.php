<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('roles')
            ->orderBy('first_name')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'full_name'  => $u->full_name,
                'username'   => $u->username,
                'email'      => $u->email,
                'cedula'     => $u->cedula,
                'phone'      => $u->phone,
                'role'       => $u->getRoleNames()->first(),
                'created_at' => $u->created_at->format('d/m/Y'),
            ]);

        return Inertia::render('admin/Users/Index', compact('users'));
    }

    public function create(): Response
    {
        $roles = Role::pluck('name');
        return Inertia::render('admin/Users/Create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'cedula'     => 'required|string|max:20|unique:users',
            'phone'      => 'nullable|string|max:15',
            'username'   => 'required|string|max:50|unique:users',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'required|exists:roles,name',
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): Response
    {
        $roles = Role::pluck('name');
        return Inertia::render('admin/Users/Edit', [
            'user'  => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'cedula'     => $user->cedula,
                'phone'      => $user->phone,
                'username'   => $user->username,
                'email'      => $user->email,
                'role'       => $user->getRoleNames()->first(),
            ],
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'cedula'     => "required|string|max:20|unique:users,cedula,{$user->id}",
            'phone'      => 'nullable|string|max:15',
            'username'   => "required|string|max:50|unique:users,username,{$user->id}",
            'email'      => "required|email|unique:users,email,{$user->id}",
            'password'   => 'nullable|string|min:8|confirmed',
            'role'       => 'required|exists:roles,name',
        ]);

        $user->update(array_filter([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'cedula'     => $data['cedula'],
            'phone'      => $data['phone'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => isset($data['password']) ? Hash::make($data['password']) : null,
        ], fn($v) => !is_null($v)));

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propio usuario.']);
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}
