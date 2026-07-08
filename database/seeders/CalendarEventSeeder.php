<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@portal.test')->first();

        $events = [
            ['title' => 'Semester 1 Start',     'type' => 'SEMESTER_START', 'starts_at' => '2025-09-01', 'ends_at' => '2025-09-01'],
            ['title' => 'Mid-Semester Break',   'type' => 'HOLIDAY',        'starts_at' => '2025-11-03', 'ends_at' => '2025-11-07'],
            ['title' => 'Exam Period',           'type' => 'EXAM_PERIOD',    'starts_at' => '2025-12-01', 'ends_at' => '2025-12-15'],
            ['title' => 'Semester 1 End',        'type' => 'SEMESTER_END',   'starts_at' => '2025-12-19', 'ends_at' => '2025-12-19'],
            ['title' => 'Semester 2 Start',     'type' => 'SEMESTER_START', 'starts_at' => '2026-01-12', 'ends_at' => '2026-01-12'],
            ['title' => 'Public Holiday',       'type' => 'HOLIDAY',        'starts_at' => '2026-02-10', 'ends_at' => '2026-02-10'],
            ['title' => 'Final Exam Period',    'type' => 'EXAM_PERIOD',    'starts_at' => '2026-05-04', 'ends_at' => '2026-05-22'],
            ['title' => 'Semester 2 End',       'type' => 'SEMESTER_END',   'starts_at' => '2026-05-29', 'ends_at' => '2026-05-29'],
        ];

        foreach ($events as $event) {
            CalendarEvent::factory()->create(array_merge($event, [
                'description' => '',
                'created_by'  => $admin->id,
            ]));
        }
    }
}
