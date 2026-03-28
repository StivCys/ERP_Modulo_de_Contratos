<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domains\Cliente\Models\Cliente;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;

class ApiClienteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();
    }

    protected function autenticar()
    {
        $user = User::firstOrCreate(
            ['email' => 'testes@example.com'],
            [
                'name' => 'Usuario Testes',
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

    public function test_api_pode_listar_clientes()
    {


        Cliente::factory()->count(3)->create();

        $response = $this->getJson('/api/clientes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nome', 'email']
                ]
            ]);
    }

    public function test_api_pode_criar_cliente()
    {
        $email = fake()->unique()->safeEmail();
        $cpfCnpj = fake('pt_BR')->unique()->cpf(false);

        $data = [
            'nome' => 'João Silva',
            'email' => $email,
            'cpf_cnpj' => $cpfCnpj,
            'ativo' => 'sim',
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'nome' => 'João Silva',
                'email' => $email,
            ]);

        $this->assertDatabaseHas('clientes', [
            'email' => $email
        ]);
    }

    public function test_api_nao_cria_cliente_com_dados_invalidos()
    {
        $response = $this->postJson('/api/clientes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'email']);
    }

    public function test_api_pode_atualizar_cliente()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->putJson("/api/clientes/{$cliente->id}", [
            'nome' => 'Nome Atualizado',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nome' => 'Nome Atualizado'
        ]);
    }

    public function test_api_pode_deletar_cliente()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->deleteJson("/api/clientes/{$cliente->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clientes', [
            'id' => $cliente->id
        ]);
    }

    public function test_api_nao_cria_cliente_com_cpf_invalido()
    {
        $data = [
            'nome' => 'Maria Silva',
            'email' => fake()->unique()->safeEmail(),
            'cpf_cnpj' => '11111111111', // CPF inválido
            'ativo' => 'sim',
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf_cnpj']);
    }

    public function test_api_nao_cria_cliente_com_cnpj_invalido()
    {
        $data = [
            'nome' => 'Empresa Teste',
            'email' => fake()->unique()->safeEmail(),
            'cpf_cnpj' => '11111111111111', // CNPJ inválido
            'ativo' => 'sim',
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf_cnpj']);
    }
}