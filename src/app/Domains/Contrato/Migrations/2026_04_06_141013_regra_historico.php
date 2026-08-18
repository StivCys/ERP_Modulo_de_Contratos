<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('regra_adicional_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regra_adicional_id')->constrained('contrato_regras_adicionais')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('acao');
            $table->text('descricao');
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regra_adicional_historicos');
    }
};
