<?php

namespace App\Livewire\Vendas;

use App\Models\Notificacao;
use App\Models\Venda;
use App\Services\VendaEstoqueService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AprovarVenda extends Component
{
    public $id;
    public $venda;
    public $motivo = '';

    protected $rules = [
        'motivo' => 'required|string|min:3',
    ];

    public function mount($id)
    {
        $this->id = $id;
        $this->venda = Venda::with(['itens.produto', 'usuario'])->findOrFail($id);
    }

    public function aprovar()
    {
        if ($this->venda->aprovacao_status !== 'pendente') {
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->venda->itens as $item) {
                VendaEstoqueService::aplicarSaidaItem($item, (int) $item->quantidade);
            }

            $this->venda->update([
                'status' => 'aberta',
                'aprovacao_status' => 'aprovada',
                'aprovacao_admin_id' => auth()->id(),
                'aprovacao_motivo' => null,
            ]);

            Notificacao::create([
                'user_id' => $this->venda->user_id,
                'titulo' => 'Venda aprovada',
                'mensagem' => "A venda #{$this->venda->id} foi aprovada.",
                'tipo' => 'venda.aprovada',
                'dados' => ['venda_id' => $this->venda->id],
            ]);

            DB::commit();

            $this->dispatch('refreshTabelaVendas');
            $this->dispatch('fecharModal');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Venda aprovada com sucesso.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function recusar()
    {
        $this->validate();

        $this->venda->update([
            'status' => 'cancelada',
            'aprovacao_status' => 'recusada',
            'aprovacao_admin_id' => auth()->id(),
            'aprovacao_motivo' => $this->motivo,
        ]);

        Notificacao::create([
            'user_id' => $this->venda->user_id,
            'titulo' => 'Venda recusada',
            'mensagem' => "A venda #{$this->venda->id} foi recusada. Motivo: {$this->motivo}",
            'tipo' => 'venda.recusada',
            'dados' => ['venda_id' => $this->venda->id],
        ]);

        $this->dispatch('refreshTabelaVendas');
        $this->dispatch('fecharModal');
        $this->dispatch('toast', ['type' => 'warning', 'message' => 'Venda recusada.']);
    }

    public function render()
    {
        return view('livewire.vendas.aprovar-venda');
    }
}
