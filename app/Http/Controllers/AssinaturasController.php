<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Assinaturas;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssinaturasController extends Controller
{
    /**
     * 🧭 Lista todas as assinaturas
     */
    public function index()
    {
        $assinaturas = Assinaturas::with('empresa')
            ->orderByDesc('data_vencimento')
            ->paginate(15);

        return view('assinaturas.index', compact('assinaturas'));
    }

    /**
     * ➕ Exibe o formulário de criação
     */
    public function create()
    {
        $empresas = Empresa::all();
        return view('assinaturas.create', compact('empresas'));
    }

    /**
     * 💾 Cadastra nova assinatura
     */
    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'plano' => 'required|string|max:60',
            'valor_mensal' => 'required|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_inicio',
            'status' => 'required|in:pendente,ativo,atrasado,cancelado',
            'metodo_pagamento' => 'required|in:manual,pix,asaas,pagseguro',
        ]);

        DB::beginTransaction();
        try {
            Assinaturas::create($request->all());
            DB::commit();

            return redirect()
                ->route('assinaturas.index')
                ->with('success', 'Assinatura criada com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao salvar assinatura: ' . $e->getMessage());
        }
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
}
