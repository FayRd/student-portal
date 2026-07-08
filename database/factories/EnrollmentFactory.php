<?php

namespace Database\Factories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'     => null,
            'module_id'   => null,
            'status'      => 'ACTIVE',
            'enrolled_at' => fake()->dateTimeBetween('-3 months', '-1 month'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'COMPLETED']);
    }

    public function dropped(): static
    {
        return $this->state(fn () => ['status' => 'DROPPED']);
    }
}
