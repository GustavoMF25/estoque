<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuloSeed extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🔹 Truncate com segurança
        DB::table('empresa_submodulos')->truncate();
        DB::table('empresa_modulos')->truncate();
        DB::table('submodulos')->truncate();
        DB::table('modulos')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $modulos = [
            ['nome' => 'Perfil', 'slug' => 'profile.show', 'icone' => 'fa fa-user'],
            ['nome' => 'Estoque', 'slug' => 'estoques.index', 'icone' => 'fa fa-boxes'],
            ['nome' => 'Categorias', 'slug' => 'categorias.index', 'icone' => 'fa fa-tags'],
            ['nome' => 'Fabricante', 'slug' => 'fabricantes.index', 'icone' => 'fa fa-industry'],
            ['nome' => 'Clientes', 'slug' => 'clientes.index', 'icone' => 'fa fa-users'],
            ['nome' => 'Produto', 'slug' => 'produtos.index', 'icone' => 'fa fa-cube'],
            ['nome' => 'Vendas', 'slug' => 'vendas.index', 'icone' => 'fa fa-shopping-cart'],
            ['nome' => 'Configurações', 'slug' => 'configurar.index', 'icone' => 'fa fa-cogs'],
            // ['nome' => 'Usuários', 'slug' => 'usuarios.index', 'icone' => 'fa fa-user-shield'],
            // ['nome' => 'Empresa', 'slug' => 'empresa.index', 'icone' => 'fa fa-building'],
        ];

        $moduloIds = [];
        foreach ($modulos as $modulo) {
            $moduloIds[$modulo['slug']] = DB::table('modulos')->insertGetId(array_merge($modulo, [
                'ativo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Submódulos (simplificado)
        $submodulos = [
            // ['modulo' => 'estoques.index', 'nome' => 'Movimentações', 'rota' => 'estoque.movimentacoes.index', 'icone' => 'fa fa-exchange-alt'],
            ['modulo' => 'produtos.index', 'nome' => 'Listagem', 'rota' => 'produtos.index', 'icone' => 'fa fa-cube'],
            ['modulo' => 'produtos.index', 'nome' => 'Catálogo', 'rota' => 'produtos.catalogo', 'icone' => 'fa fa-box-open'],
            ['modulo' => 'vendas.index', 'nome' => 'Listagem', 'rota' => 'vendas.index', 'icone' => 'fa fa-file-invoice-dollar'],
            ['modulo' => 'configurar.index', 'nome' => 'Usuarios', 'rota' => 'usuarios.index', 'icone' => 'fa fa-user-shield'],
            ['modulo' => 'configurar.index', 'nome' => 'Empresa', 'rota' => 'empresa.edit', 'icone' => 'fa fa-building'],
        ];

        foreach ($submodulos as $sub) {
            DB::table('submodulos')->insert([
                'modulo_id' => $moduloIds[$sub['modulo']],
                'nome' => $sub['nome'],
                'rota' => $sub['rota'],
                'icone' => $sub['icone'],
                'ativo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Vincula todos os módulos à empresa 1
        if (DB::table('empresas')->exists()) {
            $empresa = DB::table('empresas')->first();

            foreach ($moduloIds as $slug => $moduloId) {
                DB::table('empresa_modulos')->insert([
                    'empresa_id' => $empresa->id,
                    'modulo_id' => $moduloId,
                    'ativo' => true,
                    'status' => 'ativo', // 🔒 Bloqueia "Vendas"
                    'expira_em' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
