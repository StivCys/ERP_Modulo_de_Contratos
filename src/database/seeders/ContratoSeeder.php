<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Contrato\Models\Contrato;
use App\Domains\Cliente\Models\Cliente;
use App\Domains\Servico\Models\Servico;
use Carbon\Carbon;

class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Precisamos ter certeza de que há clientes e serviços
        if (Cliente::count() === 0) {
            Cliente::create([
                'nome' => 'Cliente Exemplo Ltda',
                'email' => 'contato@clienteexemplo.com.br',
                'cpf_cnpj' => '12345678000199',
                'ativo' => 'sim',
            ]);
            Cliente::create([
                'nome' => 'João Silva',
                'email' => 'joao.silva@email.com',
                'cpf_cnpj' => '12345678901',
                'ativo' => 'sim',
            ]);
        }

        $clientes = Cliente::all();
        $servicos = Servico::all();

        if ($servicos->count() === 0) {
            return;
        }

        // Criando contrato para o Cliente 1 
        $contrato1 = Contrato::create([
            'cliente_id' => $clientes->first()->id,
            'data_inicio' => Carbon::now()->subMonths(2),
            'status' => 'ativo',
        ]);

        $servicosC1 = $servicos->random(3);
        foreach ($servicosC1 as $servico) {
            $contrato1->items()->create([
                'servico_id' => $servico->id,
                'quantidade' => 1,
                'valor_unitario' => $servico->valor,
            ]);
        }

        // Criando contrato para o Cliente 2 
        if ($clientes->count() > 1) {
            $contrato2 = Contrato::create([
                'cliente_id' => $clientes->last()->id,
                'data_inicio' => Carbon::now()->subDays(15),
                'status' => 'ativo',
            ]);

            $servicoUnico = $servicos->first();
            $contrato2->items()->create([
                'servico_id' => $servicoUnico->id,
                'quantidade' => 2,
                'valor_unitario' => $servicoUnico->valor,
            ]);
        }
    }
}
