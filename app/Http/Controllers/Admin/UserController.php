<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EnvController;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retourne la liste paginée des utilisateurs.
     */
    public function index(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $users = User::with('role');
        $count = User::whereRaw('1 = 1');

        $env = new EnvController();
        $count = $env->QueryBuilder($count, $data, true);
        $users = $env->QueryBuilder($users, $data);

        return json_encode(['payload' => $users->get(), 'count' => $count]);
    }

    /**
     * Retourne la liste des rôles pour les selects.
     */
    public function getRoles(Request $request)
    {
        $roles = Role::all();

        return json_encode(['roles' => $roles]);
    }

    /**
     * Crée un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->user);

        $user = new User();
        $user->nom            = $data->nom;
        $user->prenom         = $data->prenom;
        $user->email          = $data->email;
        $user->date_naissance      = $data->date_naissance ?? null;
        $user->telephone           = $data->telephone ?? null;
        $user->adresse_numero      = $data->adresse_numero ?? null;
        $user->adresse_rue         = $data->adresse_rue ?? null;
        $user->adresse_complement1 = $data->adresse_complement1 ?? null;
        $user->adresse_complement2 = $data->adresse_complement2 ?? null;
        $user->adresse_code_postal = $data->adresse_code_postal ?? null;
        $user->adresse_ville       = $data->adresse_ville ?? null;
        $user->role_id             = isset($data->role) ? $data->role->id : Role::where('slug', 'member')->value('id');
        $user->password            = \Hash::make($data->password ?? 'ChangeMe@1234');

        $user->save();

        return json_encode(true);
    }

    /**
     * Met à jour un utilisateur existant.
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->user);

        $user = User::findOrFail($id);
        $user->nom            = $data->nom;
        $user->prenom         = $data->prenom;
        $user->email          = $data->email;
        $user->date_naissance      = $data->date_naissance ?? null;
        $user->telephone           = $data->telephone ?? null;
        $user->adresse_numero      = $data->adresse_numero ?? null;
        $user->adresse_rue         = $data->adresse_rue ?? null;
        $user->adresse_complement1 = $data->adresse_complement1 ?? null;
        $user->adresse_complement2 = $data->adresse_complement2 ?? null;
        $user->adresse_code_postal = $data->adresse_code_postal ?? null;
        $user->adresse_ville       = $data->adresse_ville ?? null;
        $user->role_id             = $data->role->id;

        $user->save();

        return json_encode(true);
    }

    /**
     * Met à jour le mot de passe d'un utilisateur.
     */
    public function updatePassword(Request $request, $id)
    {
        $data = json_decode($request->user);

        $user = User::findOrFail($id);
        $user->password = \Hash::make($data->password);

        $user->save();

        return json_encode(true);
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return json_encode(true);
    }
}
