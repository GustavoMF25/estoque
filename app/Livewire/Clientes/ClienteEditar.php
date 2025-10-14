<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class ClienteEditar extends Component
{
    public $clienteId;

    public $nome;
    public $email;
    public $telefone;
    public $documento;
    public $ativo = true;
    public $observacoes;

    protected $rules = [
        'nome'        => 'required|string|max:255',
        'email'       => 'nullable|email|max:255',
        'telefone'    => 'nullable|string|max:30',
        'documento'   => 'nullable|string|max:30',
        'ativo'       => 'boolean',
        'observacoes' => 'nullable|string',
    ];

    public function mount($id)
    {
        $cliente = Cliente::findOrFail($id);
        $this->clienteId = $cliente->id;
        $this->nome = $cliente->nome;
        $this->email = $cliente->email;
        $this->telefone = $cliente->telefone;
        $this->documento = $cliente->documento;
        $this->ativo = $cliente->ativo;
        $this->observacoes = $cliente->observacoes;
    }

    public function salvar()
    {
        $this->validate();

        $cliente = Cliente::findOrFail($this->clienteId);
        $cliente->update([
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'documento' => $this->documento,
            'ativo' => $this->ativo,
            'observacoes' => $this->observacoes,
        ]);

        session()->flash('success', 'Cliente atualizado com sucesso!');
        return redirect()->route('clientes.index');
    }

    public function render()
    {
        return view('livewire.clientes.cliente-editar');
    }
}
