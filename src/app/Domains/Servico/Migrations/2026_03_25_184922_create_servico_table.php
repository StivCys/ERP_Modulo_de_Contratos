<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->float('valor', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
        Schema::dropIfExists('servicos');
    }
};
