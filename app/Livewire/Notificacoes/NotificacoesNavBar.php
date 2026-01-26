<?php

namespace App\Livewire\Notificacoes;

use App\Models\Notificacao;
use Livewire\Component;

class NotificacoesNavBar extends Component
{
    public $notificacoes = [];
    public $naoLidas = 0;

    public function mount()
    {
        $this->carregar();
    }

    public function carregar(): void
    {
        $this->notificacoes = Notificacao::where('user_id', auth()->id())
            ->whereNull('lida_em')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $this->naoLidas = Notificacao::where('user_id', auth()->id())
            ->whereNull('lida_em')
            ->count();
    }

    public function abrir($id)
    {
        $notificacao = Notificacao::where('user_id', auth()->id())->findOrFail($id);

        if (!$notificacao->lida_em) {
            $notificacao->update(['lida_em' => now()]);
        }

        $this->carregar();

        $rota = route('notificacoes.index');
        if ($notificacao->tipo === 'venda.aprovacao' && optional(auth()->user())->isAdmin()) {
            $rota = route('vendas.index');
        }

        return $this->redirect($rota);
    }

    public function render()
    {
        return view('livewire.notificacoes.notificacoes-nav-bar');
    }
}
