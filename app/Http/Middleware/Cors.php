<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        //, https://platform.flyhom.com/ , http://localhost:11000/
        //Origin, Methods, Content-Type, Authorization, X-Requested-With, x-xsrf-token, */*, X-PINGARUNER
        // POST, PUT, GET, DELETE, OPTIONS
        return $next($request)->header('Access-Control-Allow-Origin', '*')
                                ->header('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE, OPTIONS')
                                ->header('Access-Control-Allow-Headers', 'Origin, Methods, Content-Type, Authorization, X-Requested-With, x-xsrf-token, */*, X-PINGARUNER')
                                ->header('Access-Control-Allow-Credentials', true);
    }
}
