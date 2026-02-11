<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $r)
    {
        $clientes = Cliente::with('enderecoPadrao')
            ->when($r->filled('busca'), function ($q) use ($r) {
                $s = $r->string('busca');
                $q->where('nome', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('documento', 'like', "%{$s}%");
            })
            ->orderBy('nome')
            ->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $r)
    {
        $dados = $r->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes,email',
            'telefone' => 'nullable|string|max:30',
            'documento' => 'nullable|string|max:30',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string',

            'endereco.cep' => 'nullable|string|max:15',
            'endereco.rua' => 'nullable|string|max:255',
            'endereco.numero' => 'nullable|string|max:50',
            'endereco.complemento' => 'nullable|string|max:255',
            'endereco.bairro' => 'nullable|string|max:255',
            'endereco.cidade' => 'nullable|string|max:255',
            'endereco.estado' => 'nullable|string|max:5',
        ]);

        DB::transaction(function () use ($dados) {
            $endereco = $dados['endereco'] ?? null;
            unset($dados['endereco']);
            $cliente = Cliente::create($dados);
            // dd($cliente);
            if ($endereco && array_filter($endereco)) {
                $cliente->enderecos()->create(array_merge($endereco, [
                    'rotulo' => 'Principal',
                    'padrao' => true,
                ]));
            }
        });

        return redirect()->route('clientes.index')->with('success', 'Cliente criado!');
    }

    public function edit(Clientes $cliente)
    {
        $cliente->load('enderecoPadrao');
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $r, Clientes $cliente)
    {
        $dados = $r->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes,email,' . $cliente->id,
            'telefone' => 'nullable|string|max:30',
            'documento' => 'nullable|string|max:30',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string',

            'endereco.cep' => 'nullable|string|max:15',
            'endereco.rua' => 'nullable|string|max:255',
            'endereco.numero' => 'nullable|string|max:50',
            'endereco.complemento' => 'nullable|string|max:255',
            'endereco.bairro' => 'nullable|string|max:255',
            'endereco.cidade' => 'nullable|string|max:255',
            'endereco.estado' => 'nullable|string|max:5',
        ]);

        DB::transaction(function () use ($cliente, $dados) {
            $endereco = $dados['endereco'] ?? null;
            unset($dados['endereco']);
            $cliente->update($dados);
            if ($endereco && array_filter($endereco)) {
                $cliente->enderecos()->updateOrCreate(['padrao' => true], $endereco);
            }
        });

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado!');
    }

    public function destroy(Clientes $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente removido!');
    }
}
