<?php

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'submission_id' => null,
            'graded_by'     => null,
            'score'         => fake()->randomFloat(2, 20, 100),
            'feedback'      => fake()->paragraph(2),
            'graded_at'     => fake()->dateTimeBetween('-2 weeks', 'now'),
        ];
    }

    public function feedbackOnly(): static
    {
        return $this->state(fn () => [
            'score' => null,
        ]);
    }
}
