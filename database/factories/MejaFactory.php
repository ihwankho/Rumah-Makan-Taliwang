<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MejaFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Membuat nomor meja acak, misal: "Meja-12"
            'nomor_meja' => 'Meja-' . fake()->unique()->numberBetween(1, 50), 
        ];
    }
}