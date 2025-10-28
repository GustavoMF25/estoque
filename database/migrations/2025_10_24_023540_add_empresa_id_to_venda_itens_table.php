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
        Schema::table('venda_itens', function (Blueprint $table) {
            // 🔹 Adiciona empresa_id (pode ser nullable para vendas antigas)
            $table->foreignId('empresa_id')
                ->nullable()
                ->after('id') // coloque depois da coluna 'id' ou onde preferir
                ->constrained('empresas') // relaciona com a tabela 'empresas'
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('venda_itens', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
