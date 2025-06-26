<?php

namespace Tests\Feature\List;

use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateListTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_list_in_board(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('lists.store', $board->id), [
            'name' => 'My First List',
        ]);

        $response->assertRedirect(); // biasanya redirect ke board
        $this->assertDatabaseHas('lists', [
            'name' => 'My First List',
            'board_id' => $board->id,
        ]);
    }
}
