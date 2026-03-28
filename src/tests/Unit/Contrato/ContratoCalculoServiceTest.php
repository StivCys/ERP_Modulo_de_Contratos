<?php

namespace Tests\Unit\Contrato;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Contrato\Services\ContratoCalculoService;
use App\Domains\Contrato\Models\Contrato;
use App\Domains\Contrato\Models\ContratoRegraAdicional;
use App\Domains\Servico\Models\Servico;
use App\Domains\Cliente\Models\Cliente;

class ContratoCalculoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ContratoCalculoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ContratoCalculoService::class);
    }

    public function test_calcula_valor_base_sem_regras()
    {
        $cliente = Cliente::factory()->create();

        $contrato = Contrato::factory()->for($cliente)->create();

        $servico1 = Servico::factory()->create();
        $servico2 = Servico::factory()->create();

        $contrato->items()->createMany([
            [
                'servico_id' => $servico1->id,
                'quantidade' => 2,
                'valor_unitario' => 100
            ],
            [
                'servico_id' => $servico2->id,
                'quantidade' => 1,
                'valor_unitario' => 50
            ],
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(250, $resultado['valor_base']);
        $this->assertEquals(250, $resultado['valor_final']);
        $this->assertFalse($resultado['teve_desconto_ou_acrescimo']);
    }

    public function test_aplica_regra_quantidade_servicos()
    {
        $contrato = Contrato::factory()->create();

        $servicos = Servico::factory()->count(3)->create();

        $contrato->items()->createMany([
            [
                'servico_id' => $servicos[0]->id,
                'quantidade' => 1,
                'valor_unitario' => 100
            ],
            [
                'servico_id' => $servicos[1]->id,
                'quantidade' => 1,
                'valor_unitario' => 100
            ],
            [
                'servico_id' => $servicos[2]->id,
                'quantidade' => 1,
                'valor_unitario' => 100
            ],
        ]);

        ContratoRegraAdicional::create([
            'nome' => 'Regra quantidade',
            'tipo_regra' => 'quantidade_servicos',
            'ativo' => true,
            'parametros' => [
                'quantidade_minima' => 3,
                'desconto_percentual' => 10
            ]
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(300, $resultado['valor_base']);
        $this->assertEquals(270, $resultado['valor_final']);
        $this->assertTrue($resultado['teve_desconto_ou_acrescimo']);
    }

    public function test_aplica_desconto_progressivo()
    {
        $contrato = Contrato::factory()->create();

        $servicos = Servico::factory()->count(2)->create();

        $contrato->items()->createMany([
            [
                'servico_id' => $servicos[0]->id,
                'quantidade' => 1,
                'valor_unitario' => 100
            ],
            [
                'servico_id' => $servicos[1]->id,
                'quantidade' => 1,
                'valor_unitario' => 100
            ],
        ]);

        ContratoRegraAdicional::create([
            'nome' => 'Progressivo',
            'tipo_regra' => 'desconto_progressivo',
            'ativo' => true,
            'parametros' => [
                'desconto_por_item_percentual' => 5,
                'desconto_maximo_percentual' => 20
            ]
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(200, $resultado['valor_base']);
        $this->assertEquals(180, $resultado['valor_final']);
    }

    public function test_aplica_regra_servico_especifico()
    {
        $contrato = Contrato::factory()->create();

        $servico = Servico::factory()->create();

        $contrato->items()->create([
            'servico_id' => $servico->id,
            'quantidade' => 1,
            'valor_unitario' => 100
        ]);

        ContratoRegraAdicional::create([
            'nome' => 'Servico especial',
            'tipo_regra' => 'servico_especifico',
            'ativo' => true,
            'parametros' => [
                'servico_id' => $servico->id,
                'acrescimo_fixo' => 50,
                'desconto_percentual' => 10
            ]
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(100, $resultado['valor_base']);
        $this->assertEquals(140, $resultado['valor_final']);
    }

    public function test_aplica_regra_fidelidade()
    {
        $contrato = Contrato::factory()->create([
            'data_inicio' => now()->subMonths(12),
            'data_fim' => now()
        ]);

        $servico = Servico::factory()->create();

        $contrato->items()->create([
            'servico_id' => $servico->id,
            'quantidade' => 1,
            'valor_unitario' => 200
        ]);

        ContratoRegraAdicional::create([
            'nome' => 'Fidelidade',
            'tipo_regra' => 'fidelidade',
            'ativo' => true,
            'parametros' => [
                'meses_fidelidade' => 12,
                'desconto_percentual' => 10
            ]
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(200, $resultado['valor_base']);
        $this->assertEquals(180, $resultado['valor_final']);
    }

    public function test_nao_permite_valor_negativo()
    {
        $contrato = Contrato::factory()->create();

        $servico = Servico::factory()->create();

        $contrato->items()->create([
            'servico_id' => $servico->id,
            'quantidade' => 1,
            'valor_unitario' => 50
        ]);

        ContratoRegraAdicional::create([
            'nome' => 'Desconto absurdo',
            'tipo_regra' => 'quantidade_servicos',
            'ativo' => true,
            'parametros' => [
                'quantidade_minima' => 1,
                'desconto_percentual' => 200
            ]
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(0, $resultado['valor_final']);
    }
}