<?php

namespace Tests\Feature\Board;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class CreateBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_board()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/boards', [
            'title' => 'Board Uji Coba'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('boards', [
            'title' => 'Board Uji Coba'
        ]);
    }
}
