<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Meja;
use App\Models\Pelanggan;

class PesananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_meja' => Meja::factory(),
            'id_pelanggan' => Pelanggan::factory(),
            'tipe_pesanan' => 'makan_ditempat',
            'status_pesanan' => 'menunggu',
            'total_harga' => 0, 
        ];
    }
}
