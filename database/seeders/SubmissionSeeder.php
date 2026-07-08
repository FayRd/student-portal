<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed submissions for past assignments
        Assignment::whereDate('due_at', '<', now())->each(function ($assignment) {
            $enrollments = Enrollment::where('module_id', $assignment->module_id)->get();

            $enrollments->each(function ($enrollment) use ($assignment) {
                // 80% of students submit on time, 10% late, 10% don't submit
                $rand = rand(1, 10);

                if ($rand <= 8) {
                    Submission::factory()->create([
                        'assignment_id' => $assignment->id,
                        'user_id'       => $enrollment->user_id,
                        'submitted_at'  => fake()->dateTimeBetween(
                            $assignment->available_at,
                            $assignment->due_at
                        ),
                    ]);
                } elseif ($rand === 9) {
                    Submission::factory()->late()->create([
                        'assignment_id' => $assignment->id,
                        'user_id'       => $enrollment->user_id,
                        'submitted_at'  => fake()->dateTimeBetween(
                            $assignment->due_at,
                            '+1 week'
                        ),
                    ]);
                }
                // $rand === 10: no submission
            });
        });
    }
}
