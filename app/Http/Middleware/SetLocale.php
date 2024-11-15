<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = session()->get('locale');
        if ($locale) {
            app()->setLocale($locale);
        }else
            app()->setLocale('ar');

       // $response = $next($request);
        // if($next($request) instanceof \Illuminate\Http\Response)
        //     return $next($request)->withHeaders([
        //         'Content-Language' => app()->getLocale(),
        //     ]);

        return $next($request);
    }
}
