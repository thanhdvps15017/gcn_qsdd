<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoaiHoSoController;
use App\Http\Controllers\LoaiThuTucController;
use App\Http\Controllers\XaController;
use App\Http\Controllers\HoSoController;
use App\Http\Controllers\SoTheoDoiController;
use App\Http\Controllers\XuatFile\MauWordController;
use App\Http\Controllers\XuatFile\XuatExcelController;
use App\Http\Controllers\XuatFile\XuatWordController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn() => view('_form'))->name('dashboard');

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

    Route::prefix('so-theo-doi')->name('so-theo-doi.')->group(function () {
        Route::get('/', [SoTheoDoiController::class, 'index'])->name('index');
        Route::get('/create', [SoTheoDoiController::class, 'create'])->name('create');
        Route::post('/', [SoTheoDoiController::class, 'store'])->name('store');

        Route::get('/{group}', [SoTheoDoiController::class, 'show'])->name('show');
        Route::get('/{group}/edit', [SoTheoDoiController::class, 'edit'])->name('edit');
        Route::put('/{group}', [SoTheoDoiController::class, 'update'])->name('update');
        Route::delete('/{group}', [SoTheoDoiController::class, 'destroy'])->name('destroy');

        Route::post('/{group}/batch-add', [SoTheoDoiController::class, 'batchAdd'])->name('batch-add');
        Route::post('/{group}/batch-remove', [SoTheoDoiController::class, 'batchRemove'])->name('batch-remove');

        Route::get('/{group}/export-excel', [SoTheoDoiController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{group}/export-word', [SoTheoDoiController::class, 'exportWord'])->name('export-word');
    });

    Route::prefix('xuat-excel')->name('xuat-excel.')->group(function () {
        Route::get('/', [XuatExcelController::class, 'index'])->name('index');
        Route::get('/export', [XuatExcelController::class, 'export'])->name('export');
    });

    Route::prefix('xuat-word')->name('xuat-word.')->group(function () {
        Route::get('/', [XuatWordController::class, 'index'])->name('index');
        Route::post('/export', [XuatWordController::class, 'export'])->name('export');
    });

    Route::prefix('settings')->group(function () {

        Route::resource('roles', RolePermissionController::class)
            ->except(['create', 'show']);

        Route::post(
            'roles/{role}/assign-permission',
            [RolePermissionController::class, 'assignPermission']
        )->name('roles.assign-permission');

        Route::get(
            'roles/user/{user}',
            [RolePermissionController::class, 'userRoles']
        )->name('roles.user.roles');

        Route::post(
            'roles/user/{user}',
            [RolePermissionController::class, 'assignUserRole']
        )->name('roles.user.assign');

        Route::resource('users', UserController::class)
            ->except(['create', 'edit']);

        Route::resource('loai-ho-so', LoaiHoSoController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('loai-thu-tuc', LoaiThuTucController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('xa', XaController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::prefix('mau-word')->name('mau-word.')->group(function () {
            Route::get('/', [MauWordController::class, 'index'])->name('index');
            Route::post('/upload', [MauWordController::class, 'store'])->name('store');
            Route::put('/{mauWord}', [MauWordController::class, 'update'])->name('update');
            Route::delete('/{mauWord}', [MauWordController::class, 'destroy'])->name('destroy');
        });
    });
});
