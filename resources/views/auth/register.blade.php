<x-guest-layout>
    <div class="login-box">
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <x-authentication-card-logo />

            </div>
            <x-validation-errors class="mb-4" />
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="input-group mb-3">

                        <x-input id="name" class="form-control" type="text" name="name" placeholder="Name"
                            :value="old('name')" required autofocus autocomplete="name" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <x-input id="empresa_nome" class="form-control" type="text" name="empresa_nome"
                            placeholder="Nome da Empresa" :value="old('empresa_nome')" required />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-building"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <x-input id="empresa_cnpj" class="form-control" type="text" name="empresa_cnpj"
                            placeholder="CNPJ (opcional)" :value="old('empresa_cnpj')" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <x-input id="email" class="form-control" type="email" name="email" placeholder="Email"
                            :value="old('email')" required autocomplete="username" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <x-input id="password" class="form-control" type="password" placeholder="Confirmar a senha"
                            name="password" required autocomplete="new-password" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <x-input id="password_confirmation" class="form-control" type="password"
                            placeholder="Confirmar a senha" name="password_confirmation" required
                            autocomplete="new-password" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <x-label for="terms">
                                        <div class="flex items-center">
                                            <x-checkbox name="terms" id="terms" required />

                                            <div class="ms-2">
                                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                                    'terms_of_service' =>
                                                        '<a target="_blank" href="' .
                                                        route('terms.show') .
                                                        '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                        __('Terms of Service') .
                                                        '</a>',
                                                    'privacy_policy' =>
                                                        '<a target="_blank" href="' .
                                                        route('policy.show') .
                                                        '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                        __('Privacy Policy') .
                                                        '</a>',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </x-label>
                                </div>
                            </div>
                            <!-- /.col -->
                        @endif
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                        <!-- /.col -->
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-guest-layout>
