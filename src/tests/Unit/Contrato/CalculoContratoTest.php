<?php

namespace Tests\Unit\Contrato;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Contrato\Models\Contrato;
use App\Domains\Contrato\Models\ContratoItems;
use App\Domains\Contrato\Models\ContratoRegraAdicional;
use App\Domains\Contrato\Services\ContratoCalculoService;

class CalculoContratoTest extends TestCase
{
    use RefreshDatabase;

    protected ContratoCalculoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ContratoCalculoService::class);
    }

    public function test_calculo_simples_de_total()
    {
        $items = [
            ['preco' => 100, 'quantidade' => 2],
            ['preco' => 50, 'quantidade' => 1],
        ];

        $total = collect($items)->sum(fn($i) => $i['preco'] * $i['quantidade']);

        $this->assertEquals(250, $total);
    }

    public function test_regra_quantidade_servicos_aplica_desconto()
    {
        ContratoRegraAdicional::create([
            'nome' => 'Desconto pacote',
            'tipo_regra' => 'quantidade_servicos',
            'parametros' => ['quantidade_minima' => 3, 'desconto_percentual' => 10],
            'ativo' => true,
        ]);

        $contrato = Contrato::factory()->create();

        ContratoItems::factory()->count(3)->create([
            'contrato_id' => $contrato->id,
            'valor_unitario' => 100,
            'quantidade' => 1,
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals(300, $resultado['valor_base']);
        $this->assertEquals(270, $resultado['valor_final']);
        $this->assertTrue($resultado['teve_desconto_ou_acrescimo']);
    }

    public function test_regra_inativa_nao_e_aplicada()
    {
        ContratoRegraAdicional::create([
            'nome' => 'Regra inativa',
            'tipo_regra' => 'quantidade_servicos',
            'parametros' => ['quantidade_minima' => 1, 'desconto_percentual' => 50],
            'ativo' => false,
        ]);

        $contrato = Contrato::factory()->create();

        ContratoItems::factory()->count(2)->create([
            'contrato_id' => $contrato->id,
            'valor_unitario' => 100,
            'quantidade' => 1,
        ]);

        $resultado = $this->service->calcular($contrato->fresh('items'));

        $this->assertEquals($resultado['valor_base'], $resultado['valor_final']);
        $this->assertFalse($resultado['teve_desconto_ou_acrescimo']);
    }
}