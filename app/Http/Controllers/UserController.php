<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::with('roles')->latest()->get(),
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|unique:users',
            'name'     => 'nullable',
            'email'    => 'nullable|email',
            'phone'    => 'nullable',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->syncRoles([$request->role]);

        return back()->with('success', 'Tạo user thành công');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'name'     => 'nullable',
            'email'    => 'nullable|email',
            'phone'    => 'nullable',
            'password' => 'nullable|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return back()->with('success', 'Cập nhật thành công');
    }

    public function show(User $user)
    {
        $user->load('roles');

        return response()->json([
            'id'       => $user->id,
            'username' => $user->username,
            'name'     => $user->name,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'role'     => $user->roles->first()->name ?? null,
            'created'  => $user->created_at->format('d/m/Y H:i'),
        ]);
    }


    public function destroy(User $user)
    {
        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'Không thể xoá superadmin');
        }

        $user->delete();
        return back()->with('success', 'Đã xoá user');
    }
}
