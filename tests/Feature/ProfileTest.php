<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_their_profile(): void
    {
        $user = User::factory()->create([
            'bio' => 'Minha bio.',
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Perfil')
            ->assertSee($user->name)
            ->assertSee('Minha bio.');
    }

    public function test_user_can_update_profile_without_replacing_image(): void
    {
        $user = User::factory()->create([
            'bio' => 'Bio anterior.',
            'image_url' => 'profiles/avatar-anterior.png',
        ]);

        $this->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post(route('profile.update'), [
                '_method' => 'PATCH',
                'name' => 'Maria Silva',
                'email' => 'maria@example.com',
                'bio' => 'Nova bio.',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Perfil atualizado com sucesso.')
            ->assertJsonPath('profile.name', 'Maria Silva')
            ->assertJsonPath('profile.image_url', 'http://localhost/storage/profiles/avatar-anterior.png');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'bio' => 'Nova bio.',
            'image_url' => 'profiles/avatar-anterior.png',
        ]);
    }

    public function test_user_can_replace_profile_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profiles/avatar-anterior.png', 'imagem anterior');
        $user = User::factory()->create([
            'bio' => 'Minha bio.',
            'image_url' => 'profiles/avatar-anterior.png',
        ]);

        $this->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post(route('profile.update'), [
                '_method' => 'PATCH',
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
                'image' => UploadedFile::fake()->image('avatar.png', 100, 100),
            ])
            ->assertOk();

        $user->refresh();

        $this->assertNotSame('profiles/avatar-anterior.png', $user->image_url);
        Storage::disk('public')->assertMissing('profiles/avatar-anterior.png');
        Storage::disk('public')->assertExists($user->image_url);
    }

    public function test_profile_requires_name_email_and_bio(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('profile.update'), ['_method' => 'PATCH'])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors(['name', 'email', 'bio']);
    }

    public function test_profile_rejects_an_email_used_by_another_user(): void
    {
        $user = User::factory()->create(['bio' => 'Minha bio.']);
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('profile.update'), [
                '_method' => 'PATCH',
                'name' => $user->name,
                'email' => $otherUser->email,
                'bio' => $user->bio,
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('email');
    }

    public function test_guest_cannot_access_or_update_a_profile(): void
    {
        $this->get(route('profile.edit'))
            ->assertRedirect(route('login'));

        $this->post(route('profile.update'), ['_method' => 'PATCH'])
            ->assertRedirect(route('login'));
    }
}
