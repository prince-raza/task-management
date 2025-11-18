<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected static int $orderCounter = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->sentence(),
            'order' => self::$orderCounter++,
            'date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'status' => fake()->randomElement(TaskStatus::getValues()),
            'priority' => fake()->randomElement(TaskPriority::getValues()),
        ];
    }
}
