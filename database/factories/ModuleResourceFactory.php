<?php

namespace Database\Factories;

use App\Models\ModuleResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModuleResource>
 */
class ModuleResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ext     = fake()->randomElement(['pdf', 'pptx', 'docx', 'txt']);
        $mimeMap = [
            'pdf'  => 'application/pdf',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt'  => 'text/plain',
        ];
        $fileName = fake()->slug(3) . '.' . $ext;

        return [
            'module_id'        => null,
            'folder_id'        => null,
            'uploaded_by'      => null,
            'title'            => fake()->sentence(4),
            'file_path'        => 'resources/' . fake()->uuid() . '/' . $fileName,
            'file_name'        => $fileName,
            'file_size'        => fake()->numberBetween(50000, 5000000),
            'mime_type'        => $mimeMap[$ext],
            'last_modified_by' => null,
            'last_modified_at' => null,
        ];
    }
}
