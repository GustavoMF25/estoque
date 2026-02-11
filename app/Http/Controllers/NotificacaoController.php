<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    public function index()
    {
        $notificacoes = Notificacao::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('notificacoes.index', compact('notificacoes'));
    }

    public function marcarComoLida(Notificacao $notificacao): RedirectResponse
    {
        if ($notificacao->user_id !== auth()->id()) {
            abort(403);
        }

        $notificacao->update(['lida_em' => now()]);

        return redirect()->route('notificacoes.index');
    }
}
