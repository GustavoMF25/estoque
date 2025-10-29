<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Modulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::orderBy('nome')->paginate(15);
        return view('empresa.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresa.create');
    }

    public function store(Request $request)
    {


        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:empresas,cnpj',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except('logo');

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }

            $empresa = Empresa::create($data);

            // 🔗 Vincula automaticamente todos os módulos existentes
            $modulos = Modulo::all();
            foreach ($modulos as $modulo) {
                DB::table('empresa_modulos')->insert([
                    'empresa_id' => $empresa->id,
                    'modulo_id' => $modulo->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('empresas.index')->with('success', 'Empresa cadastrada com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar empresa: ' . $e->getMessage());
        }
    }

    public function edit(Empresa $empresa)
    {
        return view('empresa.edit', ['empresas' => $empresa]);
    }

    public function editEmpresa()
    {
        $empresa = auth()->user()->empresa;
        return view('empresa.edit', ['empresas' => $empresa]);
    }

    public function update(Request $request, Empresa $empresa)
    {

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'endereco' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);


        if ($request->hasFile('logo')) {

            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $logo = $request->file('logo');
            $filename = uniqid('logo_') . '.' . $logo->getClientOriginalExtension();

            $logo->move(storage_path('app/public/logos'), $filename);

            $data['logo'] = 'logos/' . $filename;
        }

        $empresa->update($data);

        return redirect()->back()->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        try {
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $empresa->delete();
            return back()->with('success', 'Empresa excluída com sucesso!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao excluir empresa: ' . $e->getMessage());
        }
    }
}
