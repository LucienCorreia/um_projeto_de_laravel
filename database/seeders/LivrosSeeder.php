<?php

namespace Database\Seeders;

use App\Models\Indice;
use App\Models\Livro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LivrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Livro::factory()->count(10)->has(Indice::factory()->count(3))->create();
    }
}
