<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Contrato\Models\ContratoItems;
use App\Domains\Servico\Models\Servico;

class ContratoItemFactory extends Factory
{
    protected $model = ContratoItems::class;

    public function definition(): array
    {
        return [
            'servico_id' => Servico::factory(),
            'quantidade' => $this->faker->numberBetween(1, 10),
            'valor_unitario' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}