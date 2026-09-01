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
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'is_active' => 1,
            'created_at' => now(),
        ]);
        $admin->assignRole('Admin');

        User::factory()->count(3)->create()->each(function (User $user) {
            $user->assignRole('User');
        });
    }
}
