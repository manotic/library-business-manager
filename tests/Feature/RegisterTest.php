<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Livewire\Auth\Register;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_view_register_page()
    {
        $this->get('/register')->assertStatus(200);
    }

    #[Test]
    public function new_user_can_register()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('register')
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertAuthenticated();
    }

    #[Test]
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'exists@example.com']);

        Livewire::test(Register::class)
            ->set('name', 'New User')
            ->set('email', 'exists@example.com')
            ->set('password', 'password123')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    #[Test]
    public function password_must_be_minimum_length()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }
}