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
        $user->username = $data->username;
        $user->nom = $data->nom;
        $user->prenom = $data->prenom;
        $user->email = $data->email;
        $user->role_id = $data->role->id;
        $user->password = \Hash::make($data->password);

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
        $user->username = $data->username;
        $user->nom = $data->nom;
        $user->prenom = $data->prenom;
        $user->email = $data->email;
        $user->role_id = $data->role->id;

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
