<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <x-application-mark class="block h-9 w-auto" />
        <span class="brand-text font-weight-light">{{ $empresa->nome }} - <small>Sistema </small></span>
    </a>

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
                <li class="nav-item">
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
                    <a href="{{ route('produtos.index') }}"
                        class="nav-link {{ request()->routeIs('produtos.index') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i>
                        <p>
                            Produto
                        </p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('produtos.catalogo') }}"
                        class="nav-link {{ request()->routeIs('produtos.catalogo') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i>
                        <p>
                            Catálogo
                        </p>
                    </a>
                </li> --}}
                @if (optional(auth()->user())->isAdmin())
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            
                            <p>
                                Configurações
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('usuarios.index') }}"
                                    class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}">
                                    <i class="fas fa-user nav-icon"></i>
                                    <p>Usuarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('empresa.edit') }}"
                                    class="nav-link {{ request()->routeIs('empresa.edit') ? 'active' : '' }}">
                                    <i class="fas fa-building"></i>
                                    <p>Empresa</p>
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
