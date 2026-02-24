<x-app-layout>
    <x-basic.content-page :class="'card-secondary'" :title="__('Permissões por Perfil')">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="alert alert-info">
            Funcionalidades ativas no sistema: <strong>{{ $totalFuncionalidades }}</strong>.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Perfil</th>
                        <th>Slug</th>
                        <th class="text-center">Funcionalidades vinculadas</th>
                        <th class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perfis as $perfil)
                        <tr>
                            <td>{{ $perfil->nome }}</td>
                            <td><code>{{ $perfil->slug }}</code></td>
                            <td class="text-center">{{ (int) ($qtdPorPerfil[$perfil->slug] ?? 0) }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary"
                                    onclick="
                                        window.dispatchEvent(new CustomEvent('abrirModal', {
                                            detail: {
                                                titulo: {{ \Illuminate\Support\Js::from('Permissões do perfil: ' . $perfil->nome) }},
                                                componente: 'perfil.permissoes-vincular',
                                                props: { perfilSlug: {{ \Illuminate\Support\Js::from($perfil->slug) }}, size: 'modal-xl' },
                                                formId: {{ \Illuminate\Support\Js::from('formPerfilPermissoes_' . $perfil->slug) }}
                                            }
                                        }));
                                        $('#modal-sm').modal('show');
                                    ">
                                    Vincular funcionalidades
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Nenhum perfil ativo encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-basic.content-page>
</x-app-layout>
