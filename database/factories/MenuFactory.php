<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KategoriMenu;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition()
    {
        return [
            'kategori_menu_id' => KategoriMenu::factory(),
            'nama'            => 'Nasi Goreng',
            'harga'           => 10000,
            'deskripsi'       => 'Menu dummy untuk testing',
            'gambar'          => 'menu.jpg',
            'perlu_dimasak'   => true,
            'is_aktif'        => true,
        ];
    }
}
