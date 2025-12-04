@props(['vendido', 'disponivel'])

@if(!empty($vendido))
    <span class="badge bg-success">{{$vendido ?? 0}}</span>
@endif

@if(!empty($disponivel))
    <span class="badge bg-primary">{{$disponivel ?? 0}}</span>
@endif