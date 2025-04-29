<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        
        <li class="nav-item dropdown">

            <button type="button" data-toggle="dropdown" class="btn p-0 border-0 bg-transparent"
                style="border-radius: 50%;" aria-label="Perfil do usuário">
                <img src="{{ !empty(Auth::user()->profile_photo_url) ? Auth::user()->profile_photo_url : asset('imagens/avatar.png') }}"
                    alt="{{ Auth::user()->name }}" class="rounded-circle img-fluid"
                    style="height: 3rem; width: 3rem; object-fit: cover;">
            </button>

            {{-- <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a> --}}
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                {{-- @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        </button>
                    @else
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                {{ Auth::user()->name }}

                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </span>
                    @endif --}}
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
