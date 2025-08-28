<?php

namespace App\Livewire\Vendas;

use App\Models\Venda;
use Livewire\Component;

class AtualizarVenda extends Component
{

    public $formId;
    public $venda;
    public $protocolo;

    public function mount($id, $formId)
    {
        $this->formId = $formId;
        $this->venda = Venda::findOrFail($id);
        $this->protocolo = $this->venda->protocolo;
    }

    public function save()
    {
        $this->venda->update([
            'protocolo' => $this->protocolo
        ]);
        $this->venda->save();
        $this->dispatch('toastr:success', [
            'success' => 'Venda atualizada com sucesso!'
        ]);

        $this->dispatch('refreshTabelaVendas');
    }

    public function render()
    {
        return view('livewire.vendas.atualizar-venda');
    }
}
