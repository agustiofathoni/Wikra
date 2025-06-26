<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\BoardList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'list_id' => BoardList::factory(),
            'position' => $this->faker->numberBetween(0, 10),
        ];
    }
}
