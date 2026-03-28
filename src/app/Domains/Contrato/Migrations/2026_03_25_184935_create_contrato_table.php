<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->enum('status', ['ativo', 'cancelado']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
        Schema::dropIfExists('contratos');
    }
};
