<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class LogUserAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $startTime = microtime(true); // Capture le temps de début d'exécution

        // Exécuter la requête suivante et obtenir la réponse
        $response = $next($request);

        $user = $request->user(); // Récupère l'utilisateur connecté, s'il existe

        $endTime = microtime(true); // Capture le temps de fin d'exécution
        $duration = $endTime - $startTime; // Calcule la durée d'exécution

        if(!str_contains($request->path(), '_debugbar'))
        // Enregistrer les informations dans MongoDB
        UserLog::create([
            'user_id'    => $user ? $user->id : null,
            'action'     => $request->path(), // Récupère l'URL demandée
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
            'ip'         => /*$request->ip()*/$this->getIp(),
            'user_agent' => $request->header('User-Agent'),
            'status_code'=> $response->getStatusCode(), // Récupère le statut de la réponse HTTP
            'duration'   => $duration, // Enregistre la durée d'exécution
            'created_at' => now(),
        ]);

        return $response;
    }


    private function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}
