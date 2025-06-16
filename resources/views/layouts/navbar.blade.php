<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Itens</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">Confirmar compra</a>
            </div>
        </li> 

        <li class="nav-item dropdown">

            <button type="button" data-toggle="dropdown" class="btn p-0 border-0 bg-transparent"
                style="border-radius: 50%;" aria-label="Perfil do usuário">
                <img src="{{ !empty(Auth::user()->profile_photo_url) ? Auth::user()->profile_photo_url : asset('imagens/avatar.png') }}"
                    alt="{{ Auth::user()->name }}" class="rounded-circle img-fluid"
                    style="height: 3rem; width: 3rem; object-fit: cover;">
            </button>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- Account Management -->
                <span class="dropdown-header">{{ __('Manage Account') }}</span>

                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.show') }}" class="dropdown-item">
                    <i class="fas fa-solid fa-user mr-2"></i>
                    {{ __('Profile') }}
                </a>
                {{-- @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('api-tokens.index') }}" class="dropdown-item">
                        <i class="fas fa-route mr-2"></i>
                        {{ __('API Tokens') }}
                    </a>
                @endif --}}
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <a href="{{ route('logout') }}" class="dropdown-item" @click.prevent="$root.submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
