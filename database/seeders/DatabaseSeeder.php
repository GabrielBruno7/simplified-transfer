<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'type' => 'comum',
            'name' => 'Gilson Mendes',
            'document' => '52833691025',
            'password' => bcrypt('password'),
            'email' => 'gilson.mendes@example.com',
            'id' => 'a30a1e33-52d0-4129-babd-aeb785389170',
        ]);

        User::create([
            'type' => 'lojista',
            'name' => 'Lucas Ramalho',
            'document' => '35445693000100',
            'password' => bcrypt('password'),
            'email' => 'lucas.ramalho@example.com',
            'id' => '3467076c-3bb6-495e-8b1c-0b5e99256f45',
        ]);

        DB::table('wallets')->insert([
            'balance' => 1000.50,
            'created_at' => now(),
            'updated_at' => now(),
            'id' => Str::uuid()->toString(),
            'user_id' => 'a30a1e33-52d0-4129-babd-aeb785389170',
        ]);

        DB::table('wallets')->insert([
            'balance' => 5000.75,
            'created_at' => now(),
            'updated_at' => now(),
            'id' => Str::uuid()->toString(),
            'user_id' => '3467076c-3bb6-495e-8b1c-0b5e99256f45',
        ]);
    }
}
