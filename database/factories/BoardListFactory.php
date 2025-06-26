<?php

namespace Database\Factories;

use App\Models\BoardList; 
use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardListFactory extends Factory
{
    protected $model = BoardList::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'position' => $this->faker->numberBetween(0, 10),
            'board_id' => Board::factory(),
        ];
    }
}
