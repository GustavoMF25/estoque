<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos_unidades', function (Blueprint $table) {
            $table->index(['produto_id', 'status'], 'produtos_unidades_produto_status_index');
        });

        Schema::table('produto_chegadas', function (Blueprint $table) {
            $table->index(['produto_id', 'status', 'previsao_chegada'], 'produto_chegadas_produto_status_previsao_index');
        });
    }

    public function down(): void
    {
        Schema::table('produtos_unidades', function (Blueprint $table) {
            $table->dropIndex('produtos_unidades_produto_status_index');
        });

        Schema::table('produto_chegadas', function (Blueprint $table) {
            $table->dropIndex('produto_chegadas_produto_status_previsao_index');
        });
    }
};
