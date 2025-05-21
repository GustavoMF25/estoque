<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use Livewire\Component;

class ModalCadastrarVenda extends Component
{
    public int $quantidade;
    public $produtos;
    public $formId;
    public $props;


    public function mount($formId)
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'saida');
        })->count();

        $this->produtos =  Produto::select('nome')
            ->whereHas('ultimaMovimentacao', function ($query) {
                $query->where('tipo', 'disponivel');
            })
            ->selectRaw('COUNT(*) as total')
            ->groupBy('nome')
            ->get();
    }

    public function render()
    {
        return view('livewire.produto.modal-cadastrar-venda');
    }
}
