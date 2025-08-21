<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class UserSessionCheckWeb
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
        $token = $request->session()->get('AuthorizationToken', '');
        if(empty($token))
        {
            Session::flush();
            return redirect('/login');
        }


        $jwtToken = Redis::get('user:token:'.str_replace("Bearer ","",$token));

        if(empty($jwtToken))
        {
            Session::flush();
            return redirect('/login');
        }

       return $next($request);
    }
}
