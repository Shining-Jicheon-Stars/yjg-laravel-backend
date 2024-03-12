<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     */
    public function handle($request, $next, ...$guards): mixed
    {
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                $request->grd = $guard;
                return $next($request);
            }
        }
        return response()->json(['error' => '인증되지 않은 유저입니다.'], 401);
    }
}
