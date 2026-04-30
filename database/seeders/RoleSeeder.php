<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $memberPermissions = Permission::whereIn('slug', [
            'can_see_books_page',
            'can_see_member_portal',
        ])->pluck('id');

        $member = Role::firstOrCreate(
            ['slug' => 'member'],
            ['name' => 'Adhérent']
        );
        $member->permissions()->sync($memberPermissions);

        $librarianPermissions = Permission::whereIn('slug', [
            'can_see_home_page',
            'can_see_books_page',
            'can_create_books',
            'can_update_books',
            'can_see_loans_page',
            'can_create_loans',
            'can_update_loans',
            'can_delete_loans',
            'can_see_reminders_page',
            'can_see_stats_page',
            'can_see_member_portal',
            'can_manage_reservations',
            'can_use_admin',
            'can_use_admin_users_page',
            'can_see_admin_users',
            'can_create_admin_users',
            'can_update_admin_users',
            'can_delete_admin_users',
        ])->pluck('id');

        $librarian = Role::firstOrCreate(
            ['slug' => 'librarian'],
            ['name' => 'Bibliothécaire']
        );
        $librarian->permissions()->sync($librarianPermissions);

        $adminPermissions = Permission::pluck('id');

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrateur']
        );
        $admin->permissions()->sync($adminPermissions);
    }
}
