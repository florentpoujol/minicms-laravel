<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Factories\PostFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Regular user',
            'email' => 'regular@example.com',
            'password' => Hash::make('regular'),
        ]);

        $writer = UserFactory::new()->create([
            'name' => 'Writer user',
            'email' => 'writer@example.com',
            'password' => Hash::make('writer'),
        ]);

        UserFactory::new()->create([
            'name' => 'admin user',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
        ]);

        PostFactory::new()
            ->count(10)
            ->for($writer, 'author')
            ->createMany();
    }
}
