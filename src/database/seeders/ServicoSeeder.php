<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Servico\Models\Servico;

class ServicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicos = [
            ['nome' => 'Internet Banda Larga 100Mbps', 'valor' => 99.90],
            ['nome' => 'Internet Banda Larga 300Mbps', 'valor' => 129.90],
            ['nome' => 'Internet Banda Larga 500Mbps', 'valor' => 159.90],
            ['nome' => 'Telefonia Fixa Ilimitada', 'valor' => 49.90],
            ['nome' => 'IP Fixo', 'valor' => 29.90],
            ['nome' => 'Suporte Técnico Premium 24/7', 'valor' => 89.90],
            ['nome' => 'Hospedagem de Site Básico', 'valor' => 39.90],
            ['nome' => 'Serviço de Nuvem (50GB)', 'valor' => 59.90],
        ];

        foreach ($servicos as $servico) {
            Servico::create($servico);
        }
    }
}
