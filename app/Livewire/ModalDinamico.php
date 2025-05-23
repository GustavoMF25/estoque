<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Livewire;

class ModalDinamico extends Component
{
    public $titulo = '';
    public $conteudo = '';
    public $componente = '';
    public $props = '';
    public $formId = '';
    public $aberto = false;

    protected $listeners = ['abrirModal'];

    // protected $listeners = ['abrirModal', 'fecharModal'];

    public function abrirModal($titulo, $componente, $props = [], $formId = null)
    {
        $this->titulo = $titulo;
        $this->formId = $formId;
        $props['formId'] = $formId;
        $this->conteudo = Livewire::mount($componente, $props);
        $this->dispatch('refreshTabelaMovimentacoes');
        
    }

    public function render()
    {
        return view('livewire.modal-dinamico');
    }
}
