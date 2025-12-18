<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'id' => '00000000-0000-0000-0000-000000000001',
            'document' => '12345678900',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
