<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <x-application-mark class="block h-9 w-auto" />
        <span class="brand-text font-weight-light">{{ $empresa->nome }} - <small>Sistema </small></span>
    </a>

    @php
        $empresa = auth()->user()->empresa;
        $modulos = $empresa
            ->modulos()
            ->with([
                'submodulos' => function ($q) {
                    $q->where('ativo', true);
                },
            ])
            ->where('modulos.ativo', true)
            ->get();
    @endphp

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->profile_photo_url }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('profile.show') }}" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                @foreach ($modulos as $modulo)
                    @php
                        $bloqueado = $modulo->pivot->status === 'bloqueado';
                    @endphp
                    <li class="nav-item  {{ $modulo->submodulos->isNotEmpty() ? 'dropdown' : '' }}">

                        <a href="{{ $modulo->submodulos->isEmpty() ? route($modulo->slug) : '#' }}"
                            @if ($bloqueado) data-toggle="tooltip" 
                                data-placement="top" 
                                title="Módulo bloqueado — disponível apenas em planos pagos" @endif
                            class="nav-link {{ $bloqueado ? 'modulo-bloqueado' : '' }} {{ request()->routeIs($modulo->slug) ? 'active' : '' }} {{ $modulo->submodulos->isNotEmpty() ? '' : '' }}">
                            <i class="{{ $modulo->icone }}"></i>

                            <p>
                                {{ $modulo->nome }}
                                @if ($modulo->submodulos->isNotEmpty())
                                    <i class="right fas fa-angle-left"></i>
                                @endif

                                @if ($bloqueado)
                                    <i class="fa fa-lock text-warning ms-2"></i>
                                @endif
                            </p>
                        </a>

                        @if ($modulo->submodulos->isNotEmpty())
                            <ul class="nav nav-treeview">
                                @foreach ($modulo->submodulos as $sub)
                                    <li class="nav-item">
                                        <a href="{{ route($sub->rota) }}"
                                            class="nav-link {{ request()->routeIs($sub->rota) ? 'active' : '' }}">
                                            <i class="{{ $sub->icone ?? 'fa fa-circle' }}"></i> {{ $sub->nome }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach

                {{-- <li class="nav-item">
                    <a href="{{ route('profile.show') }}"
                        class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-solid fa-user"></i>
                        <p>
                            Perfil
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('estoques.index') }}"
                        class="nav-link {{ request()->routeIs('estoques.index') ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i>
                        <p>
                            Estoque
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('categorias.index') }}"
                        class="nav-link {{ request()->routeIs('categorias.index') ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i>
                        <p>
                            Categorias
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('fabricantes.index') }}"
                        class="nav-link {{ request()->routeIs('fabricantes.index') ? 'active' : '' }}">
                        <i class="fa fa-industry"></i>
                        <p>
                            Fabricante
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clientes.index') }}"
                        class="nav-link {{ request()->routeIs('clientes.index') ? 'active' : '' }}">
                        <i class="fa fa-users"></i>
                        <p>
                            Clientes
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('produtos.index') }}"
                        class="nav-link {{ request()->routeIs('produtos.index') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i>
                        <p>
                            Produto
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('produtos.catalogo') }}"
                        class="nav-link {{ request()->routeIs('produtos.catalogo') ? 'active' : '' }}">
                        <i class="fa fa-book"></i>
                        <p>
                            Catálogo de Produtos
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('vendas.index') }}"
                        class="nav-link {{ request()->routeIs('vendas.index') ? 'active' : '' }}">
                        <i class="fa fa-shopping-cart"></i>
                        <p>
                            Vendas
                        </p>
                    </a>
                </li> --}}

                @if (optional(auth()->user())->isSuperAdmin())
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fab fa-teamspeak"></i>

                            <p>
                                SuperUsuario
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('assinaturas.index') }}"
                                    class="nav-link {{ request()->routeIs('assinaturas.index') ? 'active' : '' }}">
                                    <i class="fas fa-dollar-sign"></i>
                                    <p>Assinaturas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('empresas.index') }}"
                                    class="nav-link {{ request()->routeIs('empresas.index') ? 'active' : '' }}">
                                    <i class="fas fa-building"></i>
                                    <p>Empresas</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
