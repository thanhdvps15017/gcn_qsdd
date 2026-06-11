<?php

namespace App\Services;
use App\Repositories\RolePermissionRepository;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionService
{
    protected $repo;
    public function __construct(RolePermissionRepository $repo) {
        $this->repo = $repo;
    }
    public function getAllRolesWithPermissions() { return $this->repo->getAllRolesWithPermissions(); }
    public function getAllPermissions() { return $this->repo->getAllPermissions(); }
    public function createRole(array $data) { return $this->repo->createRole($data); }
    public function updateRole(Role $role, array $data, array $permissions) {
        $this->repo->updateRole($role, $data);
        $role->syncPermissions($permissions);
    }
    public function deleteRole(Role $role) { return $this->repo->deleteRole($role); }
    public function assignPermission(Role $role, array $permissions) {
        $role->syncPermissions($permissions);
    }
    public function getAllRoles() { return $this->repo->getAllRoles(); }
    public function assignUserRole(User $user, array $roles) {
        $user->syncRoles($roles);
    }
}
