@props(['status'])

@php
    $status = strtolower($status);
@endphp
@switch($status)
    @case('entrada')
        <span class="badge bg-primary">Entrada</span>
        @break

    @case('saida')
        <span class="badge bg-danger">Vendido</span>
        @break

    @case('inativo')
        <span class="badge bg-danger">Inativo</span>
        @break

    @case('disponivel')
        <span class="badge bg-success">Disponível</span>
        @break

    @case('ativo')
        <span class="badge bg-success">Ativo</span>
        @break

    @case('ajuste_positivo')
        <span class="badge bg-info text-dark">Ajuste +</span>
        @break

    @case('ajuste_negativo')
        <span class="badge bg-warning text-dark">Ajuste -</span>
        @break

    @case('transferencia')
        <span class="badge bg-secondary">Transferência</span>
        @break

    @case('reserva')
        <span class="badge bg-dark">Reserva</span>
        @break

    @case('cancelamento')
        <span class="badge bg-light text-dark">Cancelado</span>
        @break

    @case('danificado')
        <span class="badge bg-danger">Danificado</span>
        @break

    @case('expirado')
        <span class="badge bg-warning text-dark">Expirado</span>
        @break

    @case('retorno')
        <span class="badge bg-success">Retorno</span>
        @break

    @default
        <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
@endswitch
