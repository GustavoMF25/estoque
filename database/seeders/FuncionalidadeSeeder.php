<?php

namespace Database\Seeders;

use App\Models\Funcionalidade;
use App\Models\Perfil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuncionalidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcionalidades = [
            ['nome' => 'Dashboard', 'slug' => 'dashboard', 'modulo' => 'Base', 'rota' => 'dashboard', 'icone' => 'fas fa-tachometer-alt', 'ordem' => 1, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Meu Perfil', 'slug' => 'meu-perfil', 'modulo' => 'Base', 'rota' => 'profile.show', 'icone' => 'fas fa-user', 'ordem' => 2, 'visivel_menu' => true, 'ativo' => true],

            ['nome' => 'Gestão de Estoques', 'slug' => 'estoques-gestao', 'modulo' => 'Módulo Estoque', 'rota' => 'estoques.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 1, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Estoque a Chegar', 'slug' => 'estoque-chegadas', 'modulo' => 'Módulo Estoque', 'rota' => 'produto-chegadas.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 2, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Produtos', 'slug' => 'produtos-lista', 'modulo' => 'Módulo Estoque', 'rota' => 'produtos.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 3, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Cadastrar Produto', 'slug' => 'produtos-cadastrar', 'modulo' => 'Módulo Estoque', 'rota' => 'produtos.create', 'icone' => 'far fa-circle nav-icon', 'ordem' => 4, 'visivel_menu' => true, 'ativo' => true],

            ['nome' => 'Catálogo de Produtos', 'slug' => 'catalogo-produtos', 'modulo' => 'Módulo Comercial', 'rota' => 'produtos.catalogo', 'icone' => 'far fa-circle nav-icon', 'ordem' => 1, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Vendas', 'slug' => 'vendas', 'modulo' => 'Módulo Comercial', 'rota' => 'vendas.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 2, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Emitir Nota de Venda', 'slug' => 'vendas-nota-emissao', 'modulo' => 'Módulo Comercial', 'rota' => 'vendas.nota', 'icone' => null, 'ordem' => 99, 'visivel_menu' => false, 'ativo' => true],

            ['nome' => 'Visualizar Produto', 'slug' => 'produtos-visualizar', 'modulo' => 'Módulo Estoque', 'rota' => 'produtos.show', 'icone' => null, 'ordem' => 99, 'visivel_menu' => false, 'ativo' => true],

            ['nome' => 'Notificações', 'slug' => 'notificacoes', 'modulo' => 'Comunicação', 'rota' => 'notificacoes.index', 'icone' => 'fas fa-bell nav-icon', 'ordem' => 1, 'visivel_menu' => true, 'ativo' => true],

            ['nome' => 'Clientes', 'slug' => 'clientes', 'modulo' => 'Módulo Cadastros', 'rota' => 'clientes.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 1, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Categorias', 'slug' => 'categorias', 'modulo' => 'Módulo Cadastros', 'rota' => 'categorias.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 2, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Fabricantes', 'slug' => 'fabricantes', 'modulo' => 'Módulo Cadastros', 'rota' => 'fabricantes.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 3, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Lojas', 'slug' => 'lojas', 'modulo' => 'Módulo Cadastros', 'rota' => 'lojas.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 4, 'visivel_menu' => true, 'ativo' => true],

            ['nome' => 'Usuários', 'slug' => 'usuarios', 'modulo' => 'Administração', 'rota' => 'usuarios.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 1, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Perfis', 'slug' => 'perfis', 'modulo' => 'Administração', 'rota' => 'perfis.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 2, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Empresa', 'slug' => 'empresa', 'modulo' => 'Administração', 'rota' => 'empresa.edit', 'icone' => 'far fa-circle nav-icon', 'ordem' => 3, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Modelos de Nota', 'slug' => 'nota-modelos', 'modulo' => 'Administração', 'rota' => 'nota-modelos.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 4, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Auditoria', 'slug' => 'auditoria', 'modulo' => 'Administração', 'rota' => 'auditoria.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 5, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Gerenciar Funcionalidades', 'slug' => 'funcionalidades', 'modulo' => 'Administração', 'rota' => 'funcionalidades.index', 'icone' => 'far fa-circle nav-icon', 'ordem' => 6, 'visivel_menu' => true, 'ativo' => true],
            ['nome' => 'Permissões por Perfil', 'slug' => 'permissoes-perfil', 'modulo' => 'Administração', 'rota' => 'perfil-funcionalidades.edit', 'icone' => 'far fa-circle nav-icon', 'ordem' => 7, 'visivel_menu' => true, 'ativo' => true],
        ];

        foreach ($funcionalidades as $funcionalidade) {
            Funcionalidade::updateOrCreate(
                ['slug' => $funcionalidade['slug']],
                $funcionalidade
            );
        }

        $permissoesBase = [
            'admin' => Funcionalidade::query()->pluck('id')->all(),
            'gerente' => Funcionalidade::query()
                ->whereNotIn('slug', ['usuarios', 'perfis', 'empresa', 'auditoria', 'funcionalidades', 'permissoes-perfil'])
                ->pluck('id')
                ->all(),
            'operador' => Funcionalidade::query()
                ->whereIn('slug', [
                    'dashboard',
                    'meu-perfil',
                    'estoques-gestao',
                    'estoque-chegadas',
                    'produtos-lista',
                    'produtos-cadastrar',
                    'catalogo-produtos',
                    'vendas',
                    'notificacoes',
                    'clientes',
                    'categorias',
                    'fabricantes',
                    'lojas',
                ])->pluck('id')->all(),
            'vendedor' => Funcionalidade::query()
                ->whereIn('slug', [
                    'dashboard',
                    'meu-perfil',
                    'catalogo-produtos',
                    'vendas',
                    'notificacoes',
                    'clientes',
                ])->pluck('id')->all(),
            'escritorio' => Funcionalidade::query()
                ->whereIn('slug', [
                    'produtos-lista',
                    'produtos-visualizar',
                    'vendas-nota-emissao',
                ])->pluck('id')->all(),
        ];

        $perfis = Perfil::query()->pluck('slug')->all();

        foreach ($perfis as $perfil) {
            $ids = $permissoesBase[$perfil] ?? [];
            if (empty($ids)) {
                continue;
            }

            $linhas = collect($ids)->map(fn ($funcionalidadeId) => [
                'perfil' => $perfil,
                'funcionalidade_id' => $funcionalidadeId,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            DB::table('perfil_funcionalidade')->insertOrIgnore($linhas);
        }
    }
}
