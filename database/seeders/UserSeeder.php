<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserTableSeeder.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        User::withTrashed()->where('is_super_admin', false)->forceDelete();

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@transbud.com',
            'password' => Hash::make('admin'),
            'is_active' => 1,
            'created_at' => now(),
        ]);
        $admin->assignRole('Admin');

        foreach (range(1, 3) as $i) {
            $user = User::factory()->create([
                'name' => "user{$i}",
                'email' => "user{$i}@transbud.com",
                'password' => Hash::make('password'),
                'is_active' => 1,
                'created_at' => now(),
            ]);
            $user->assignRole('User');
        }
    }
}
