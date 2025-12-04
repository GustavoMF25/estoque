<x-app-layout>
    <div class="content">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                        <small class="text-muted">Visão geral para perfil: {{ ucfirst($perfil ?? 'operador') }}</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @forelse ($cards as $card)
                        @php
                            $isMoney = $card['is_money'] ?? false;
                            $rawValue = $card['value'] ?? 0;
                            $value = $isMoney
                                ? 'R$ ' . number_format((float) $rawValue, 2, ',', '.')
                                : number_format((float) $rawValue, 0, '', '.');
                        @endphp
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="small-box bg-{{ $card['variant'] ?? 'secondary' }}">
                                <div class="inner">
                                    <h3 class="text-white">{{ $value }}</h3>
                                    <p class="mb-1">{{ $card['title'] }}</p>
                                    <small>{{ $card['subtitle'] ?? '' }}</small>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-{{ $card['icon'] ?? 'chart-bar' }}"></i>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                Nenhuma informação disponível para exibição.
                            </div>
                        </div>
                    @endforelse
                </div>

                @if (!empty($lists))
                    <div class="row">
                        @foreach ($lists as $list)
                            <div class="col-lg-6">
                                <div class="card card-outline card-{{ $list['variant'] ?? 'secondary' }}">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $list['title'] }}</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($list['items'] as $item)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="font-weight-bold">{{ $item['primary'] }}</div>
                                                        <small class="text-muted">{{ $item['secondary'] ?? '' }}</small>
                                                    </div>
                                                    @if (!empty($item['badge']))
                                                        <span class="badge badge-{{ $item['badge_variant'] ?? 'primary' }}">
                                                            {{ $item['badge'] }}
                                                        </span>
                                                    @endif
                                                </li>
                                            @empty
                                                <li class="list-group-item text-muted">
                                                    Nenhum registro encontrado.
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
