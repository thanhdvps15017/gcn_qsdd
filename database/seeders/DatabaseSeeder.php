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
        User::create([
            'name' => 'Đoàn Văn Thành',
            'username' => 'superadmin',
            'email' => 'thanhvan2703201@gmail.com',
            'phone' => '0377421240',
            'password' => Hash::make('123123123'),
            'email_verified_at' => now(),
        ]);
    }
}
