<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EnvController;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retourne la liste paginée des rôles.
     */
    public function index(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $roles = Role::with('permissions');
        $count = Role::whereRaw('1 = 1');

        $env = new EnvController();
        $count = $env->QueryBuilder($count, $data, true);
        $roles = $env->QueryBuilder($roles, $data);

        return json_encode(['payload' => $roles->get(), 'count' => $count]);
    }

    /**
     * Retourne la liste des permissions pour les selects.
     */
    public function getPermissions(Request $request)
    {
        $permissions = Permission::all();

        return json_encode(['permissions' => $permissions]);
    }

    /**
     * Crée un nouveau rôle.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->role);
        $permissions = json_decode($request->permissions);

        $role = new Role();
        $role->slug = $data->slug;
        $role->name = $data->name;

        $role->save();

        $role->permissions()->sync($permissions);

        return json_encode(true);
    }

    /**
     * Met à jour un rôle existant.
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->role);
        $permissions = json_decode($request->permissions);

        $role = Role::findOrFail($id);
        $role->slug = $data->slug;
        $role->name = $data->name;

        $role->save();

        $role->permissions()->sync($permissions);

        return json_encode(true);
    }

    /**
     * Supprime un rôle.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->sync([]);
        $role->delete();

        return json_encode(true);
    }
}
