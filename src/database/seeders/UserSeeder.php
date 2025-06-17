<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者
        User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'type' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 出品者
        User::create([
            'name' => '出品者1',
            'email' => 'seller1@example.com',
            'password' => Hash::make('password'),
            'type' => 'seller',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => '出品者2',
            'email' => 'seller2@example.com',
            'password' => Hash::make('password'),
            'type' => 'seller',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 購入者
        User::create([
            'name' => '購入者1',
            'email' => 'buyer1@example.com',
            'password' => Hash::make('password'),
            'type' => 'buyer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => '購入者2',
            'email' => 'buyer2@example.com',
            'password' => Hash::make('password'),
            'type' => 'buyer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
