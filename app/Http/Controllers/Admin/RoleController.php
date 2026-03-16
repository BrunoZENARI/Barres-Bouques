<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Controllers\EnvController;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Récupération des utilisteurs pour l'interface administrateur.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getroles(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $roles = Role::with('permissions');
        $count = Role::whereRaw("1 = 1");

        $env = new EnvController();
        $count = $env->QueryBuilder($count,$data,true);
        $roles = $env->QueryBuilder($roles,$data);
        
        
        return json_encode(array('payload' => $roles->get(), 'count' => $count));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getpermissions(Request $request)
    {
        $permissions = Permission::all();
        
        return json_encode(array('permissions' => $permissions));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createrole(Request $request)
    {
        $data = json_decode($request->role);
        $permissions = json_decode($request->permissions);

        $role = new Role();
        $role->r_slug = $data->r_slug;
        $role->r_libelle = $data->r_libelle;

        $role->save();

        $role->permissions()->sync($permissions);

        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updaterole(Request $request)
    {
        $data = json_decode($request->role);
        $permissions = json_decode($request->permissions);

        $role = Role::findOrFail($data->id);
        $role->r_slug = $data->r_slug;
        $role->r_libelle = $data->r_libelle;

        $role->save();

        $role->permissions()->sync($permissions);
        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleterole(Request $request)
    {
        $data = json_decode($request->role);

        $role = Role::findOrFail($data->id);
        $role->permissions()->sync([]);
        $role->delete();
        
        return json_encode(true);
    }
}
