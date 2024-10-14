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

            if ($user->user_role === 'homeowner') {
                return app(LoginResponse::class)->toResponse($this->request)->intended('/home');
            } else {
                return app(LoginResponse::class)->toResponse($this->request)->intended('/dashboard');
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
        $this->showPassword = !$this->showPassword;
    }
}

