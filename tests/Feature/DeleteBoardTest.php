<?php

namespace Tests\Feature\Board;

use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_delete_board(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('boards.destroy', $board->id));

        $response->assertRedirect(); // biasanya redirect ke dashboard
        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }
}
