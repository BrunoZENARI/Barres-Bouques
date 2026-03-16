<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Controllers\EnvController;
use App\Models\Role;

class UserController extends Controller
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
    public function getusers(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $users = User::with('role');
        $count = User::whereRaw("1 = 1");

        $env = new EnvController();
        $count = $env->QueryBuilder($count,$data,true);
        $users = $env->QueryBuilder($users,$data);
        
        
        return json_encode(array('payload' => $users->get(), 'count' => $count));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getroles(Request $request)
    {
        $roles = Role::all();
        
        return json_encode(array('roles' => $roles));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createuser(Request $request)
    {
        $data = json_decode($request->user);

        $user = new User();
        $user->username = $data->username;
        $user->nom = $data->nom;
        $user->prenom = $data->prenom;
        $user->email = $data->email;
        $user->roles_id = $data->role->id;
        $user->password = \Hash::make($data->password);

        $user->save();

        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateuser(Request $request)
    {
        $data = json_decode($request->user);

        $user = User::findOrFail($data->id);
        $user->username = $data->username;
        $user->nom = $data->nom;
        $user->prenom = $data->prenom;
        $user->email = $data->email;
        $user->roles_id = $data->role->id;

        $user->save();
        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updatepassworduser(Request $request)
    {
        $data = json_decode($request->user);

        $user = User::findOrFail($data->id);
        $user->password = \Hash::make($data->password);

        $user->save();
        
        return json_encode(true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleteuser(Request $request)
    {
        $data = json_decode($request->user);

        $user = User::findOrFail($data->id);
        $user->delete();
        
        return json_encode(true);
    }
}
