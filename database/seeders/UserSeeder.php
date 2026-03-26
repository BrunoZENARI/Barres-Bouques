<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $librarianRole = Role::where('slug', 'librarian')->first();
        $memberRole = Role::where('slug', 'member')->first();

        // Compte administrateur par défaut
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nom'       => 'Admin',
                'prenom'    => 'Super',
                'email'     => 'admin@bibliotheque.fr',
                'role_id'   => $adminRole->id,
                'is_active' => true,
                'password'  => Hash::make('Admin@1234'),
            ]
        );

        // Compte bibliothécaire de démonstration
        User::firstOrCreate(
            ['username' => 'biblio'],
            [
                'nom'       => 'Dupont',
                'prenom'    => 'Marie',
                'email'     => 'marie.dupont@bibliotheque.fr',
                'role_id'   => $librarianRole->id,
                'is_active' => true,
                'password'  => Hash::make('Biblio@1234'),
            ]
        );

        // Adhérents de démonstration
        User::factory(10)->create(['role_id' => $memberRole->id]);
    }
}
