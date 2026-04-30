<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriMenu;

class KategoriMenuSeeder extends Seeder
{
    public function run(): void
    {
        KategoriMenu::insert([
            ['nama' => 'Makanan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Minuman', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
