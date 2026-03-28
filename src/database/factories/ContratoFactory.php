<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Cliente\Models\Cliente;
use App\Domains\Contrato\Models\Contrato;

class ContratoFactory extends Factory
{
    protected $model = Contrato::class;

    public function definition(): array
    {
        $dataInicio = $this->faker->dateTimeBetween('-1 year', 'now');
        $dataFim = $this->faker->optional()->dateTimeBetween($dataInicio, '+1 year');

        return [
            'cliente_id' => Cliente::factory(),
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'status' => $this->faker->randomElement(['ativo', 'cancelado']),
        ];
    }
}