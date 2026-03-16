<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EnvController;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retourne la liste paginée des permissions.
     */
    public function index(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $permissions = Permission::whereRaw('1 = 1');
        $count = Permission::whereRaw('1 = 1');

        $env = new EnvController();
        $count = $env->QueryBuilder($count, $data, true);
        $permissions = $env->QueryBuilder($permissions, $data);

        return json_encode(['payload' => $permissions->get(), 'count' => $count]);
    }

    /**
     * Crée une nouvelle permission.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->permission);

        $permission = new Permission();
        $permission->slug = $data->slug;
        $permission->name = $data->name;
        $permission->category = $data->category;

        $permission->save();

        return json_encode(true);
    }

    /**
     * Met à jour une permission existante.
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->permission);

        $permission = Permission::findOrFail($id);
        $permission->slug = $data->slug;
        $permission->name = $data->name;
        $permission->category = $data->category;

        $permission->save();

        return json_encode(true);
    }

    /**
     * Supprime une permission.
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return json_encode(true);
    }
}
