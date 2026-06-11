<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\RolePermissionService;

class RolePermissionController extends Controller
{
    protected $service;

    public function __construct(RolePermissionService $service) {
        $this->service = $service;
    }

    public function index() {
        $roles = $this->service->getAllRolesWithPermissions();
        $permissions = $this->service->getAllPermissions();
        return view('cai-dat.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:roles,name']);
        $this->service->createRole(['name' => $request->name]);
        return back()->with('success', 'Tạo role thành công');
    }

    public function edit(Role $role) {
        $permissions = $this->service->getAllPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('cai-dat.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role) {
        $request->validate(['name' => 'required']);
        $permissions = array_filter($request->permissions ?? []);
        $this->service->updateRole($role, ['name' => $request->name], $permissions);
        return back()->with('success', 'Cập nhật role + permission thành công');
    }

    public function destroy(Role $role) {
        $this->service->deleteRole($role);
        return back()->with('success', 'Xoá role thành công');
    }

    public function assignPermission(Request $request, Role $role) {
        $permissions = array_filter($request->permissions ?? []);
        $this->service->assignPermission($role, $permissions);
        return back()->with('success', 'Gán quyền cho role thành công');
    }

    public function userRoles(User $user) {
        $roles = $this->service->getAllRoles();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('cai-dat.roles.user_roles', compact('user', 'roles', 'userRoles'));
    }

    public function assignUserRole(Request $request, User $user) {
        $roles = array_filter($request->roles ?? []);
        $this->service->assignUserRole($user, $roles);
        return back()->with('success', 'Gán role cho user thành công');
    }
}
