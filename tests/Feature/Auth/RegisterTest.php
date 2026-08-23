<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->post(route('register'), [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Conta criada com sucesso!');

        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    public function test_success_message_is_displayed_on_login_page(): void
    {
        $this->withSession(['success' => 'Conta criada com sucesso!'])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Conta criada com sucesso!');
    }

    public function test_register_error_message_is_displayed(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $this->from(route('register'))
            ->post(route('register'), [
                'first_name' => 'João',
                'last_name' => 'Silva',
                'email' => 'joao@example.com',
                'password' => 'password123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
    }
}
