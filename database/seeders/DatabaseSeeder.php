<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        

        \App\Models\Role::create([
            'r_slug' => 'user',
            'r_libelle' => 'Utilisateur',
        ]);

        \App\Models\Role::create([
            'r_slug' => 'admin',
            'r_libelle' => 'Administrateur',
        ]);

        \App\Models\Permission::create([
            'p_slug' => 'can_see_home_page',
            'p_libelle' => 'Peut voir la home page',
            'p_categorie' => 'GLOBAL',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_use_admin',
            'p_libelle' => 'Peut administrer le site',
            'p_categorie' => 'ADMINISTRATION',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_use_admin_users_page',
            'p_libelle' => 'Peut accéder à la page administration utilisateurs',
            'p_categorie' => 'ADMINISTRATION_USER',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_see_admin_users',
            'p_libelle' => 'Peut voir la liste des utilisateurs',
            'p_categorie' => 'ADMINISTRATION_USER',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_create_admin_users',
            'p_libelle' => 'Peut créer des utilisateurs',
            'p_categorie' => 'ADMINISTRATION_USER',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_update_admin_users',
            'p_libelle' => 'Peut mettre à jour des utilisateurs',
            'p_categorie' => 'ADMINISTRATION_USER',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_delete_admin_users',
            'p_libelle' => 'Peut supprimer des utilisateurs',
            'p_categorie' => 'ADMINISTRATION_USER',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_use_admin_permissions_page',
            'p_libelle' => 'Peut accéder à la page administration permissions',
            'p_categorie' => 'ADMINISTRATION_PERMISSION',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_see_admin_permissions',
            'p_libelle' => 'Peut voir la liste des permissions',
            'p_categorie' => 'ADMINISTRATION_PERMISSION',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_create_admin_permissions',
            'p_libelle' => 'Peut créer des permissions',
            'p_categorie' => 'ADMINISTRATION_PERMISSION',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_update_admin_permissions',
            'p_libelle' => 'Peut mettre à jour des permissions',
            'p_categorie' => 'ADMINISTRATION_PERMISSION',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_delete_admin_permissions',
            'p_libelle' => 'Peut supprimer des permissions',
            'p_categorie' => 'ADMINISTRATION_PERMISSION',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_use_admin_roles_page',
            'p_libelle' => 'Peut accéder à la page administration roles',
            'p_categorie' => 'ADMINISTRATION_ROLE',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_see_admin_roles',
            'p_libelle' => 'Peut voir la liste des roles',
            'p_categorie' => 'ADMINISTRATION_ROLE',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_create_admin_roles',
            'p_libelle' => 'Peut créer des roles',
            'p_categorie' => 'ADMINISTRATION_ROLE',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_update_admin_roles',
            'p_libelle' => 'Peut mettre à jour des roles',
            'p_categorie' => 'ADMINISTRATION_ROLE',
        ]);
        \App\Models\Permission::create([
            'p_slug' => 'can_delete_admin_roles',
            'p_libelle' => 'Peut supprimer des roles',
            'p_categorie' => 'ADMINISTRATION_ROLE',
        ]);

        \App\Models\RolePermission::create([
            'id_role' => 1,
            'id_permission' => 1,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 1,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 2,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 3,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 4,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 5,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 6,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 7,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 8,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 9,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 10,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 11,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 12,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 13,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 14,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 15,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 16,
        ]);
        \App\Models\RolePermission::create([
            'id_role' => 2,
            'id_permission' => 17,
        ]);


        \App\Models\User::factory(10)->create();
    }
}
