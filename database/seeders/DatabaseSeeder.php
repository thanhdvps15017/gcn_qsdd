<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Đoàn Văn Thành',
                'email' => 'thanhvan2703201@gmail.com',
                'phone' => '0377421240',
                'password' => bcrypt('12345678@'),
                'email_verified_at' => now(),
            ]
        );

        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin']);
        $user->assignRole($role);

        // Tự động quét và tạo Permission từ Route
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $permissionsToSync = [];
        foreach ($routes as $route) {
            $middlewares = $route->middleware();
            if (!is_array($middlewares)) continue;

            foreach ($middlewares as $middleware) {
                if (str_starts_with($middleware, 'can:')) {
                    $permissionName = substr($middleware, 4);
                    $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);
                    $permissionsToSync[] = $permissionName;
                }
            }
        }
        
        // Cấp full quyền cho superadmin
        if (!empty($permissionsToSync)) {
            $role->syncPermissions($permissionsToSync);
        }

        // Gọi thêm MasterDataSeeder
        $this->call(MasterDataSeeder::class);
    }
}
