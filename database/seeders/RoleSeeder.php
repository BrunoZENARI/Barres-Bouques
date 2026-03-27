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
            'can_see_loans_page',
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
