<x-app-layout>

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Usuarios') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">{{ __('Usuarios') }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <div class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h4 class="card-title w-100">
                    {{ __('Usuarios') }}
                </h4>
            </div>
            <div class="card-body">
                <div class="container">
                    @livewire('user-table')

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    
                </div>

            </div>
        </div>
    </div>


</x-app-layout>
