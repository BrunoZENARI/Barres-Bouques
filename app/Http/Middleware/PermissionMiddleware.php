<?php

namespace App\Http\Middleware;
use \Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;

use Closure;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param   string   $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if(!$request->user()->hasPermission($permission)) {
            if($request->user()->hasPermission('can_see_home_page')){
                return redirect(RouteServiceProvider::HOME);
            }else{
                abort(403);
            }
            
        }
        return $next($request);
    }
}
