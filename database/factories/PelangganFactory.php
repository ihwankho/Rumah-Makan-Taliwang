<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Membuat nama orang acak
            'nama_pelanggan' => fake()->name(),
        ];
    }
}
