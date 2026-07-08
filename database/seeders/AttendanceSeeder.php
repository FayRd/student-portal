<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only mark attendance for past class sessions
        ClassSession::whereDate('ends_at', '<', now())->each(function ($session) {
            $lecturer    = Module::find($session->module_id)?->editors()->first();
            $enrollments = Enrollment::where('module_id', $session->module_id)->get();

            $enrollments->each(function ($enrollment) use ($session, $lecturer) {
                Attendance::factory()->create([
                    'class_id'  => $session->id,
                    'user_id'   => $enrollment->user_id,
                    'marked_by' => $lecturer?->id,
                    'marked_at' => $session->ends_at,
                ]);
            });
        });
    }
}
