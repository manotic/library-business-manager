<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Register extends Component
{
    /**
     * Using Livewire 3 Attributes for clean validation
     */
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|string|email|max:255|unique:users')]
    public $email = '';

    // Add these at the end [regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/]
    #[Rule('required|string|min:8')]
    public $password = '';

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Standard Laravel event to trigger verification emails if enabled
        event(new Registered($user));

        // Authenticate the user immediately
        Auth::login($user);

        // Flash success message and redirect to the dashboard
        session()->flash('status', 'Account created successfully. Welcome to your vault!');

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
