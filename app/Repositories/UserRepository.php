<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAllWithRoles()
    {
        return User::with('roles')->latest()->get();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }
}
