<?php

namespace Tests\Feature\Board;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Board;

class InviteCollaboratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_invite_collaborator_to_board()
    {
        $user = User::factory()->create();
        $collaborator = User::factory()->create();
        $board = Board::factory()->create();

        $response = $this->actingAs($user)->post("/boards/{$board->id}/invite", [
            'email' => $collaborator->email,
        ]);

        $response->assertStatus(302);
        // Tes lebih detail bisa assert DB kolaborasi
    }
}
