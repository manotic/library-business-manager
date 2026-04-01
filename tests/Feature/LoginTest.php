<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Livewire\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_view_login_page()
    {
        $this->get('/login')->assertStatus(200);
    }

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'secret-123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', $password)
            ->call('login')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    #[Test]
    public function login_is_throttled_after_five_failed_attempts()
    {
        $user = User::factory()->create();
        $component = Livewire::test(Login::class)->set('email', $user->email);

        // Simulate 5 failed hits
        foreach (range(1, 5) as $i) {
            $component->set('password', 'wrong-val-'.$i)->call('login');
        }

        // The 6th attempt should trigger the ValidationException from RateLimiter
        $component->set('password', 'any-password')
            ->call('login')
            ->assertHasErrors(['email']);
            
        $this->assertTrue(RateLimiter::tooManyAttempts(strtolower($user->email).'|127.0.0.1', 5));
    }
}