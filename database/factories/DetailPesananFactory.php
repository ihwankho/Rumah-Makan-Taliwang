<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pesanan;
use App\Models\Menu;

class DetailPesananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_pesanan' => Pesanan::factory(),
            'id_menu' => Menu::factory(),
            'jumlah' => 1,
            'harga_satuan' => 15000,
            'subtotal' => 15000,
        ];
    }
}
