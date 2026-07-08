<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extensions = ['pdf', 'docx', 'pptx', 'txt', 'zip'];
        $mimeMap    = [
            'pdf'  => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt'  => 'text/plain',
            'zip'  => 'application/zip',
        ];
        $ext          = fake()->randomElement($extensions);
        $fileName     = fake()->slug(3) . '.' . $ext;
        $submittedAt  = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'assignment_id' => null,
            'user_id'       => null,
            'file_path'     => 'submissions/' . fake()->uuid() . '/' . $fileName,
            'file_name'     => $fileName,
            'file_size'     => fake()->numberBetween(50000, 10000000),
            'mime_type'     => $mimeMap[$ext],
            'status'        => 'ONTIME',
            'submitted_at'  => $submittedAt,
            'processed_at'  => (clone $submittedAt)->modify('+2 minutes'),
        ];
    }

    public function late(): static
    {
        return $this->state(fn () => ['status' => 'LATE']);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'       => 'PENDING',
            'processed_at' => null,
        ]);
    }

    public function error(): static
    {
        return $this->state(fn () => [
            'status'       => 'ERROR',
            'processed_at' => null,
        ]);
    }
}
