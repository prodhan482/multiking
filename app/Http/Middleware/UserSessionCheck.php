<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class UserSessionCheck
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
        $token = $request->header('Authorization');

        if(empty($token))
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'description'=>'No Authorization Data Found'
            ), 401);
        }

        //Redis::set('user:token:'.str_replace("Basic ","",$token), Str::random(32));
        //Redis::set('user:token:'.str_replace("Basic ","",$token), 'Taylor', 'EX', (60 * 60 * 2));

        $jwtToken = Redis::get('user:token:'.str_replace("Bearer ","",$token));

        if(empty($jwtToken))
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'description'=>'Invalid Token'
            ), 401);
        }

       return $next($request);
    }
}
