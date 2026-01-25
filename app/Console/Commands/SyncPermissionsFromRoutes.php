<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class SyncPermissionsFromRoutes extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Tự động tạo permission từ route name';

    public function handle()
    {
        $routes = Route::getRoutes();
        $created = 0;

        foreach ($routes as $route) {
            $name = $route->getName();

            if (!$name) {
                continue;
            }

            // ❗ Chỉ sync route cần phân quyền
            if (! $this->isPermissionRoute($name)) {
                continue;
            }

            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);

            $created++;
        }

        $this->info("✅ Đã sync {$created} permissions từ routes.");
    }

    /**
     * Các route nào được phép tạo permission
     */
    protected function isPermissionRoute(string $name): bool
    {
        return str_starts_with($name, 'roles.')
            || $name === 'dashboard';
    }
}
