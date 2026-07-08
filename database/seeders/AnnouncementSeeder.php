<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@portal.test')->first();

        // 3 global announcements from admin
        Announcement::factory()->global()->count(3)->create([
            'created_by' => $admin->id,
        ]);

        // 2 module-specific announcements per active module
        Module::where('status', 'ACTIVE')->each(function ($module) {
            $lecturer = $module->editors()->first();

            Announcement::factory()->count(2)->create([
                'module_id'  => $module->id,
                'created_by' => $lecturer?->id,
            ]);
        });
    }
}
