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
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();

            // 🔗 Empresa associada (multi-empresa)
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');

            // 📦 Dados principais
            $table->string('plano', 60)->default('gestao_completa');
            $table->decimal('valor_mensal', 10, 2)->default(149.00);

            // 📅 Datas de controle
            $table->date('data_inicio');
            $table->date('data_vencimento');

            // ⚙️ Status e pagamento
            $table->enum('status', ['pendente', 'ativo', 'atrasado', 'cancelado'])->default('pendente');
            $table->enum('metodo_pagamento', ['manual', 'pix', 'asaas', 'pagseguro'])->default('manual');

            // 📆 Auditoria de confirmações
            $table->timestamp('ultima_confirmacao')->nullable();

            // 🕒 Timestamps padrão
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};
