<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

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
