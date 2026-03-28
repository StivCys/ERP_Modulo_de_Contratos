<?php

namespace Database\Factories;

use App\Domains\Servico\Models\Servico;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicoFactory extends Factory
{
    protected $model = Servico::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(3, true),
            'valor' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
