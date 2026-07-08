<?php

namespace Database\Factories;

use App\Models\ClassSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSession>
 */
class ClassSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $starts = fake()->dateTimeBetween('-2 months', '+2 months');
        $ends   = (clone $starts)->modify('+2 hours');

        return [
            'module_id'          => null,
            'resource_folder_id' => null,
            'title'              => 'Week ' . fake()->numberBetween(1, 14) . ' — ' . fake()->randomElement(['Lecture', 'Tutorial', 'Lab']),
            'location'           => fake()->randomElement([
                'Room ' . fake()->bothify('##?'),
                'Lab ' . fake()->bothify('#?'),
                'https://zoom.us/j/' . fake()->numerify('##########'),
                'SDL',
            ]),
            'starts_at'          => $starts,
            'ends_at'            => $ends,
            'type'               => fake()->randomElement(['PHYSICAL', 'VIRTUAL']),
        ];
    }

    public function physical(): static
    {
        return $this->state(fn () => [
            'type'     => 'PHYSICAL',
            'location' => 'Room ' . fake()->bothify('##?'),
        ]);
    }

    public function virtual(): static
    {
        return $this->state(fn () => [
            'type'     => 'VIRTUAL',
            'location' => 'https://zoom.us/j/' . fake()->numerify('##########'),
        ]);
    }
}
