<?php

namespace App\Repositories;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionRepository
{
    public function getAllRolesWithPermissions() {
        return Role::with('permissions')->get();
    }
    public function getAllPermissions() {
        return Permission::orderBy('name')->get();
    }
    public function createRole(array $data) {
        return Role::create($data);
    }
    public function updateRole(Role $role, array $data) {
        return $role->update($data);
    }
    public function deleteRole(Role $role) {
        return $role->delete();
    }
    public function getAllRoles() {
        return Role::all();
    }
}
