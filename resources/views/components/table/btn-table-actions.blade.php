@php
    $isAdmin = auth()->check() && auth()->user()->perfil === 'admin';
    $canEdit = !empty($edit) && ($isAdmin || ($edit['permitir'] ?? false));
    $canCustom = !empty($custonComponent) && ($isAdmin || ($custonComponent['permitir'] ?? false));
    $hasDropdown = $canEdit
        || !empty($show)
        || !empty($custonComponents)
        || $canCustom
        || !empty($remove)
        || !empty($restore)
        || !empty($pdf);
@endphp

@if ($hasDropdown)
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false" title="Opções">
            Opções
        </button>

        <div class="dropdown-menu dropdown-menu-right">
            @if ($canEdit)
                <button class="dropdown-item"
                    onclick="
                        window.dispatchEvent(new CustomEvent('abrirModal', {
                            detail: {
                                titulo: {{ \Illuminate\Support\Js::from($edit['title']) }},
                                componente: {{ \Illuminate\Support\Js::from($edit['componente']) }},
                                props: {{ \Illuminate\Support\Js::from($edit['props']) }},
                                formId: {{ \Illuminate\Support\Js::from(!empty($edit['formId']) ? $edit['formId'] : null) }}
                            }
                        }));
                        $('#modal-sm').modal('show');
                    ">
                    <i class="fas fa-pen mr-2"></i>{{ $edit['title'] ?? 'Editar' }}
                </button>
            @endif

            @if (!empty($show))
                @if ($show['modal'])
                    <button class="dropdown-item"
                        onclick="
                            window.dispatchEvent(new CustomEvent('abrirModal', {
                                detail: {
                                    titulo: {{ \Illuminate\Support\Js::from($show['title']) }},
                                    componente: {{ \Illuminate\Support\Js::from($show['componente']) }},
                                    props: {{ \Illuminate\Support\Js::from($show['props']) }},
                                    formId: {{ \Illuminate\Support\Js::from(!empty($show['formId']) ? $show['formId'] : null) }}
                                }
                            }));
                            $('#modal-sm').modal('show');
                        ">
                        <i class="fas fa-eye mr-2"></i>{{ $show['title'] ?? 'Visualizar' }}
                    </button>
                @else
                    <a href="{{ $show['route'] }}" class="dropdown-item">
                        <i class="fas fa-eye mr-2"></i>{{ $show['title'] ?? 'Visualizar' }}
                    </a>
                @endif
            @endif

            @if (!empty($custonComponents) && is_array($custonComponents))
                @foreach ($custonComponents as $custonItem)
                    @if ($isAdmin || ($custonItem['permitir'] ?? false))
                        <button class="dropdown-item"
                            onclick="
                                window.dispatchEvent(new CustomEvent('abrirModal', {
                                    detail: {
                                        titulo: {{ \Illuminate\Support\Js::from($custonItem['title']) }},
                                        componente: {{ \Illuminate\Support\Js::from($custonItem['componente']) }},
                                        props: {{ \Illuminate\Support\Js::from($custonItem['props']) }},
                                        formId: {{ \Illuminate\Support\Js::from(!empty($custonItem['formId']) ? $custonItem['formId'] : null) }}
                                    }
                                }));
                                $('#modal-sm').modal('show');
                            ">
                            <i class="{{ $custonItem['icon'] }} mr-2"></i>{{ $custonItem['title'] }}
                        </button>
                    @endif
                @endforeach
            @elseif ($canCustom)
                <button class="dropdown-item"
                    onclick="
                        window.dispatchEvent(new CustomEvent('abrirModal', {
                            detail: {
                                titulo: {{ \Illuminate\Support\Js::from($custonComponent['title']) }},
                                componente: {{ \Illuminate\Support\Js::from($custonComponent['componente']) }},
                                props: {{ \Illuminate\Support\Js::from($custonComponent['props']) }},
                                formId: {{ \Illuminate\Support\Js::from(!empty($custonComponent['formId']) ? $custonComponent['formId'] : null) }}
                            }
                        }));
                        $('#modal-sm').modal('show');
                    ">
                    <i class="{{ $custonComponent['icon'] }} mr-2"></i>{{ $custonComponent['title'] }}
                </button>
            @endif

            @if (!empty($remove) && $isAdmin)
                <form action="{{ $remove['route'] }}" method="POST"
                    onsubmit="return confirm('Deseja realmente excluir este registro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-trash-alt mr-2"></i>Excluir
                    </button>
                </form>
            @endif

            @if (!empty($restore))
                <form action="{{ $restore['route'] }}" method="POST"
                    onsubmit="return confirm('Deseja realmente restaurar este registro?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sync-alt mr-2"></i>Restaurar
                    </button>
                </form>
            @endif

            @if (!empty($pdf))
                <a href="{{ $pdf['route'] }}" target="_blank" class="dropdown-item">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </a>
            @endif
        </div>
    </div>
@endif
