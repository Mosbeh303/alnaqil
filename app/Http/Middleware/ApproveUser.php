<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ApproveUser extends Middleware
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
        if (Auth::user()->type == "client" && Auth::user()->store){
            if (Auth::user()->store->status != 'approved')
                return redirect()->route('under-review');
        }

        return $next($request);
    }
}
