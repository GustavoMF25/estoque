<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_emissoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->foreignId('modelo_id')->constrained('nota_modelos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('cliente_nome')->nullable();
            $table->string('cliente_documento')->nullable();
            $table->string('cliente_email')->nullable();
            $table->string('cliente_telefone')->nullable();

            $table->string('cep')->nullable();
            $table->string('rua')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();

            $table->longText('conteudo_frente');
            $table->longText('conteudo_verso');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_emissoes');
    }
};
