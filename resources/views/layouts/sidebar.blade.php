<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <x-application-mark class="block h-9 w-auto" />
        <span class="brand-text font-weight-light">{{ $empresa->nome }} - <small>Sistema</small></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->profile_photo_url }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('profile.show') }}" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        @php
            $usuario = auth()->user();
            $isAdmin = $usuario?->isAdmin() ?? false;

            $rotasPermitidas = collect();
            if ($usuario) {
                $perfilConfigurado = \Illuminate\Support\Facades\Schema::hasTable('perfil_funcionalidade')
                    && \Illuminate\Support\Facades\DB::table('perfil_funcionalidade')
                        ->where('perfil', $usuario->perfil)
                        ->exists();

                if ($perfilConfigurado && \Illuminate\Support\Facades\Schema::hasTable('funcionalidades')) {
                    $rotasPermitidas = \App\Models\Funcionalidade::query()
                        ->where('ativo', true)
                        ->where('visivel_menu', true)
                        ->whereNotNull('rota')
                        ->whereHas('perfis', function ($query) use ($usuario) {
                            $query->where('perfil_funcionalidade.perfil', $usuario->perfil);
                        })
                        ->pluck('rota');
                } else {
                    if (in_array($usuario->perfil, ['admin', 'gerente', 'operador', 'vendedor'], true)) {
                        $rotasPermitidas = collect([
                            'dashboard',
                            'profile.show',
                            'estoques.index',
                            'produto-chegadas.index',
                            'produtos.index',
                            'produtos.create',
                            'produtos.catalogo',
                            'vendas.index',
                            'notificacoes.index',
                            'clientes.index',
                            'categorias.index',
                            'fabricantes.index',
                            'lojas.index',
                        ]);
                    }

                    if ($isAdmin) {
                        $rotasPermitidas = $rotasPermitidas->merge([
                            'usuarios.index',
                            'perfis.index',
                            'empresa.edit',
                            'nota-modelos.index',
                            'auditoria.index',
                            'funcionalidades.index',
                            'perfil-funcionalidades.edit',
                        ]);
                    }
                }
            }

            $routePermitida = function (string $routeName) use ($rotasPermitidas) {
                return $rotasPermitidas->contains($routeName);
            };

            $moduloEstoqueVisivel = $routePermitida('estoques.index')
                || $routePermitida('produto-chegadas.index')
                || $routePermitida('produtos.index')
                || $routePermitida('produtos.create');

            $moduloComercialVisivel = $routePermitida('produtos.catalogo')
                || $routePermitida('vendas.index');

            $moduloCadastrosVisivel = $routePermitida('clientes.index')
                || $routePermitida('categorias.index')
                || $routePermitida('fabricantes.index')
                || $routePermitida('lojas.index');

            $moduloAdminVisivel = $isAdmin && (
                $routePermitida('usuarios.index')
                || $routePermitida('perfis.index')
                || $routePermitida('empresa.edit')
                || $routePermitida('nota-modelos.index')
                || $routePermitida('auditoria.index')
                || $routePermitida('funcionalidades.index')
                || $routePermitida('perfil-funcionalidades.edit')
            );

            $moduloEstoqueAtivo = $moduloEstoqueVisivel && (
                request()->routeIs('estoques.*')
                || request()->routeIs('produto-chegadas.*')
                || request()->routeIs('produtos.index')
                || request()->routeIs('produtos.create')
                || request()->routeIs('produtos.show')
            );

            $moduloComercialAtivo = $moduloComercialVisivel && (
                request()->routeIs('produtos.catalogo')
                || request()->routeIs('carrinho.confirmar')
                || request()->routeIs('vendas.*')
            );

            $moduloCadastrosAtivo = $moduloCadastrosVisivel && (
                request()->routeIs('clientes.*')
                || request()->routeIs('categorias.*')
                || request()->routeIs('fabricantes.*')
                || request()->routeIs('lojas.*')
            );

            $moduloAdminAtivo = $moduloAdminVisivel && (
                request()->routeIs('usuarios.*')
                || request()->routeIs('perfis.*')
                || request()->routeIs('empresa.*')
                || request()->routeIs('auditoria.*')
                || request()->routeIs('nota-modelos.*')
                || request()->routeIs('funcionalidades.*')
                || request()->routeIs('perfil-funcionalidades.*')
            );
        @endphp

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if ($routePermitida('dashboard'))
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endif

                @if ($routePermitida('profile.show'))
                    <li class="nav-item">
                        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Meu Perfil</p>
                        </a>
                    </li>
                @endif

                @if ($moduloEstoqueVisivel)
                    <li class="nav-item {{ $moduloEstoqueAtivo ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $moduloEstoqueAtivo ? 'active' : '' }}">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>
                                Módulo Estoque
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if ($routePermitida('estoques.index'))
                                <li class="nav-item">
                                    <a href="{{ route('estoques.index') }}" class="nav-link {{ request()->routeIs('estoques.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gestão de Estoques</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('produto-chegadas.index'))
                                <li class="nav-item">
                                    <a href="{{ route('produto-chegadas.index') }}" class="nav-link {{ request()->routeIs('produto-chegadas.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Estoque a Chegar</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('produtos.index'))
                                <li class="nav-item">
                                    <a href="{{ route('produtos.index') }}" class="nav-link {{ request()->routeIs('produtos.index') || request()->routeIs('produtos.show') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Produtos</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('produtos.create'))
                                <li class="nav-item">
                                    <a href="{{ route('produtos.create') }}" class="nav-link {{ request()->routeIs('produtos.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cadastrar Produto</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if ($moduloComercialVisivel)
                    <li class="nav-item {{ $moduloComercialAtivo ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $moduloComercialAtivo ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Módulo Comercial
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if ($routePermitida('produtos.catalogo'))
                                <li class="nav-item">
                                    <a href="{{ route('produtos.catalogo') }}" class="nav-link {{ request()->routeIs('produtos.catalogo') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Catálogo de Produtos</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('vendas.index'))
                                <li class="nav-item">
                                    <a href="{{ route('vendas.index') }}" class="nav-link {{ request()->routeIs('vendas.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Vendas</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if ($routePermitida('notificacoes.index'))
                    <li class="nav-item">
                        <a href="{{ route('notificacoes.index') }}" class="nav-link {{ request()->routeIs('notificacoes.*') ? 'active' : '' }}">
                            <i class="fas fa-bell nav-icon"></i>
                            <p>Notificações</p>
                        </a>
                    </li>
                @endif

                @if ($moduloCadastrosVisivel)
                    <li class="nav-item {{ $moduloCadastrosAtivo ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $moduloCadastrosAtivo ? 'active' : '' }}">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>
                                Módulo Cadastros
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if ($routePermitida('clientes.index'))
                                <li class="nav-item">
                                    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('categorias.index'))
                                <li class="nav-item">
                                    <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Categorias</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('fabricantes.index'))
                                <li class="nav-item">
                                    <a href="{{ route('fabricantes.index') }}" class="nav-link {{ request()->routeIs('fabricantes.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Fabricantes</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('lojas.index'))
                                <li class="nav-item">
                                    <a href="{{ route('lojas.index') }}" class="nav-link {{ request()->routeIs('lojas.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Lojas</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if ($moduloAdminVisivel)
                    <li class="nav-item {{ $moduloAdminAtivo ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $moduloAdminAtivo ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Administração
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if ($routePermitida('usuarios.index'))
                                <li class="nav-item">
                                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Usuários</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('perfis.index'))
                                <li class="nav-item">
                                    <a href="{{ route('perfis.index') }}" class="nav-link {{ request()->routeIs('perfis.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Perfis</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('empresa.edit'))
                                <li class="nav-item">
                                    <a href="{{ route('empresa.edit') }}" class="nav-link {{ request()->routeIs('empresa.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Empresa</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('nota-modelos.index'))
                                <li class="nav-item">
                                    <a href="{{ route('nota-modelos.index') }}" class="nav-link {{ request()->routeIs('nota-modelos.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Modelos de Nota</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('auditoria.index'))
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.index') }}" class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Auditoria</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('funcionalidades.index'))
                                <li class="nav-item">
                                    <a href="{{ route('funcionalidades.index') }}" class="nav-link {{ request()->routeIs('funcionalidades.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Funcionalidades</p>
                                    </a>
                                </li>
                            @endif
                            @if ($routePermitida('perfil-funcionalidades.edit'))
                                <li class="nav-item">
                                    <a href="{{ route('perfil-funcionalidades.edit') }}" class="nav-link {{ request()->routeIs('perfil-funcionalidades.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Permissões por Perfil</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
