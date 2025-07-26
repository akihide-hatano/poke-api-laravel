<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ★ ここに UserSeeder を呼び出すように変更
        $this->call(UserSeeder::class);

        // ★ ここに FavoritePokemonSeeder を呼び出すように変更
        $this->call(FavoritePokemonSeeder::class);
    }
}