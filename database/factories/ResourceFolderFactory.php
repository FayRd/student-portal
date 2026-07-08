<?php

namespace Database\Factories;

use App\Models\ResourceFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceFolder>
 */
class ResourceFolderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => null,
            'parent_id' => null,
            'name'      => 'Week ' . fake()->unique()->numberBetween(1, 14) . ' — ' . fake()->catchPhrase(),
            'order'     => 0,
        ];
    }
}
