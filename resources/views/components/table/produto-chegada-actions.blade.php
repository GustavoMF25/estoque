@php
    $podeGerenciar = in_array(optional(auth()->user())->perfil, ['admin', 'operador']);
    $podeGerenciarLote = $podeGerenciar && $chegada->status === 'aberto';
@endphp

@if (!$podeGerenciarLote)
    -
@else
    @php
        $postActions = [
            [
                'title' => 'Receber',
                'route' => route('produto-chegadas.receber', $chegada),
                'method' => 'POST',
                'icon' => 'fas fa-check-circle',
                'class' => 'text-success',
                'confirm' => 'Confirmar recebimento deste lote?',
                'permitir' => true,
            ],
            [
                'title' => 'Cancelar',
                'route' => route('produto-chegadas.cancelar', $chegada),
                'method' => 'POST',
                'icon' => 'fas fa-ban',
                'class' => 'text-danger',
                'confirm' => 'Cancelar este lote?',
                'permitir' => (int) $chegada->quantidade_comprometida === 0,
            ],
        ];
    @endphp

    @include('components.table.btn-table-actions', [
        'show' => '',
        'custonComponents' => '',
        'custonComponent' => '',
        'remove' => '',
        'restore' => '',
        'pdf' => '',
        'edit' => '',
        'linkActions' => [
            [
                'title' => 'Editar lote',
                'route' => route('produto-chegadas.edit', $chegada),
                'icon' => 'fas fa-pen',
                'permitir' => true,
            ],
        ],
        'postActions' => $postActions,
    ])
@endif
