<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class Toast extends Component
{
    public ?string $message = null;
    public string $type = 'info'; 
    #[On('toast')]
    public function toast($dados)
    {
        $this->type = $dados['type'];
        $this->message = $dados['message'];
    }
    public function dismiss()
    {
        $this->reset('message', 'type');
    }

    public function render()
    {
        return view('livewire.components.toast');
    }
}
