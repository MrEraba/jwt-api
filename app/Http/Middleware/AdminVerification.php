<?php

namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class AdminVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::parseToken()->authenticate();
        //
        if($user->user_type != 'admin'){
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }


}
