<x-app-layout>
    <x-basic.content-page :title="__('Estoque a Chegar')" :class="'card-secondary'">
        @if (in_array(optional(auth()->user())->perfil, ['admin', 'operador']))
        <form method="POST" action="{{ route('produto-chegadas.store') }}" class="mb-4" id="form-estoque-chegada">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <label>Produto</label>
                    <select id="produto_id" name="produto_id" class="form-control select2" required>
                        <option value="">Selecione</option>
                        @foreach ($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Quantidade</label>
                    <input type="number" min="1" name="quantidade_total" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Previsão de chegada</label>
                    <input type="text" id="previsao_chegada" name="previsao_chegada" class="form-control inputmask-datetime"
                        placeholder="dd/mm/aaaa" value="{{ old('previsao_chegada') }}" maxlength="10"
                        inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}">
                </div>
                <div class="col-md-3">
                    <label>Observação</label>
                    <input type="text" name="observacao" class="form-control" maxlength="1000">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Registrar estoque a chegar</button>
            </div>
        </form>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Comprometida</th>
                        <th>Disponível</th>
                        <th>Previsão</th>
                        <th>Status</th>
                        <th>Criado por</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($chegadas as $chegada)
                    <tr>
                        <td>{{ $chegada->id }}</td>
                        <td>{{ $chegada->produto->nome ?? 'Produto removido' }}</td>
                        <td>{{ $chegada->quantidade_total }}</td>
                        <td>{{ $chegada->quantidade_comprometida }}</td>
                        <td>{{ $chegada->quantidade_disponivel }}</td>
                        <td>{{ $chegada->previsao_chegada?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            @if ($chegada->status === 'aberto')
                            <span class="badge badge-warning">Aberto</span>
                            @elseif ($chegada->status === 'recebido')
                            <span class="badge badge-success">Recebido</span>
                            @else
                            <span class="badge badge-secondary">Cancelado</span>
                            @endif
                        </td>
                        <td>{{ $chegada->criadoPor->name ?? '-' }}</td>
                        <td>
                            @if (in_array(optional(auth()->user())->perfil, ['admin', 'operador']) && $chegada->status === 'aberto')
                            <form method="POST" action="{{ route('produto-chegadas.receber', $chegada) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"
                                    onclick="return confirm('Confirmar recebimento deste lote?')">Receber</button>
                            </form>
                            @if ((int) $chegada->quantidade_comprometida === 0)
                            <form method="POST" action="{{ route('produto-chegadas.cancelar', $chegada) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Cancelar este lote?')">Cancelar</button>
                            </form>
                            @endif
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Nenhum lote de estoque a chegar cadastrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $chegadas->links() }}
        </div>
    </x-basic.content-page>
</x-app-layout>

@push('styles')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $('#previsao_chegada').inputmask('datetime', {
            inputFormat: 'dd/mm/yyyy',
            placeholder: 'dd/mm/aaaa',
            clearIncomplete: true,
            showMaskOnHover: false,
        });

        $('#form-estoque-chegada').on('submit', function (e) {
            const campoData = document.getElementById('previsao_chegada');
            const valor = (campoData.value || '').trim();

            if (!valor) {
                return;
            }

            const match = valor.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!match) {
                e.preventDefault();
                if (window.toastr) {
                    toastr.warning('Data inválida. Use o formato dd/mm/aaaa.');
                } else {
                    alert('Data inválida. Use o formato dd/mm/aaaa.');
                }
                campoData.focus();
                return;
            }

            const dia = parseInt(match[1], 10);
            const mes = parseInt(match[2], 10) - 1;
            const ano = parseInt(match[3], 10);
            const data = new Date(ano, mes, dia);
            const valida = data.getFullYear() === ano && data.getMonth() === mes && data.getDate() === dia;

            if (!valida) {
                e.preventDefault();
                if (window.toastr) {
                    toastr.warning('Data inválida. Verifique o dia, mês e ano informados.');
                } else {
                    alert('Data inválida. Verifique o dia, mês e ano informados.');
                }
                campoData.focus();
            }
        });
    });
</script>
@endpush
