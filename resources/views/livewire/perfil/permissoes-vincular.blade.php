<div>
    <form wire:submit.prevent="save" id="{{ $formId }}">
        <div class="mb-3">
            <span class="badge badge-secondary text-uppercase">{{ $perfil?->slug }}</span>
            <h5 class="mt-2 mb-0">{{ $perfil?->nome }}</h5>
        </div>

        @foreach ($funcionalidadesPorModulo as $modulo => $itensModulo)
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title">{{ $modulo }}</h3>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        @foreach ($itensModulo as $funcionalidade)
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                        id="func_{{ $perfilSlug }}_{{ $funcionalidade['id'] }}"
                                        value="{{ $funcionalidade['id'] }}"
                                        wire:model="funcionalidadesSelecionadas">
                                    <label class="custom-control-label"
                                        for="func_{{ $perfilSlug }}_{{ $funcionalidade['id'] }}">
                                        {{ $funcionalidade['nome'] }}
                                    </label>
                                </div>
                                <small class="text-muted d-block pl-1">
                                    {{ $funcionalidade['slug'] }}
                                    @if (!empty($funcionalidade['rota']))
                                        | rota: {{ $funcionalidade['rota'] }}
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </form>
</div>
