<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_displayed(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Acessar conta')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('action="'.route('login').'"', false)
            ->assertSee('href="'.route('register').'"', false);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('links.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'user@example.com',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_links_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('links.index'))
            ->assertOk()
            ->assertSee('Lista de links');
    }

    public function test_guest_cannot_access_links_list(): void
    {
        $this->get(route('links.index'))
            ->assertRedirect(route('login'));
    }
}
