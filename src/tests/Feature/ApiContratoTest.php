<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Contrato\Models\Contrato;
use App\Domains\Cliente\Models\Cliente;
use App\Domains\Servico\Models\Servico;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;

class ApiContratoTest extends TestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();
    }

    protected function autenticar()
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        $permissions = [
            'cliente.view-email',
            'cliente.create',
            'cliente.update',
            'cliente.delete',
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);

            $user->givePermissionTo($permission);
        }

        Sanctum::actingAs($user);

        return $user;
    }

    public function test_api_pode_criar_contrato_com_items()
    {
        $cliente = Cliente::factory()->create();
        $servico = Servico::factory()->create();

        $data = [
            'cliente_id' => $cliente->id,
            'data_inicio' => now()->toDateString(),
            'status' => 'ativo',
            'items' => [
                [
                    'servico_id' => $servico->id,
                    'quantidade' => 2
                ]
            ]
        ];

        $response = $this->postJson('/api/contratos', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contratos', [
            'cliente_id' => $cliente->id
        ]);

        $this->assertDatabaseHas('contrato_items', [
            'quantidade' => 2
        ]);
    }

    public function test_api_nao_cria_contrato_sem_cliente()
    {
        $response = $this->postJson('/api/contratos', [
            'data_inicio' => now()->toDateString(),
            'status' => 'ativo'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cliente_id']);
    }

    public function test_api_pode_listar_contratos()
    {
        Contrato::factory()->count(2)->create();

        $response = $this->getJson('/api/contratos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'cliente_id', 'status']
                ]
            ]);
    }

    public function test_api_pode_atualizar_contrato_sem_perder_items()
    {
        $contrato = Contrato::factory()
            ->has(\App\Domains\Contrato\Models\ContratoItems::factory()->count(2), 'items')
            ->create(['status' => 'ativo']);

        $idsOriginais = $contrato->items->pluck('id')->toArray();

        $response = $this->putJson("/api/contratos/{$contrato->id}", [
            'status' => 'cancelado'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contratos', [
            'id' => $contrato->id,
            'status' => 'cancelado'
        ]);

        // 🔥 garante que NÃO recriou (IDs continuam os mesmos)
        foreach ($idsOriginais as $id) {
            $this->assertDatabaseHas('contrato_items', [
                'id' => $id,
                'contrato_id' => $contrato->id
            ]);
        }
    }
}