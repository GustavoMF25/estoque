<?php

namespace App\Livewire;

use Livewire\Component;


class ModalDinamico extends Component
{
    public $titulo = '';
    public $conteudo = '';
    public $formId = '';
    public $aberto = false;

    // protected $listeners = ['abrirModal'];

    // protected $listeners = ['abrirModal', 'fecharModal'];

    public function abrirModal($titulo, $conteudo, $formId = '')
    {
        $this->titulo = $titulo;
        $this->conteudo = $conteudo;
        $this->formId = $formId;
        $this->aberto = true;

        $this->dispatchBrowserEvent('abrir-modal-bootstrap');
    }

    public function render()
    {
        return view('livewire.modal-dinamico');
    }
}
