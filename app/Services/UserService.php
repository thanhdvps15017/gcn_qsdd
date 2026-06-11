<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllWithRoles();
    }

    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = $this->userRepository->create($data);
        $user->syncRoles([$data['role']]);
        
        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $this->userRepository->update($user, $data);
        $user->syncRoles([$data['role']]);
        
        return $user;
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            throw new Exception('Không thể tự xoá chính mình');
        }

        if ($user->hasRole('superadmin')) {
            throw new Exception('Không thể xoá superadmin');
        }

        return $this->userRepository->delete($user);
    }
}
