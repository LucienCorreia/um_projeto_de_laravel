<?php

namespace Database\Factories;

use App\Models\Indice;
use App\Models\Livro;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Indice>
 */
class IndiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(),
            'pagina' => fake()->numberBetween(1, 100),
        ];
    }
}
