<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::whereNotNull('institutional_id')
            ->where('institutional_id', 'like', '2%')
            ->get();

        $activeModules = Module::where('status', 'ACTIVE')->get();
        $archivedModule = Module::where('status', 'ARCHIVED')->first();

        $students->each(function ($student) use ($activeModules, $archivedModule) {
            // Enroll each student in 3 random active modules
            $activeModules->random(3)->each(function ($module) use ($student) {
                Enrollment::factory()->create([
                    'user_id'   => $student->id,
                    'module_id' => $module->id,
                ]);
            });

            // Enroll all students in the archived module as completed
            if ($archivedModule) {
                Enrollment::factory()->completed()->create([
                    'user_id'   => $student->id,
                    'module_id' => $archivedModule->id,
                ]);
            }
        });
    }
}
