<?php

namespace App\Livewire\Usuario;

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
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|min:8|confirmed',
            'profile_photo' => 'nullable|image|max:2048',
            'perfil' => 'required|in:admin,operador,gerente,vendedor',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->perfil = $this->perfil;

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
