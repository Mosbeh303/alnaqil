<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Providers\RouteServiceProvider;

class RedirectIfUserApproved extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next,  ...$guards)
    {
        if (Auth::check()){
            if ( Auth::user()->type == "client" && Auth::user()->store){
                if (Auth::user()->store->status == 'approved')
                    return redirect(RouteServiceProvider::HOME);
            }

            if (Auth::user()->type != "client" )
                return redirect(RouteServiceProvider::HOME);
        } else
            return redirect(RouteServiceProvider::HOME);

        return $next($request);
    }
}
