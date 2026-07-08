<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Triple 'PRESENT' because increase chance
        return [
            'class_id'  => null,
            'user_id'   => null,
            'status'    => fake()->randomElement(['PRESENT', 'PRESENT', 'PRESENT', 'ABSENT', 'LATE', 'EXCUSED']),
            'marked_by' => null,
            'marked_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'note'      => null,
        ];
    }

    public function present(): static
    {
        return $this->state(fn () => ['status' => 'PRESENT', 'note' => null]);
    }

    public function absent(): static
    {
        return $this->state(fn () => [
            'status' => 'ABSENT',
            'note'   => fake()->sentence(),
        ]);
    }
}
