<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class LojaController extends Controller
{
    private const LIMITE_LOJAS_POR_EMPRESA = 2;

    public function index()
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojasCount = Loja::where('empresa_id', $empresaId)->count();

        return view('lojas.index', [
            'limiteLojas' => self::LIMITE_LOJAS_POR_EMPRESA,
            'lojasCount' => $lojasCount,
        ]);
    }

    public function create()
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojasCount = Loja::where('empresa_id', $empresaId)->count();
        if ($lojasCount >= self::LIMITE_LOJAS_POR_EMPRESA) {
            return redirect()->route('lojas.index')->with('error', 'Limite de lojas atingido (2 por empresa).');
        }

        return view('lojas.create');
    }

    public function store(Request $request)
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojasCount = Loja::where('empresa_id', $empresaId)->count();
        if ($lojasCount >= self::LIMITE_LOJAS_POR_EMPRESA) {
            return redirect()->route('lojas.index')->with('error', 'Limite de lojas atingido (2 por empresa).');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20|unique:lojas,cnpj',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string|max:20',
            'contatos' => 'nullable|array',
            'contatos.*.name' => 'nullable|string|max:80',
            'contatos.*.numero' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $dados = $request->only('nome', 'razao_social', 'cnpj', 'endereco', 'telefone', 'email');
        $dados['contatos'] = collect($request->input('contatos', []))
            ->map(fn($item) => [
                'name' => trim((string) ($item['name'] ?? '')),
                'numero' => trim((string) ($item['numero'] ?? '')),
            ])
            ->filter(fn($item) => $item['name'] !== '' || $item['numero'] !== '')
            ->values()
            ->all();
        $dados['empresa_id'] = $empresaId;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = uniqid('logo_loja_') . '.' . $logo->getClientOriginalExtension();
            $logo->move(storage_path('app/public/logos'), $filename);
            $dados['logo'] = 'logos/' . $filename;
        }

        Loja::create($dados);

        return redirect()->route('lojas.index')->with('success', 'Loja criada com sucesso!');
    }

    public function edit(Loja $loja)
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        abort_if($loja->empresa_id !== $empresaId, 403);

        return view('lojas.edit', compact('loja'));
    }

    public function update(Request $request, Loja $loja)
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        abort_if($loja->empresa_id !== $empresaId, 403);

        $request->validate([
            'nome' => 'required|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('lojas', 'cnpj')->ignore($loja->id),
            ],
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string|max:20',
            'contatos' => 'nullable|array',
            'contatos.*.name' => 'nullable|string|max:80',
            'contatos.*.numero' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $dados = $request->only('nome', 'razao_social', 'cnpj', 'endereco', 'telefone', 'email');
        $dados['contatos'] = collect($request->input('contatos', []))
            ->map(fn($item) => [
                'name' => trim((string) ($item['name'] ?? '')),
                'numero' => trim((string) ($item['numero'] ?? '')),
            ])
            ->filter(fn($item) => $item['name'] !== '' || $item['numero'] !== '')
            ->values()
            ->all();

        if ($request->hasFile('logo')) {
            if ($loja->logo && Storage::disk('public')->exists($loja->logo)) {
                Storage::disk('public')->delete($loja->logo);
            }

            $logo = $request->file('logo');
            $filename = uniqid('logo_loja_') . '.' . $logo->getClientOriginalExtension();
            $logo->move(storage_path('app/public/logos'), $filename);
            $dados['logo'] = 'logos/' . $filename;
        }

        $loja->update($dados);

        return redirect()->route('lojas.index')->with('success', 'Loja atualizada com sucesso!');
    }

    public function destroy(Loja $loja)
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        abort_if($loja->empresa_id !== $empresaId, 403);

        if ($loja->logo && Storage::disk('public')->exists($loja->logo)) {
            Storage::disk('public')->delete($loja->logo);
        }

        $loja->delete();
        return redirect()->route('lojas.index')->with('success', 'Loja removida com sucesso!');
    }
}
