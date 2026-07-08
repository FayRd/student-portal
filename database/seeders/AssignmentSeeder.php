<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::all()->each(function ($module) {
            $lecturer = $module->editors()->first();

            // 2 past assignments
            Assignment::factory()->past()->count(2)->create([
                'module_id'  => $module->id,
                'created_by' => $lecturer?->id,
            ]);

            // 1 upcoming assignment (only for active modules)
            if ($module->status === 'ACTIVE') {
                Assignment::factory()->create([
                    'module_id'  => $module->id,
                    'created_by' => $lecturer?->id,
                ]);
            }
        });
    }
}
