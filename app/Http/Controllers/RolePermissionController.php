<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $request->name]);

        return back()->with('success', 'Tạo role thành công');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required']);
        $role->update(['name' => $request->name]);

        $permissions = array_filter($request->permissions ?? []);
        $role->syncPermissions($permissions);

        return back()->with('success', 'Cập nhật role + permission thành công');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Xoá role thành công');
    }

    public function assignPermission(Request $request, Role $role)
    {
        $permissions = array_filter($request->permissions ?? []);
        $role->syncPermissions($permissions);

        return back()->with('success', 'Gán quyền cho role thành công');
    }

    public function userRoles(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('roles.user_roles', compact('user', 'roles', 'userRoles'));
    }

    public function assignUserRole(Request $request, User $user)
    {
        $roles = array_filter($request->roles ?? []);
        $user->syncRoles($roles);

        return back()->with('success', 'Gán role cho user thành công');
    }
}
