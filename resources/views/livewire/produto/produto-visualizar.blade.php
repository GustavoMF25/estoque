<div>
    <div class="mb-3">
        <h5 class="mb-1"><strong>Informações do Produto</strong></h5>

        <p><strong>Nome:</strong> {{ $nome }}</p>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-hover table-bordered">
                <thead class="">
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produtos as $prod)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $prod->imagem) }}" class="img-circle" style="width: 40px;" alt="{{ $prod->nome }}">
                        </td>
                        <td>{{ $prod->nome }}</td>
                        <td>{{ $prod->preco ?? '—' }}</td>
                        <td>{{ $prod->ultimaMovimentacao->tipo ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($prod->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <hr>

</div>