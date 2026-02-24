<?php

namespace App\Livewire\Perfil;

use App\Models\Funcionalidade;
use App\Models\Perfil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PermissoesVincular extends Component
{
    public string $perfilSlug = '';
    public ?Perfil $perfil = null;
    public string $formId = '';

    public array $funcionalidadesSelecionadas = [];
    public array $funcionalidadesPorModulo = [];

    public function mount(string $perfilSlug, string $formId = 'formPerfilPermissoes')
    {
        $this->perfilSlug = $perfilSlug;
        $this->formId = $formId ?: 'formPerfilPermissoes';

        $this->perfil = Perfil::query()
            ->where('slug', $this->perfilSlug)
            ->firstOrFail();

        $funcionalidades = Funcionalidade::query()
            ->where('ativo', true)
            ->orderBy('modulo')
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        $this->funcionalidadesPorModulo = $funcionalidades
            ->groupBy(fn (Funcionalidade $funcionalidade) => $funcionalidade->modulo ?: 'Sem módulo')
            ->map(fn ($itens) => $itens->values()->toArray())
            ->toArray();

        $this->funcionalidadesSelecionadas = DB::table('perfil_funcionalidade')
            ->where('perfil', $this->perfilSlug)
            ->pluck('funcionalidade_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function save()
    {
        $idsValidos = Funcionalidade::query()->pluck('id')->all();
        $idsSelecionados = collect($this->funcionalidadesSelecionadas)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $idsValidos, true))
            ->unique()
            ->values();

        DB::transaction(function () use ($idsSelecionados) {
            DB::table('perfil_funcionalidade')->where('perfil', $this->perfilSlug)->delete();

            if ($idsSelecionados->isEmpty()) {
                return;
            }

            $linhas = $idsSelecionados->map(fn ($funcionalidadeId) => [
                'perfil' => $this->perfilSlug,
                'funcionalidade_id' => $funcionalidadeId,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            DB::table('perfil_funcionalidade')->insert($linhas);
        });

        $this->dispatch('toastr:success', [
            'success' => "Permissões do perfil {$this->perfil->nome} atualizadas.",
        ]);
        $this->dispatch('fecharModal');
    }

    public function render()
    {
        return view('livewire.perfil.permissoes-vincular');
    }
}
