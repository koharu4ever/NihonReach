<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            $admin = User::query()->firstOrNew([
                'email' => 'demo-admin@example.test',
            ]);

            $admin->forceFill([
                'name' => 'Demo Admin',
                'email_verified_at' => now(),
                'password' => 'password',
                'is_admin' => true,
            ])->save();
        }

        $this->call([
            ProductCategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
