<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Empresa::first();
        return view('empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $empresa = Empresa::first();

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

        return redirect()->route('empresa.edit')->with('success', 'Dados da empresa atualizados com sucesso!');
    }
}
