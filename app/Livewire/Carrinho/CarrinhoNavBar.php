<?php

namespace App\Livewire\Carrinho;

use Livewire\Component;

class CarrinhoNavBar extends Component
{
    public array $itens = [];
    public int $qtdItemCarrinho = 0;

    protected $listeners = ['atualizarCarrinho' => 'mount'];

    public function mount()
    {
        $this->itens = session('carrinho', []);
        $this->qtdItemCarrinho = array_sum(array_column($this->itens, 'quantidade'));
    }

    public function render()
    {
        return view('livewire.carrinho.carrinho-nav-bar');
    }
}
