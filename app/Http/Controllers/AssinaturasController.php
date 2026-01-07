<?php

namespace App\Http\Controllers;

use App\Models\Assinaturas;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AssinaturasController extends Controller
{
    /**
     * 🧭 Lista todas as assinaturas
     */
    public function index()
    {
        // $assinaturas = Assinaturas::with('empresa')
        //     ->orderByDesc('data_vencimento')
        //     ->paginate(15);
        // 🔹 Superusuário visualiza todas as empresas com ou sem assinatura
        $empresas = Empresa::with('assinatura')->orderBy('nome')->paginate(15);

        return view('assinaturas.index', compact('empresas'));

        // return view('assinaturas.index', compact('assinaturas'));
    }

    public function create($empresaId)
    {
        $empresas = Empresa::findOrFail($empresaId);
        return view('assinaturas.create', compact('empresas'));
    }

    public function store(Request $request, $empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);

        $request->validate([
            'plano_nome' => 'required|string|max:60',
            'valor' => 'required|numeric|min:0',
            'periodicidade' => 'required|in:mensal,trimestral,anual,vitalicio',
            'status' => 'nullable|string',
            'em_teste' => 'nullable|boolean',
            'trial_expira_em' => 'nullable|date',
        ]);

        $periodicidade = $this->resolvePeriodicidade($request->periodicidade);
        $status = $this->resolveStatus($request->status);
        $emTeste = $request->boolean('em_teste');
        $trialExpira = $emTeste
            ? ($request->filled('trial_expira_em') ? Carbon::parse($request->trial_expira_em) : now()->addDays(7))
            : null;

        $assinatura = Assinaturas::create([
            'empresa_id' => $empresa->id,
            'plano' => $request->plano_nome,
            'valor_mensal' => $request->valor,
            'data_inicio' => now(),
            'periodicidade' => $periodicidade,
            'status' => $emTeste ? 'ativo' : $status,
            'em_teste' => $emTeste,
            'trial_expira_em' => $trialExpira,
        ]);

        if ($assinatura->em_teste) {
            $assinatura->data_vencimento = $assinatura->trial_expira_em;
        } else {
            $assinatura->definirDatasPorPeriodicidade();
        }
        $assinatura->save();

        return redirect()
            ->route('assinaturas.index')
            ->with('success', 'Assinatura criada com sucesso para ' . $empresa->nome . '!');
    }

    public function show($id)
    {
        $assinatura = Assinaturas::with(['empresa.modulos', 'faturas'])->findOrFail($id);

        return $this->renderAssinatura($assinatura);
    }

    public function minha()
    {
        $user = Auth::user();

        abort_unless($user && ($user->isSuperAdmin() || $user->isAdmin()), 403);

        $assinatura = Assinaturas::with(['empresa.modulos', 'faturas'])
            ->where('empresa_id', $user->empresa_id)
            ->firstOrFail();

        return $this->renderAssinatura($assinatura, true);
    }

    /**
     * ✏️ Exibe o formulário de edição
     */
    public function edit($id)
    {
        abort_unless(optional(auth()->user())->isSuperAdmin(), 403);

        $assinatura = Assinaturas::findOrFail($id);
        $empresas = Empresa::all();

        return view('assinaturas.edit', compact('assinatura', 'empresas'));
    }

    /**
     * 🔁 Atualiza uma assinatura existente
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'plano' => 'required|string|max:60',
            'valor_mensal' => 'required|numeric|min:0',
            'data_inicio' => 'required|date',
            'status' => 'required|in:pendente,ativo,atrasado,cancelado',
            'metodo_pagamento' => 'required|in:manual,pix,asaas,pagseguro',
            'periodicidade' => 'required|in:mensal,trimestral,anual,vitalicio',
            'em_teste' => 'nullable|boolean',
            'trial_expira_em' => 'nullable|date',
            'data_vencimento' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $assinatura = Assinaturas::findOrFail($id);
            $assinatura->fill($validated);
            $assinatura->periodicidade = $this->resolvePeriodicidade($request->periodicidade);
            $assinatura->em_teste = $request->boolean('em_teste');

            if ($assinatura->em_teste) {
                $assinatura->trial_expira_em = $request->filled('trial_expira_em')
                    ? Carbon::parse($request->trial_expira_em)
                    : now()->addDays(7);
                $assinatura->data_inicio = Carbon::parse($request->data_inicio);
                $assinatura->data_vencimento = $assinatura->trial_expira_em;
                $assinatura->status = 'ativo';
            } else {
                $assinatura->trial_expira_em = null;
                if ($assinatura->periodicidade === 'vitalicio') {
                    $assinatura->data_vencimento = null;
                } else {
                    $assinatura->data_inicio = Carbon::parse($request->data_inicio);
                    $assinatura->definirDatasPorPeriodicidade();
                    if ($request->filled('data_vencimento')) {
                        $assinatura->data_vencimento = Carbon::parse($request->data_vencimento);
                    }
                }

                $assinatura->status = $this->resolveStatus($request->status);
            }

            $assinatura->save();
            DB::commit();

            return redirect()
                ->route('assinaturas.index')
                ->with('success', 'Assinatura atualizada com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * ❌ Remove uma assinatura
     */
    public function destroy($id)
    {
        try {
            $assinatura = Assinaturas::findOrFail($id);
            $assinatura->delete();

            return redirect()
                ->route('assinaturas.index')
                ->with('success', 'Assinatura removida com sucesso!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao remover assinatura: ' . $e->getMessage());
        }
    }

    /**
     * 🔄 Ação rápida: renovar assinatura manualmente (+30 dias)
     */
    public function renovar($id)
    {
        try {
            $assinatura = Assinaturas::findOrFail($id);
            $assinatura->renovar();

            return back()->with('success', 'Assinatura renovada por mais 30 dias!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao renovar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * 🕒 Atualiza status automaticamente (usado por cron)
     */
    public function verificarVencidas()
    {
        $assinaturas = Assinaturas::all();

        foreach ($assinaturas as $assinatura) {
            $assinatura->atualizarStatus();
        }

        return response()->json(['message' => 'Status das assinaturas verificados com sucesso!']);
    }

    protected function renderAssinatura(Assinaturas $assinatura, bool $propria = false)
    {
        $user = Auth::user();

        if ($user && !$user->isSuperAdmin()) {
            abort_if($assinatura->empresa_id !== $user->empresa_id, 403);
        }

        $diasRestantes = $assinatura->em_teste
            ? ($assinatura->trial_expira_em ? now()->diffInDays($assinatura->trial_expira_em, false) : null)
            : ($assinatura->data_vencimento ? now()->diffInDays($assinatura->data_vencimento, false) : null);

        $progressoCiclo = null;
        if ($assinatura->em_teste && $assinatura->trial_expira_em) {
            $totalCiclo = max(1, $assinatura->data_inicio?->diffInDays($assinatura->trial_expira_em) ?? 7);
            $diasConsumidos = $assinatura->data_inicio?->diffInDays(now()) ?? 0;
            $diasConsumidos = max(0, min($totalCiclo, $diasConsumidos));
            $progressoCiclo = round(($diasConsumidos / $totalCiclo) * 100, 0);
        } elseif ($assinatura->data_inicio && $assinatura->data_vencimento) {
            $totalCiclo = max(1, $assinatura->data_inicio->diffInDays($assinatura->data_vencimento));
            $diasConsumidos = $assinatura->data_inicio->diffInDays(now());
            $diasConsumidos = max(0, min($totalCiclo, $diasConsumidos));
            $progressoCiclo = round(($diasConsumidos / $totalCiclo) * 100, 0);
        }

        $faturasPendentes = $assinatura->faturas->where('status', '!=', 'pago')->count();
        $valorAnual = $assinatura->valor_mensal ? $assinatura->valor_mensal * 12 : 0;
        $ultimasFaturas = $assinatura->faturas->sortByDesc('created_at')->take(5);

        return view('assinaturas.show', [
            'assinatura' => $assinatura,
            'diasRestantes' => $diasRestantes,
            'progressoCiclo' => $progressoCiclo,
            'faturasPendentes' => $faturasPendentes,
            'valorAnual' => $valorAnual,
            'ultimasFaturas' => $ultimasFaturas,
            'podeRenovar' => $user?->isSuperAdmin(),
            'podeEditarModulos' => $user && ($user->isSuperAdmin() || $user->isAdmin()),
        ]);
    }

    protected function resolvePeriodicidade(?string $valor): string
    {
        $valor = strtolower($valor ?? 'mensal');

        return array_key_exists($valor, Assinaturas::PERIODICIDADES)
            ? $valor
            : 'mensal';
    }

    protected function resolveStatus(?string $status): string
    {
        $status = strtolower($status ?? 'pendente');

        return in_array($status, ['ativo', 'atrasado', 'pendente', 'cancelado'])
            ? $status
            : 'pendente';
    }
}
