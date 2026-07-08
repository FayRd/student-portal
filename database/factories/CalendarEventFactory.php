<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $starts = fake()->dateTimeBetween('-1 month', '+3 months');
        $ends   = (clone $starts)->modify('+' . fake()->numberBetween(1, 14) . ' days');

        return [
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'type'        => fake()->randomElement(['HOLIDAY', 'EXAM_PERIOD', 'SEMESTER_START', 'SEMESTER_END', 'OTHER']),
            'starts_at'   => $starts,
            'ends_at'     => $ends,
            'created_by'  => null,
        ];
    }
}
