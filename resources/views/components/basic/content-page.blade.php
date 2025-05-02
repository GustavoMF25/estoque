<section class="content mt-2">

    @if (isset($principal) && $principal)
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    @endif
    <div class="card {{ $class ?? '' }}">
        <div class="card-header">
            <h3 class="card-title">{{ $title ?? null }}</h3>

            @if (!empty($btnCadastrarAdmin))
                <x-basic.botao-cadastrar :route="$btnCadastrarAdmin['route']" :title="$btnCadastrarAdmin['title']" />
            @endif
        </div>
        <div class="card-body">
            {{ $slot }}
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            {{ $footer ?? null }}
        </div>
        <!-- /.card-footer-->
    </div>
    <!-- /.card -->

</section>
