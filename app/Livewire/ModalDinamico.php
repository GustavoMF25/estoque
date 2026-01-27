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
    public $size = '';
    public $aberto = false;

    protected $listeners = ['abrirModal', 'fecharModal'];

    // protected $listeners = ['abrirModal', 'fecharModal'];

    public function abrirModal($titulo, $componente, $props = [], $formId = null)
    {
        $this->titulo = $titulo;
        $this->formId = $formId;
        $this->size = $props['size'] ?? 'modal-sm';
        $props['formId'] = $formId;
        $props['size'] = $this->size;
        $this->conteudo = Livewire::mount($componente, $props);
        $this->aberto = true;
        $this->dispatch('refreshTabelaMovimentacoes');
        $this->dispatch('initSelect2');
    }

    public function fecharModal()
    {
        $this->aberto = false;
        $this->titulo = '';
        $this->conteudo = '';
        $this->componente = '';
        $this->props = '';
        $this->formId = '';
    }

    public function render()
    {
        return view('livewire.modal-dinamico');
    }
}
