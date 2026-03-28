<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contrato_regras_adicionais', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('tipo_regra')->comment('quantidade_servicos, desconto_progressivo, servico_especifico, fidelidade');
            $table->json('parametros')->comment('Parâmetros customizados por tipo de regra (ex: qtd_itens, desconto_percentual)');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_regras_adicionais');
    }
};
