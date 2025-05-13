<?php

namespace App\Livewire;

use Livewire\Component;


class ModalDinamico extends Component
{
    public $titulo = '';
    public $conteudo = '';
    public $aberto = false;

    // protected $listeners = ['abrirModal'];

    // protected $listeners = ['abrirModal', 'fecharModal'];

    public function abrirModal($titulo, $conteudo)
    {
        $this->titulo = $titulo;
        $this->conteudo = $conteudo;
        $this->aberto = true;

        $this->dispatchBrowserEvent('abrir-modal-bootstrap');
    }

    public function fecharModal()
    {
        $this->aberto = false;
        $this->dispatchBrowserEvent('fechar-bootstrap-modal');
    }

    public function render()
    {
        return view('livewire.modal-dinamico');
    }
}
