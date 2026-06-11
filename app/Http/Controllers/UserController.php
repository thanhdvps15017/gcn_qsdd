<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Services\UserService;
use Exception;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view('cai-dat.users.index', [
            'users' => $this->userService->getAllUsers(),
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

        $this->userService->createUser($data);

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

        $this->userService->updateUser($user, $data);

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
        try {
            $this->userService->deleteUser($user);
            return back()->with('success', 'Đã xoá user');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
