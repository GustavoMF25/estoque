<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tabelas = [
            'estoques',
            'produtos',
            'movimentacoes',
            'vendas',
            'vendas_itens',
            'clientes',
            'enderecos_clientes',
            'categorias',
            'fabricantes',
            'produto_vinculos',
            'produtos_unidades',
            'venda_item_unidades',
            'venda_itens',
        ];

        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela)) {
                Schema::table($tabela, function (Blueprint $table) use ($tabela) {
                    // 🔎 Verifica se o campo empresa_id ainda não existe
                    if (!Schema::hasColumn($tabela, 'empresa_id')) {
                        $table->unsignedBigInteger('empresa_id')->after('id')->index()->nullable();
                        $table->foreign('empresa_id')
                              ->references('id')
                              ->on('empresas')
                              ->onDelete('cascade');
                    }
                });
            }
        }

        // Também garante que a tabela users tenha o campo empresa_id
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'empresa_id')) {
                    $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                    $table->foreign('empresa_id')
                          ->references('id')
                          ->on('empresas')
                          ->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        $tabelas = [
            'lojas',
            'estoques',
            'produtos',
            'movimentacoes',
            'vendas',
            'vendas_itens',
            'clientes',
            'enderecos_clientes',
            'categorias',
            'fabricantes',
            'produto_vinculos',
            'produtos_unidades',
            'venda_item_unidades',
            'users',
            'venda_itens',
        ];

        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela) && Schema::hasColumn($tabela, 'empresa_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->dropForeign([$table->getTable().'_empresa_id_foreign']);
                    $table->dropColumn('empresa_id');
                });
            }
        }
    }
};
