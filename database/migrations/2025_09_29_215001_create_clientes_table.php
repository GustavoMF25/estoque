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
        Schema::create('clientes', function (Blueprint $t) {
            $t->id();
            $t->string('nome');
            $t->string('email')->nullable()->unique();
            $t->string('telefone')->nullable();
            $t->string('documento')->nullable()->index(); // CPF/CNPJ
            $t->boolean('ativo')->default(true);
            $t->text('observacoes')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
