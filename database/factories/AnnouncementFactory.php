<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id'    => null,
            'created_by'   => null,
            'title'        => fake()->sentence(6),
            'body'         => fake()->paragraphs(2, true),
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'expires_at'   => null,
        ];
    }

    public function global(): static
    {
        return $this->state(fn () => ['module_id' => null]);
    }

    public function expiring(): static
    {
        return $this->state(fn () => [
            'expires_at' => fake()->dateTimeBetween('now', '+2 weeks'),
        ]);
    }
}
