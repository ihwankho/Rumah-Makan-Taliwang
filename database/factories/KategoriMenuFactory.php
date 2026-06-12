<?php

namespace Database\Factories;

use App\Models\KategoriMenu;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriMenuFactory extends Factory
{
    protected $model = KategoriMenu::class;

    public function definition()
    {
        return [
            'nama' => 'Kategori-' . fake()->unique()->numberBetween(1, 1000),
        ];
    }
}
