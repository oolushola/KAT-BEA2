<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class bussinessUnitHead
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
        $user = Auth::user()->role_id;
        if( ($user != 4) && ($user != 6) && ($user != 1) && ($user != 2) && ($user != 3)) {
            $location = '/dashboard';
        }

        return $next($request);
    }
}
