@php
    $canEdit = !empty($edit) && ((!empty($edit) && auth()->user()->perfil === 'admin') || $edit['permitir']);
    $hasDropdown = !empty($show) || !empty($custonComponents) || !empty($custonComponent) || !empty($remove) || !empty($restore) || !empty($pdf);
@endphp

    @if (!empty($show))
        <x-table.btn-ver :title="$show['title']" :componente="$show['componente']" :props="$show['props']" :modal="$show['modal']" :route="$show['route']" />
    @endif

    @if (!empty($edit))
        @if ((!empty($edit) && auth()->user()->perfil === 'admin') || $edit['permitir'])
            <x-table.btn-edit :title="$edit['title']" :componente="$edit['componente']" :props="$edit['props']" :formId="$edit['formId']" />
        @endif
    @endif
    @if (!empty($custonComponent))
        @if ((!empty($custonComponent) && auth()->user()->perfil === 'admin') || $custonComponent['permitir'])
            <x-table.btn-custon-component :icon="$custonComponent['icon']" :title="$custonComponent['title']" :componente="$custonComponent['componente']" :props="$custonComponent['props']" :formId="$custonComponent['formId']" />
        @endif
    @endif

    @if ($hasDropdown)
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false" title="Mais ações">
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @if (!empty($show))
                    @if ($show['modal'])
                        <button
                            class="dropdown-item"
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
                            "
                        >
                            <i class="fas fa-eye mr-2"></i>{{ $show['title'] ?? 'Ver' }}
                        </button>
                    @else
                        <a href="{{ $show['route'] }}" class="dropdown-item">
                            <i class="fas fa-eye mr-2"></i>{{ $show['title'] ?? 'Ver' }}
                        </a>
                    @endif
                @endif

                @if (!empty($custonComponents) && is_array($custonComponents))
                    @foreach ($custonComponents as $custonItem)
                        @if ((!empty($custonItem) && auth()->user()->perfil === 'admin') || $custonItem['permitir'])
                            <button
                                class="dropdown-item"
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
                                "
                            >
                                <i class="{{ $custonItem['icon'] }} mr-2"></i>{{ $custonItem['title'] }}
                            </button>
                        @endif
                    @endforeach
                @elseif (!empty($custonComponent))
                    @if ((!empty($custonComponent) && auth()->user()->perfil === 'admin') || $custonComponent['permitir'])
                        <button
                            class="dropdown-item"
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
                            "
                        >
                            <i class="{{ $custonComponent['icon'] }} mr-2"></i>{{ $custonComponent['title'] }}
                        </button>
                    @endif
                @endif

                @if (!empty($remove) && auth()->user()->perfil === 'admin')
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

    @if (!empty($pdf))
        <a href="{{ $pdf['route'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-file-pdf"></i>
        </a>
    @endif


</div>
