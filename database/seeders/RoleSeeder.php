<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Modules
            'view modules', 'create modules', 'update modules', 'delete modules',

            // Classes
            'view classes', 'create classes', 'update classes', 'delete classes',

            // Assignments
            'view assignments', 'create assignments', 'update assignments', 'delete assignments',

            // Submissions
            'view submissions', 'submit assignments', 'delete submissions',

            // Grades
            'view grades', 'grade submissions', 'update grades', 'delete grades',

            // Attendance
            'view attendance', 'mark attendance', 'update attendance', 'delete attendance',

            // Announcements
            'view announcements', 'create announcements', 'update announcements', 'delete announcements',

            // Calendar events
            'view calendar events', 'create calendar events', 'update calendar events', 'delete calendar events',

            // Resource folders
            'view resource folders', 'create resource folders', 'update resource folders', 'delete resource folders',

            // Module resources
            'view module resources', 'create module resources', 'update module resources', 'delete module resources',

            // Users
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $lecturer = Role::firstOrCreate(['name' => 'lecturer']);
        $lecturer->givePermissionTo([
            'view modules', 'update modules',
            'view classes', 'create classes', 'update classes', 'delete classes',
            'view assignments', 'create assignments', 'update assignments', 'delete assignments',
            'view submissions',
            'view grades', 'grade submissions', 'update grades',
            'view attendance', 'mark attendance', 'update attendance',
            'view announcements', 'create announcements', 'update announcements', 'delete announcements',
            'view resource folders', 'create resource folders', 'update resource folders', 'delete resource folders',
            'view module resources', 'create module resources', 'update module resources', 'delete module resources',
        ]);

        $student = Role::firstOrCreate(['name' => 'student']);
        $student->givePermissionTo([
            'view modules',
            'view classes',
            'view assignments', 'submit assignments',
            'view submissions',
            'view grades',
            'view attendance',
            'view announcements',
            'view resource folders',
            'view module resources',
        ]);
    }
}
