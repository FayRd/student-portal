<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleResource;
use App\Models\ResourceFolder;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::all()->each(function ($module) {
            $lecturer = $module->editors()->first();

            // Create 4 root lesson folders per module
            ResourceFolder::factory()->count(4)->create([
                'module_id' => $module->id,
                'parent_id' => null,
            ])->each(function ($folder, $index) use ($module, $lecturer) {
                $folder->update(['order' => $index + 1]);

                // 2 resources per folder
                ModuleResource::factory()->count(2)->create([
                    'module_id'   => $module->id,
                    'folder_id'   => $folder->id,
                    'uploaded_by' => $lecturer?->id,
                ]);
            });
        });
    }
}
