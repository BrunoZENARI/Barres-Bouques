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
            ['email' => 'admin@bibliotheque.fr'],
            [
                'nom'       => 'Admin',
                'prenom'    => 'Super',
                'role_id'   => $adminRole->id,
                'is_active' => true,
                'password'  => Hash::make('Admin@1234'),
            ]
        );

        // Compte bibliothécaire de démonstration
        User::firstOrCreate(
            ['email' => 'marie.dupont@bibliotheque.fr'],
            [
                'nom'       => 'Dupont',
                'prenom'    => 'Marie',
                'role_id'   => $librarianRole->id,
                'is_active' => true,
                'password'  => Hash::make('Biblio@1234'),
            ]
        );

        // Adhérents de démonstration
        User::factory(10)->create(['role_id' => $memberRole->id]);
    }
}
