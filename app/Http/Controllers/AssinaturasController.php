<?php

namespace App\Http\Controllers;

use App\Models\Assinaturas;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        $meses = 1;

        switch ($request->periodicidade) {
            case 'anual':
                $meses = 12;
                break;
            case 'trimestral':
                $meses = 3;
                break;
            default:
                $meses = 1;
                break;
        }

        $status = strtolower($request->status);

        if (!in_array($status, ['ativo', 'atrasado', 'pendente'])) {
            $status = 'pendente';
        }

        Assinaturas::create([
            'empresa_id' => $empresa->id,
            'plano' => $request->plano_nome,
            'valor_mensal' => $request->valor,
            'data_inicio' => now(),
            'periodicidade' => $request->periodicidade,
            'data_vencimento' => now()->addMonths($meses),
            'status' => $status,
        ]);

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
        $assinatura = Assinaturas::findOrFail($id);
        $empresas = Empresa::all();

        return view('assinaturas.edit', compact('assinatura', 'empresas'));
    }

    /**
     * 🔁 Atualiza uma assinatura existente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'plano' => 'required|string|max:60',
            'valor_mensal' => 'required|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_inicio',
            'status' => 'required|in:pendente,ativo,atrasado,cancelado',
            'metodo_pagamento' => 'required|in:manual,pix,asaas,pagseguro',
        ]);

        DB::beginTransaction();
        try {
            $assinatura = Assinaturas::findOrFail($id);
            $assinatura->update($request->all());
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

        $diasRestantes = $assinatura->data_vencimento
            ? now()->diffInDays($assinatura->data_vencimento, false)
            : null;

        $progressoCiclo = null;
        if ($assinatura->data_inicio && $assinatura->data_vencimento) {
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
}
