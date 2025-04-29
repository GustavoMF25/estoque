<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Actions\LogoutOtherBrowserSessions;

class LogoutOtherSessions extends Component
{
    public $confirmingLogout = false;
    public $password;

    public function confirmLogout()
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->confirmingLogout = true;
    }

    public function logoutOtherBrowserSessions()
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($this->password, Auth::user()->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => __('The provided password does not match your current password.'),
            ]);
        }

        app(LogoutOtherBrowserSessions::class)(Auth::user(), $this->password);

        $this->confirmingLogout = false;

        session()->flash('success', __('Successfully logged out from other sessions.'));
    }

    public function render()
    {
        return view('livewire.logout-other-sessions');
    }
}
