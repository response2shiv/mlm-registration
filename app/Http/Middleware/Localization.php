<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Session::has('local')) {
            \App::setLocale(\Session::get('local'));
            Carbon::setLocale(\Session::get('local'));
        }
        return $next($request);
    }
}
