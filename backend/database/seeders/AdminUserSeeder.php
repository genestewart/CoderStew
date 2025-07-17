<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Gene Stewart',
            'email' => 'gene@coderstew.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'bio' => 'Full-stack developer and founder of CoderStew',
            'social_links' => [
                'github' => 'https://github.com/genestewart',
                'linkedin' => 'https://linkedin.com/in/genestewart',
                'twitter' => 'https://twitter.com/genestewart',
            ],
            'preferences' => [
                'notifications' => true,
                'newsletter' => true,
                'theme' => 'dark',
            ],
        ]);

        // Create editor user
        User::create([
            'name' => 'Editor User',
            'email' => 'editor@coderstew.com',
            'password' => Hash::make('password'),
            'role' => 'editor',
            'email_verified_at' => now(),
            'bio' => 'Content editor for CoderStew',
        ]);
    }
}
