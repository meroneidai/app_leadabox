<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());

        return [
            'project_id' => Project::factory(),
            'assigned_to' => null,
            'title' => rtrim(fake()->sentence(4), '.'),
            'description' => fake()->boolean(60) ? fake()->paragraph() : null,
            'status' => $status,
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'due_date' => fake()->boolean(60) ? fake()->dateTimeBetween('-1 week', '+1 month') : null,
            'completed_at' => $status === TaskStatus::Done ? now() : null,
        ];
    }

    public function done(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Done,
            'completed_at' => now(),
        ]);
    }
}
