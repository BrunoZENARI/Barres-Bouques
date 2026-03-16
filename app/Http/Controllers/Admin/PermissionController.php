<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EnvController;
use App\Models\Permission;

class PermissionController extends Controller
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
    public function getpermissions(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $permissions = Permission::whereRaw("1 = 1");
        $count = Permission::whereRaw("1 = 1");

        $env = new EnvController();
        $count = $env->QueryBuilder($count,$data,true);
        $permissions = $env->QueryBuilder($permissions,$data);
        
        
        return json_encode(array('payload' => $permissions->get(), 'count' => $count));
    }

    


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createpermission(Request $request)
    {
        $data = json_decode($request->permission);

        $permission = new Permission();
        $permission->p_slug = $data->p_slug;
        $permission->p_libelle = $data->p_libelle;
        $permission->p_categorie = $data->p_categorie;

        $permission->save();

        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updatepermission(Request $request)
    {
        $data = json_decode($request->permission);

        $permission = Permission::findOrFail($data->id);
        $permission->p_slug = $data->p_slug;
        $permission->p_libelle = $data->p_libelle;
        $permission->p_categorie = $data->p_categorie;

        $permission->save();
        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deletepermission(Request $request)
    {
        $data = json_decode($request->permission);

        $permission = Permission::findOrFail($data->id);
        $permission->delete();
        
        return json_encode(true);
    }
}
