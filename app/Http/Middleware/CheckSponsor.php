<?php

namespace App\Http\Middleware;

use Closure;

class CheckSponsor
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
        $hasValidApiToken = env('ENROLLMENT_API_TOKEN') == $request->post('apiToken');

        if (!session()->has('sponsor') && !$hasValidApiToken) {
            return redirect('enrollment/sponsor');
        }

        return $next($request);
    }
}
