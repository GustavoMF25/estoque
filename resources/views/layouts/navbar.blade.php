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
            <livewire:notificacoes.notificacoes-nav-bar />
        </li>
        <li class="nav-item dropdown">
            <livewire:carrinho.carrinho-nav-bar />
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

                    <a href="{{ route('logout') }}" class="dropdown-item" @click.prevent="$root.submit();" onclick="localStorage.removeItem('novaVersaoAviso');">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
