<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Module;
use App\Models\ResourceFolder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::all()->each(function ($module) {
            $folders = ResourceFolder::where('module_id', $module->id)
                ->whereNull('parent_id')
                ->get();

            // 2 class sessions per folder (same lesson, different groups)
            $folders->each(function ($folder) use ($module) {
                ClassSession::factory()->count(2)->create([
                    'module_id'          => $module->id,
                    'resource_folder_id' => $folder->id,
                ]);
            });
        });
    }
}
