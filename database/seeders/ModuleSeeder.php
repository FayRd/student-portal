<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin    = User::where('email', 'admin@portal.test')->first();
        $lecturers = User::whereNotNull('institutional_id')
            ->where('institutional_id', 'like', '1%')
            ->where('email', '!=', 'admin@portal.test')
            ->get();

        // 4 active modules
        Module::factory()->count(4)->create(['created_by' => $admin->id])
            ->each(function ($module, $index) use ($lecturers) {
                $module->editors()->attach($lecturers[$index % $lecturers->count()]->id, [
                    'role'       => 'editor',
                    'created_at' => now(),
                ]);
            });

        // 1 upcoming module
        Module::factory()->upcoming()->create(['created_by' => $admin->id])
            ->editors()->attach($lecturers->first()->id, [
                'role'       => 'editor',
                'created_at' => now(),
            ]);

        // 1 archived module
        Module::factory()->archived()->create(['created_by' => $admin->id])
            ->editors()->attach($lecturers->last()->id, [
                'role'       => 'editor',
                'created_at' => now(),
            ]);
    }
}
