<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class SyncPermissionsFromRoutes extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Tự động tạo permission từ các route có middleware auth';

    public function handle()
    {
        $routes = Route::getRoutes();
        $created = 0;

        foreach ($routes as $route) {
            // ❌ Route không có name → bỏ
            $name = $route->getName();
            if (! $name) {
                continue;
            }

            // ❌ Không thuộc group auth → bỏ
            if (! $this->isAuthRoute($route)) {
                continue;
            }

            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);

            $created++;
        }

        $this->info("✅ Đã sync {$created} permissions từ route auth.");
    }

    /**
     * Kiểm tra route có middleware auth hay không
     */
    protected function isAuthRoute($route): bool
    {
        $middlewares = $route->gatherMiddleware();

        return in_array('auth', $middlewares)
            || in_array('auth:web', $middlewares);
    }
}
