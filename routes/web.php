<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoaiHoSoController;
use App\Http\Controllers\LoaiThuTucController;
use App\Http\Controllers\XaController;
use App\Http\Controllers\HoSoController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', function () {
    return view('_form');
})->middleware('auth')->name('dashboard');

Route::prefix('ho-so')->name('ho-so.')->group(function () {
    Route::get('/', [HoSoController::class, 'index'])->name('index');
    Route::get('/create', [HoSoController::class, 'create'])->name('create');
    Route::post('/', [HoSoController::class, 'store'])->name('store');

    Route::get('/{hoSo}', [HoSoController::class, 'show'])->name('show');

    Route::get('/{hoSo}/edit', [HoSoController::class, 'edit'])->name('edit');

    Route::put('/{hoSo}', [HoSoController::class, 'update'])->name('update');

    Route::delete('/{hoSo}', [HoSoController::class, 'destroy'])->name('destroy');

    Route::patch('/{hoSo}/trang-thai', [HoSoController::class, 'updateTrangThai'])
        ->name('update-trang-thai');
});

Route::prefix('loai-ho-so')->name('loai-ho-so.')->group(function () {
    Route::get('/', [LoaiHoSoController::class, 'index'])->name('index');
    Route::post('/', [LoaiHoSoController::class, 'store'])->name('store');
    Route::put('/{id}', [LoaiHoSoController::class, 'update'])->name('update');
    Route::delete('/{id}', [LoaiHoSoController::class, 'destroy'])->name('destroy');
});


Route::prefix('loai-thu-tuc')->name('loai-thu-tuc.')->group(function () {
    Route::get('/', [LoaiThuTucController::class, 'index'])->name('index');
    Route::post('/', [LoaiThuTucController::class, 'store'])->name('store');
    Route::put('/{id}', [LoaiThuTucController::class, 'update'])->name('update');
    Route::delete('/{id}', [LoaiThuTucController::class, 'destroy'])->name('destroy');
});

Route::prefix('xa')->name('xa.')->group(function () {
    Route::get('/', [XaController::class, 'index'])->name('index');
    Route::post('/', [XaController::class, 'store'])->name('store');
    Route::put('/{id}', [XaController::class, 'update'])->name('update');
    Route::delete('/{id}', [XaController::class, 'destroy'])->name('destroy');
});

Route::prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [RolePermissionController::class, 'index'])->name('index');
    Route::post('/', [RolePermissionController::class, 'store'])->name('store');
    Route::get('/{role}/edit', [RolePermissionController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RolePermissionController::class, 'update'])->name('update');
    Route::delete('/{role}', [RolePermissionController::class, 'destroy'])->name('destroy');

    Route::post('/{role}/assign-permission', [RolePermissionController::class, 'assignPermission'])->name('assign-permission');

    Route::get('/user/{user}/roles', [RolePermissionController::class, 'userRoles'])->name('user.roles');
    Route::post('/user/{user}/roles', [RolePermissionController::class, 'assignUserRole'])->name('user.assign-role');
});

Route::middleware('auth')->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
});
