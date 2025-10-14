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
        Schema::create('enderecos_clientes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $t->string('rotulo')->nullable(); // Ex.: Principal, Cobrança
            $t->string('cep', 15)->nullable();
            $t->string('rua')->nullable();
            $t->string('numero', 50)->nullable();
            $t->string('complemento')->nullable();
            $t->string('bairro')->nullable();
            $t->string('cidade')->nullable();
            $t->string('estado', 5)->nullable();
            $t->boolean('padrao')->default(true);
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endereco_clientes');
    }
};
