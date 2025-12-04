<x-guest-layout>
    <div class="login-box">
        <div class="card shadow-lg card-outline card-primary">
            <div class="card-header text-center border-0">
                <x-authentication-card-logo class="mb-2" />
            </div>
            <div class="card-body pt-0">
                <p class="login-box-msg text-secondary">
                    Entre para continuar gerenciando estoques, unidades e vendas em tempo real.
                </p>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <x-validation-errors class="mb-3" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                            placeholder="E-mail corporativo" required autofocus autocomplete="username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Senha" required
                            autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember_me" name="remember">
                                <label for="remember_me">
                                    {{ __('Remember me') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 text-right">
                            @if (Route::has('password.request'))
                                <a class="font-weight-medium small text-primary" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ __('Log in') }}
                    </button>
                </form>

                <div class="mt-4 text-muted small">
                    <div class="d-flex align-items-start mb-2">
                        <span class="badge badge-light border mr-2"><i
                                class="fas fa-check-circle text-success"></i></span>
                        <span>Acesso seguro e auditável por usuário e empresa.</span>
                    </div>
                    <div class="d-flex align-items-start">
                        <span class="badge badge-light border mr-2"><i class="fas fa-headset text-primary"></i></span>
                        <span>Suporte disponível para administradores 24/7.</span>
                    </div>
                </div>
            </div>
        </div>
        <p class="mt-3 text-center text-muted small">
            &copy; {{ now()->year }} SyntaxWeb
        </p>
    </div>
</x-guest-layout>
