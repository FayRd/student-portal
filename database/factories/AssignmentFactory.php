<?php

namespace Database\Factories;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $available = fake()->dateTimeBetween('-2 months', '-1 week');
        $due       = fake()->dateTimeBetween('now', '+6 weeks');

        return [
            'module_id'   => null,
            'created_by'  => null,
            'title'       => 'Assignment ' . fake()->numberBetween(1, 5) . ': ' . fake()->catchPhrase(),
            'description' => fake()->paragraphs(2, true),
            'type'        => 'INDIVIDUAL',
            'max_score'   => fake()->randomElement([null, 50, 100]),
            'due_at'      => $due,
            'available_at'=> $available,
        ];
    }

    public function past(): static
    {
        return $this->state(function () {
            $available = fake()->dateTimeBetween('-3 months', '-2 months');
            $due       = fake()->dateTimeBetween('-6 weeks', '-1 week');
            return [
                'available_at' => $available,
                'due_at'       => $due,
            ];
        });
    }
}
