<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_link_with_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('links.store'), [
                'title' => 'Vídeo de exemplo',
                'platform' => 'YouTube',
                'url' => 'https://www.youtube.com/watch?v=example',
                'image' => UploadedFile::fake()->image('thumbnail.png', 100, 100),
            ])
            ->assertRedirect(route('links.index'))
            ->assertSessionHas('message', 'Link adicionado com sucesso.');

        $link = $user->links()->sole();

        $this->assertSame('Vídeo de exemplo', $link->title);
        $this->assertSame('YouTube', $link->category);
        $this->assertSame('blue', $link->category_variant);
        $this->assertSame(1, $link->position);
        Storage::disk('public')->assertExists($link->getRawOriginal('image_url'));
    }

    public function test_creating_a_link_requires_all_fields(): void
    {
        $user = User::factory()->create();

        foreach (['title', 'platform', 'url', 'image'] as $field) {
            $data = [
                'title' => 'Vídeo de exemplo',
                'platform' => 'YouTube',
                'url' => 'https://example.com',
                'image' => UploadedFile::fake()->image('thumbnail.png'),
            ];
            unset($data[$field]);

            $this->actingAs($user)
                ->from(route('links.index'))
                ->post(route('links.store'), $data)
                ->assertRedirect(route('links.index'))
                ->assertSessionHasErrors($field);
        }

        $this->assertDatabaseCount('links', 0);
    }

    public function test_creating_a_link_rejects_invalid_url_and_image(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('links.index'))
            ->post(route('links.store'), [
                'title' => 'Vídeo de exemplo',
                'platform' => 'YouTube',
                'url' => 'ftp://example.com',
                'image' => UploadedFile::fake()->create('thumbnail.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('links.index'))
            ->assertSessionHasErrors(['url', 'image']);
    }

    public function test_creating_a_link_rejects_an_image_larger_than_two_megabytes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('links.index'))
            ->post(route('links.store'), [
                'title' => 'Vídeo de exemplo',
                'platform' => 'YouTube',
                'url' => 'https://example.com',
                'image' => UploadedFile::fake()->image('thumbnail.png')->size(2049),
            ])
            ->assertRedirect(route('links.index'))
            ->assertSessionHasErrors('image');
    }

    public function test_guest_cannot_create_a_link(): void
    {
        $this->post(route('links.store'), [])
            ->assertRedirect(route('login'));
    }

    public function test_user_sees_only_own_links_in_persisted_order(): void
    {
        $user = User::factory()->create();
        $firstLink = Link::factory()->for($user)->create([
            'title' => 'Primeiro link',
            'url' => 'https://example.com/primeiro',
            'image_url' => 'https://example.com/primeiro.png',
            'category' => 'Vídeos',
            'position' => 1,
        ]);
        $secondLink = Link::factory()->for($user)->create([
            'title' => 'Segundo link',
            'url' => 'https://example.com/segundo',
            'position' => 2,
        ]);
        $otherLink = Link::factory()->create(['title' => 'Link de outro usuário']);

        $this->actingAs($user)
            ->get(route('links.index'))
            ->assertOk()
            ->assertSeeInOrder([$firstLink->title, $secondLink->title])
            ->assertSee('example.com/primeiro')
            ->assertSee('Vídeos')
            ->assertSee('src="https://example.com/primeiro.png"', false)
            ->assertDontSee($otherLink->title);
    }

    public function test_user_can_move_a_link_up_and_down(): void
    {
        $user = User::factory()->create();
        $firstLink = Link::factory()->for($user)->create(['position' => 1]);
        $secondLink = Link::factory()->for($user)->create(['position' => 2]);

        $this->actingAs($user)
            ->patchJson(route('links.position.update', $secondLink), ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('moved', true)
            ->assertJsonCount(2, 'links');

        $this->assertDatabaseHas('links', ['id' => $firstLink->id, 'position' => 2]);
        $this->assertDatabaseHas('links', ['id' => $secondLink->id, 'position' => 1]);

        $this->patchJson(route('links.position.update', $secondLink), ['direction' => 'down'])
            ->assertOk()
            ->assertJsonPath('moved', true);

        $this->assertDatabaseHas('links', ['id' => $firstLink->id, 'position' => 1]);
        $this->assertDatabaseHas('links', ['id' => $secondLink->id, 'position' => 2]);
    }

    public function test_moving_first_or_last_link_does_not_change_positions(): void
    {
        $user = User::factory()->create();
        $firstLink = Link::factory()->for($user)->create(['position' => 1]);
        $lastLink = Link::factory()->for($user)->create(['position' => 2]);

        $this->actingAs($user)
            ->patchJson(route('links.position.update', $firstLink), ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('moved', false);

        $this->patchJson(route('links.position.update', $lastLink), ['direction' => 'down'])
            ->assertOk()
            ->assertJsonPath('moved', false);

        $this->assertDatabaseHas('links', ['id' => $firstLink->id, 'position' => 1]);
        $this->assertDatabaseHas('links', ['id' => $lastLink->id, 'position' => 2]);
    }

    public function test_invalid_direction_is_rejected(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this->actingAs($user)
            ->from(route('links.index'))
            ->patch(route('links.position.update', $link), ['direction' => 'sideways'])
            ->assertRedirect(route('links.index'))
            ->assertSessionHasErrors('direction');
    }

    public function test_guest_cannot_reorder_links(): void
    {
        $link = Link::factory()->create();

        $this->patch(route('links.position.update', $link), ['direction' => 'up'])
            ->assertRedirect(route('login'));
    }

    public function test_user_cannot_reorder_another_users_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create();

        $this->actingAs($user)
            ->patchJson(route('links.position.update', $link), ['direction' => 'up'])
            ->assertForbidden();
    }

    public function test_reordering_one_users_links_does_not_affect_another_users_positions(): void
    {
        $user = User::factory()->create();
        $firstLink = Link::factory()->for($user)->create(['position' => 1]);
        $secondLink = Link::factory()->for($user)->create(['position' => 2]);
        $otherUser = User::factory()->create();
        $otherUserLink = Link::factory()->for($otherUser)->create(['position' => 1]);

        $this->actingAs($user)
            ->patchJson(route('links.position.update', $secondLink), ['direction' => 'up'])
            ->assertOk();

        $this->assertDatabaseHas('links', ['id' => $firstLink->id, 'position' => 2]);
        $this->assertDatabaseHas('links', ['id' => $secondLink->id, 'position' => 1]);
        $this->assertDatabaseHas('links', ['id' => $otherUserLink->id, 'position' => 1]);
    }

    public function test_empty_list_is_displayed_when_user_has_no_links(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('links.index'))
            ->assertOk()
            ->assertSee('Você ainda não adicionou nenhum link.');
    }
}
