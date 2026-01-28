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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
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

    /* ================= Dashboard ================= */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('can:dashboard');

    /* ================= Hồ sơ ================= */
    Route::prefix('ho-so')->name('ho-so.')->group(function () {

        Route::get('/', [HoSoController::class, 'index'])
            ->name('index')
            ->middleware('can:ho-so.index');

        Route::get('/create', [HoSoController::class, 'create'])
            ->name('create')
            ->middleware('can:ho-so.create');

        Route::post('/', [HoSoController::class, 'store'])
            ->name('store')
            ->middleware('can:ho-so.store');

        Route::get('/{hoSo}', [HoSoController::class, 'show'])
            ->name('show')
            ->middleware('can:ho-so.show');

        Route::get('/{hoSo}/edit', [HoSoController::class, 'edit'])
            ->name('edit')
            ->middleware('can:ho-so.edit');

        Route::put('/{hoSo}', [HoSoController::class, 'update'])
            ->name('update')
            ->middleware('can:ho-so.update');

        Route::delete('/{hoSo}', [HoSoController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:ho-so.destroy');

        Route::post('/{hoSo}/ghi-chu', [HoSoController::class, 'saveGhiChu'])
            ->name('save-ghi-chu')
            ->middleware('can:ho-so.save-ghi-chu');

        Route::delete('/{hoSo}/files/{hoSoFile}', [HoSoController::class, 'destroyFile'])
            ->name('files.destroy')
            ->middleware('can:ho-so.files.destroy');

        Route::patch('/{hoSo}/trang-thai', [HoSoController::class, 'updateTrangThai'])
            ->name('update-trang-thai')
            ->middleware('can:ho-so.update-trang-thai');
    });

    /* ================= Sổ theo dõi ================= */
    Route::prefix('so-theo-doi')->name('so-theo-doi.')->group(function () {

        Route::get('/', [SoTheoDoiController::class, 'index'])
            ->name('index')
            ->middleware('can:so-theo-doi.index');

        Route::get('/create', [SoTheoDoiController::class, 'create'])
            ->name('create')
            ->middleware('can:so-theo-doi.create');

        Route::post('/', [SoTheoDoiController::class, 'store'])
            ->name('store')
            ->middleware('can:so-theo-doi.store');

        Route::get('/{group}', [SoTheoDoiController::class, 'show'])
            ->name('show')
            ->middleware('can:so-theo-doi.show');

        Route::get('/{group}/edit', [SoTheoDoiController::class, 'edit'])
            ->name('edit')
            ->middleware('can:so-theo-doi.edit');

        Route::put('/{group}', [SoTheoDoiController::class, 'update'])
            ->name('update')
            ->middleware('can:so-theo-doi.update');

        Route::delete('/{group}', [SoTheoDoiController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:so-theo-doi.destroy');

        Route::post('/{group}/batch-add', [SoTheoDoiController::class, 'batchAdd'])
            ->name('batch-add')
            ->middleware('can:so-theo-doi.batch-add');

        Route::post('/{group}/batch-remove', [SoTheoDoiController::class, 'batchRemove'])
            ->name('batch-remove')
            ->middleware('can:so-theo-doi.batch-remove');

        Route::get('/{group}/export-excel', [SoTheoDoiController::class, 'exportExcel'])
            ->name('export-excel')
            ->middleware('can:so-theo-doi.export-excel');

        Route::get('/{group}/export-word', [SoTheoDoiController::class, 'exportWord'])
            ->name('export-word')
            ->middleware('can:so-theo-doi.export-word');

        Route::get('/{group}/search-chua-them', [SoTheoDoiController::class, 'searchHoSoChuaThem'])
            ->name('search-chua-them')
            ->middleware('can:so-theo-doi.search-chua-them');

        Route::get('/{group}/search-trong-so', [SoTheoDoiController::class, 'searchHoSoTrongSo'])
            ->name('search-trong-so')
            ->middleware('can:so-theo-doi.search-trong-so');
    });

    /* ================= Xuất file ================= */
    Route::prefix('xuat-excel')->name('xuat-excel.')->group(function () {
        Route::get('/', [XuatExcelController::class, 'index'])
            ->name('index')
            ->middleware('can:xuat-excel.index');

        Route::get('/export', [XuatExcelController::class, 'export'])
            ->name('export')
            ->middleware('can:xuat-excel.export');
    });

    Route::prefix('xuat-word')->name('xuat-word.')->group(function () {
        Route::get('/', [XuatWordController::class, 'index'])
            ->name('index')
            ->middleware('can:xuat-word.index');

        Route::post('/export', [XuatWordController::class, 'export'])
            ->name('export')
            ->middleware('can:xuat-word.export');

        Route::post('/preview', [XuatWordController::class, 'preview'])
            ->name('preview')
            ->middleware('can:xuat-word.preview');
    });

    /* ================= Settings ================= */
    Route::prefix('settings')->group(function () {

        Route::resource('roles', RolePermissionController::class)
            ->except(['create', 'show'])
            ->middleware('can:roles.index');

        Route::post('roles/{role}/assign-permission', [RolePermissionController::class, 'assignPermission'])
            ->name('roles.assign-permission')
            ->middleware('can:roles.assign-permission');

        Route::get('roles/user/{user}', [RolePermissionController::class, 'userRoles'])
            ->name('roles.user.roles')
            ->middleware('can:roles.user.roles');

        Route::post('roles/user/{user}', [RolePermissionController::class, 'assignUserRole'])
            ->name('roles.user.assign')
            ->middleware('can:roles.user.assign');

        Route::resource('users', UserController::class)
            ->except(['create', 'edit'])
            ->middleware('can:users.index');

        Route::resource('loai-ho-so', LoaiHoSoController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('can:loai-ho-so.index');

        Route::resource('loai-thu-tuc', LoaiThuTucController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('can:loai-thu-tuc.index');

        Route::resource('xa', XaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('can:xa.index');

        Route::prefix('mau-word')->name('mau-word.')->group(function () {
            Route::get('/', [MauWordController::class, 'index'])
                ->name('index')
                ->middleware('can:mau-word.index');

            Route::post('/upload', [MauWordController::class, 'store'])
                ->name('store')
                ->middleware('can:mau-word.store');

            Route::put('/{mauWord}', [MauWordController::class, 'update'])
                ->name('update')
                ->middleware('can:mau-word.update');

            Route::delete('/{mauWord}', [MauWordController::class, 'destroy'])
                ->name('destroy')
                ->middleware('can:mau-word.destroy');

            Route::delete('/folder/{folder}', [MauWordController::class, 'destroyFolder'])
                ->name('destroy-folder')
                ->middleware('can:mau-word.destroy-folder');
        });

        Route::get('/login-bg', [SettingController::class, 'editLoginBg'])
            ->name('settings.login-bg.edit')
            ->middleware('can:settings.login-bg.edit');

        Route::post('/login-bg', [SettingController::class, 'updateLoginBg'])
            ->name('settings.login-bg.update')
            ->middleware('can:settings.login-bg.update');
    });
});
