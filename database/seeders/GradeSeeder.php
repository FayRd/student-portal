<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Module;
use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Submission::where('status', 'ONTIME')
            ->whereHas('assignment', fn ($q) => $q->whereNotNull('max_score'))
            ->get()
            ->each(function ($submission) {
                $lecturer = Module::find($submission->assignment->module_id)
                    ?->editors()->first();

                // Grade 70% of eligible submissions
                if (rand(1, 10) <= 7) {
                    Grade::factory()->create([
                        'submission_id' => $submission->id,
                        'graded_by'     => $lecturer?->id,
                        'score'         => fake()->randomFloat(
                            2,
                            0,
                            $submission->assignment->max_score
                        ),
                    ]);
                }
            });
    }
}
