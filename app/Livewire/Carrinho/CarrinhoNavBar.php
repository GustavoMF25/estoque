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

    public function removerItem(string $nome)
    {
        $carrinho = session('carrinho', []);

        if (isset($carrinho[$nome])) {
            unset($carrinho[$nome]);
        }


        $this->itens = $carrinho;
        $this->qtdItemCarrinho = $this->calculaQtd($carrinho);

        if (empty($carrinho)) {
            return session()->forget('carrinho');
        }

        return session(['carrinho' => $carrinho]);
    }
    
    private function calculaQtd(array $itens): int
    {
        if (empty($itens)) return 0;
        return array_sum(array_map(fn($i) => (int)($i['quantidade'] ?? 0), $itens));
    }

    public function render()
    {
        return view('livewire.carrinho.carrinho-nav-bar');
    }
}
