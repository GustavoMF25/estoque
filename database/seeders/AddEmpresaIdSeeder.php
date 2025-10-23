<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEmpresaIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
        ];

        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela) && Schema::hasColumn($tabela, 'empresa_id')) {
                DB::table($tabela)
                    ->whereNull('empresa_id')
                    ->update(['empresa_id' => 1]);
            }
        }

        $this->command->info('✅ empresa_id=1 aplicado com sucesso em todas as tabelas existentes.');
    }
}
