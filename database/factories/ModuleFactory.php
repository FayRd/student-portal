<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    public function definition(): array
    {
        $prefixes = ['CS', 'IT', 'SE', 'DS', 'CY'];
        $code     = fake()->randomElement($prefixes)
                  . fake()->unique()->numberBetween(100, 499);

        return [
            'code'          => $code,
            'name'          => fake()->bs(),
            'description'   => fake()->paragraph(3),
            'credits'       => fake()->randomElement([3, 4]),
            'academic_year' => '2025/2026',
            'semester'      => fake()->randomElement([1, 2]),
            'status'        => 'ACTIVE',
            'created_by'    => null,
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => ['status' => 'UPCOMING']);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => 'ARCHIVED']);
    }
}
