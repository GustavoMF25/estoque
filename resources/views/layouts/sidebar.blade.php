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
            $usuario = optional(auth()->user());

            $moduloEstoqueAtivo = request()->routeIs('estoques.*')
                || request()->routeIs('produto-chegadas.*')
                || request()->routeIs('produtos.index')
                || request()->routeIs('produtos.create')
                || request()->routeIs('produtos.show');

            $moduloComercialAtivo = request()->routeIs('produtos.catalogo')
                || request()->routeIs('carrinho.confirmar')
                || request()->routeIs('vendas.*');

            $moduloCadastrosAtivo = request()->routeIs('clientes.*')
                || request()->routeIs('categorias.*')
                || request()->routeIs('fabricantes.*')
                || request()->routeIs('lojas.*');

            $moduloAdminAtivo = request()->routeIs('usuarios.*')
                || request()->routeIs('empresa.*')
                || request()->routeIs('auditoria.*')
                || request()->routeIs('nota-modelos.*');
        @endphp

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Meu Perfil</p>
                    </a>
                </li>

                <li class="nav-item {{ $moduloEstoqueAtivo ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $moduloEstoqueAtivo ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Módulo Estoque
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('estoques.index') }}" class="nav-link {{ request()->routeIs('estoques.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gestão de Estoques</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('produto-chegadas.index') }}" class="nav-link {{ request()->routeIs('produto-chegadas.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Estoque a Chegar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('produtos.index') }}" class="nav-link {{ request()->routeIs('produtos.index') || request()->routeIs('produtos.show') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Produtos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('produtos.create') }}" class="nav-link {{ request()->routeIs('produtos.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cadastrar Produto</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $moduloComercialAtivo ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $moduloComercialAtivo ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Módulo Comercial
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('produtos.catalogo') }}" class="nav-link {{ request()->routeIs('produtos.catalogo') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Catálogo de Produtos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vendas.index') }}" class="nav-link {{ request()->routeIs('vendas.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendas</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notificacoes.index') }}" class="nav-link {{ request()->routeIs('notificacoes.*') ? 'active' : '' }}">
                        <i class="fas fa-bell nav-icon"></i>
                        <p>Notificações</p>
                    </a>
                </li>

                <li class="nav-item {{ $moduloCadastrosAtivo ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $moduloCadastrosAtivo ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            Módulo Cadastros
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categorias</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('fabricantes.index') }}" class="nav-link {{ request()->routeIs('fabricantes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fabricantes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lojas.index') }}" class="nav-link {{ request()->routeIs('lojas.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lojas</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @if ($usuario->isAdmin())
                    <li class="nav-item {{ $moduloAdminAtivo ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $moduloAdminAtivo ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Administração
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Usuários</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('empresa.edit') }}" class="nav-link {{ request()->routeIs('empresa.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Empresa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('nota-modelos.index') }}" class="nav-link {{ request()->routeIs('nota-modelos.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Modelos de Nota</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('auditoria.index') }}" class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Auditoria</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
