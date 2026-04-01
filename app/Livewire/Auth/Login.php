<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Login extends Component
{
    #[Rule('required|email')]
    public $email = '';
    #[Rule('required')]
    public $password = '';
    public $remember = false;

    /**
     * Determine the throttle key for the request.
     */
    public function throttleKey()
    {
        return strtolower($this->email) . '|' . request()->ip();
    }

    /**
     * Handle the authentication attempt.
     */
    public function login()
    {
        $this->validate();
        
        // Check for too many failed attempts (Rate Limiting)
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey());
            
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Increment failed attempts
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Clear throttle on success
        RateLimiter::clear($this->throttleKey());

        // Regenerate session to prevent session fixation attacks
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
