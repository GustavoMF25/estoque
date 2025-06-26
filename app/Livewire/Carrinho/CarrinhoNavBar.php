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
    public function removerItem($nome)
    {
        $carrinho = session('carrinho', []);

        if (isset($carrinho[$nome])) {
            unset($carrinho[$nome]);
            session(['carrinho' => $carrinho]);
            $this->itens = $carrinho;
            $this->qtdItemCarrinho = array_sum(array_column($carrinho, 'quantidade'));

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Item removido do carrinho.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.carrinho.carrinho-nav-bar');
    }
}
