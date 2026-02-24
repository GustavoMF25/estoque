<?php

namespace App\Livewire\Usuario;

use App\Models\Loja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class AtualizarUsuario extends Component
{

    use WithFileUploads;

    public $userId;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $profile_photo;
    public $perfil;
    public $loja_id;
    public $lojas = [];
    public $formId;

    public $user;

    public function mount($userId, $formId)
    {
        $this->userId = $userId;
        $this->formId = $formId;
        $this->user = User::findOrFail($userId);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->perfil = $this->user->perfil;
        $this->loja_id = $this->user->loja_id;
        $empresaId = auth()->user()->empresa_id ?? 1;
        $this->lojas = Loja::where('empresa_id', $empresaId)->orderBy('nome')->get();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|min:8|confirmed',
            'profile_photo' => 'nullable|image|max:2048',
            'perfil' => 'required|in:admin,operador,gerente,vendedor',
            'loja_id' => 'nullable|exists:lojas,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->perfil = $this->perfil;

        if (!empty($this->loja_id)) {
            $empresaId = auth()->user()->empresa_id ?? 1;
            $lojaValida = Loja::where('empresa_id', $empresaId)
                ->where('id', $this->loja_id)
                ->exists();

            if (!$lojaValida) {
                $this->addError('loja_id', 'A loja selecionada não pertence à sua empresa.');
                return;
            }
        }

        $this->user->loja_id = $this->loja_id ?: null;

        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }

        if ($this->profile_photo) {
            $this->user->profile_photo_path = $this->profile_photo->store('profile-photos', 'public');
        }

        $this->user->save();

        $this->dispatch('toastr:success', [
            'success' => 'Usuário atualizado com sucesso!'
        ]);
        $this->dispatch('refreshTabelaUsuarios');
    }


    public function render()
    {
        return view('livewire.usuario.atualizar-usuario');
    }
}
