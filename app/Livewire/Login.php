<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;


class Login extends Component
{
    public $email;
    public $password;
    public $showPassword = false;
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $credentials = [
            Fortify::username() => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();
            
            if($user->active_status == 1) {
                if ($user->user_role === 'homeowner') {
                    return app(LoginResponse::class)->toResponse($this->request)->intended('/home');
                } else {
                    return app(LoginResponse::class)->toResponse($this->request)->intended('/dashboard');
                }
            }
            elseif($user->active_status == 2){// Deactivated user
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('login', 'Your account has been deactivated. Please contact the administrator.');
            }else{
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('login', 'Your account is pending approval. Please wait for admin verification.');
            }
        } else {
            $this->addError('login', 'Invalid credentials.');
        }
    }

    public function render()
    {
        return view('auth.login');
    }

    public function togglePasswordVisibility()
    {
        $this->addError('login', 'Invalid shit.');
        $this->showPassword = !$this->showPassword;
    }
}

