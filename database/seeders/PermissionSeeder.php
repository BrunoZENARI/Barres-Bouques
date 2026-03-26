<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Global
            ['slug' => 'can_see_home_page',         'name' => 'Peut voir la page d\'accueil',              'category' => 'GLOBAL'],

            // Ouvrages
            ['slug' => 'can_see_books_page',         'name' => 'Peut accéder à la page des ouvrages',       'category' => 'BOOKS'],
            ['slug' => 'can_create_books',           'name' => 'Peut créer des ouvrages',                   'category' => 'BOOKS'],
            ['slug' => 'can_update_books',           'name' => 'Peut modifier des ouvrages',                'category' => 'BOOKS'],
            ['slug' => 'can_delete_books',           'name' => 'Peut supprimer des ouvrages',               'category' => 'BOOKS'],

            // Emprunts
            ['slug' => 'can_see_loans_page',         'name' => 'Peut accéder à la page des emprunts',       'category' => 'LOANS'],
            ['slug' => 'can_create_loans',           'name' => 'Peut créer des emprunts',                   'category' => 'LOANS'],
            ['slug' => 'can_update_loans',           'name' => 'Peut enregistrer des retours',              'category' => 'LOANS'],
            ['slug' => 'can_delete_loans',           'name' => 'Peut supprimer des emprunts',               'category' => 'LOANS'],

            // Administration générale
            ['slug' => 'can_use_admin',              'name' => 'Peut accéder à l\'interface d\'administration', 'category' => 'ADMINISTRATION'],

            // Administration — Utilisateurs
            ['slug' => 'can_use_admin_users_page',   'name' => 'Peut accéder à la page administration utilisateurs', 'category' => 'ADMINISTRATION_USER'],
            ['slug' => 'can_see_admin_users',        'name' => 'Peut voir la liste des utilisateurs',       'category' => 'ADMINISTRATION_USER'],
            ['slug' => 'can_create_admin_users',     'name' => 'Peut créer des utilisateurs',               'category' => 'ADMINISTRATION_USER'],
            ['slug' => 'can_update_admin_users',     'name' => 'Peut modifier des utilisateurs',            'category' => 'ADMINISTRATION_USER'],
            ['slug' => 'can_delete_admin_users',     'name' => 'Peut supprimer des utilisateurs',           'category' => 'ADMINISTRATION_USER'],

            // Administration — Permissions
            ['slug' => 'can_use_admin_permissions_page', 'name' => 'Peut accéder à la page administration permissions', 'category' => 'ADMINISTRATION_PERMISSION'],
            ['slug' => 'can_see_admin_permissions',  'name' => 'Peut voir la liste des permissions',        'category' => 'ADMINISTRATION_PERMISSION'],
            ['slug' => 'can_create_admin_permissions','name' => 'Peut créer des permissions',               'category' => 'ADMINISTRATION_PERMISSION'],
            ['slug' => 'can_update_admin_permissions','name' => 'Peut modifier des permissions',            'category' => 'ADMINISTRATION_PERMISSION'],
            ['slug' => 'can_delete_admin_permissions','name' => 'Peut supprimer des permissions',           'category' => 'ADMINISTRATION_PERMISSION'],

            // Administration — Rôles
            ['slug' => 'can_use_admin_roles_page',   'name' => 'Peut accéder à la page administration rôles', 'category' => 'ADMINISTRATION_ROLE'],
            ['slug' => 'can_see_admin_roles',        'name' => 'Peut voir la liste des rôles',              'category' => 'ADMINISTRATION_ROLE'],
            ['slug' => 'can_create_admin_roles',     'name' => 'Peut créer des rôles',                     'category' => 'ADMINISTRATION_ROLE'],
            ['slug' => 'can_update_admin_roles',     'name' => 'Peut modifier des rôles',                  'category' => 'ADMINISTRATION_ROLE'],
            ['slug' => 'can_delete_admin_roles',     'name' => 'Peut supprimer des rôles',                 'category' => 'ADMINISTRATION_ROLE'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}
