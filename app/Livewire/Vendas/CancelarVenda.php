<?php

namespace App\Livewire\Vendas;

use App\Models\Venda;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CancelarVenda extends Component
{
    public $id;
    public $venda;
    public $motivo = '';

    protected $rules = [
        'motivo' => 'nullable|string|max:500',
    ];

    public function mount($id)
    {
        $this->id = $id;
        $this->venda = Venda::with(['itens.produto', 'usuario'])->findOrFail($id);
    }

    public function cancelar()
    {
        $this->validate();

        if ($this->venda->status === 'cancelada') {
            return $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Esta venda ja esta cancelada.',
            ]);
        }

        DB::transaction(function () {
            $this->venda->update([
                'status' => 'cancelada',
                'aprovacao_status' => null,
                'aprovacao_motivo' => $this->motivo ?: $this->venda->aprovacao_motivo,
                'aprovacao_admin_id' => auth()->id(),
            ]);
        });

        $this->dispatch('refreshTabelaVendas');
        $this->dispatch('fecharModal');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Venda cancelada com sucesso.',
        ]);
    }

    public function render()
    {
        return view('livewire.vendas.cancelar-venda');
    }
}
