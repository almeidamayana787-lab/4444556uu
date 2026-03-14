<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['name' => 'admin'],
            [
                'password' => bcrypt('admin'),
                'is_admin' => true,
                'phone' => '00000000000',
            ]
        );
    }
}
