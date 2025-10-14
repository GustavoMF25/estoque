<?php

namespace App\Livewire\Clientes;

use App\Models\EnderecoCliente;
use Livewire\Component;

class EnderecoEditar extends Component
{
    public $enderecoId;

    public $rotulo;
    public $cep;
    public $rua;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $estado;
    public $padrao = true;

    protected $rules = [
        'rotulo'      => 'nullable|string|max:255',
        'cep'         => 'nullable|string|max:15',
        'rua'         => 'nullable|string|max:255',
        'numero'      => 'nullable|string|max:50',
        'complemento' => 'nullable|string|max:255',
        'bairro'      => 'nullable|string|max:255',
        'cidade'      => 'nullable|string|max:255',
        'estado'      => 'nullable|string|max:5',
        'padrao'      => 'boolean',
    ];

    public function mount($id)
    {
        $endereco = EnderecoCliente::findOrFail($id);
        $this->enderecoId = $endereco->id;

        $this->rotulo      = $endereco->rotulo;
        $this->cep         = $endereco->cep;
        $this->rua         = $endereco->rua;
        $this->numero      = $endereco->numero;
        $this->complemento = $endereco->complemento;
        $this->bairro      = $endereco->bairro;
        $this->cidade      = $endereco->cidade;
        $this->estado      = $endereco->estado;
        $this->padrao = (bool) $endereco->padrao;
    }

    public function salvar()
    {
        $this->validate();

        $endereco = EnderecoCliente::findOrFail($this->enderecoId);
        $endereco->update([
            'rotulo'      => $this->rotulo,
            'cep'         => $this->cep,
            'rua'         => $this->rua,
            'numero'      => $this->numero,
            'complemento' => $this->complemento,
            'bairro'      => $this->bairro,
            'cidade'      => $this->cidade,
            'estado'      => $this->estado,
            'padrao'      => $this->padrao,
        ]);

        session()->flash('success', 'Endereço atualizado com sucesso!');
        return redirect()->route('clientes.index');
    }

    public function render()
    {
        return view('livewire.clientes.endereco-editar');
    }
}
