<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use \Closure;

class CheckForMaintenanceMode extends Middleware
{
    public function handle($request, Closure $next)
    {
        if (isset($data['allowed']) && IpUtils::checkIp($request->ip(), (array) $data['allowed'])) {
            return $next($request);
        }

        if ($this->inExceptArray($request)) {
            return $next($request);
        }
        
        $maintenance = json_decode(file_get_contents("https://myibuumerang.s3.amazonaws.com/maintenance.json"), true);
        if($maintenance['maintenance'] == true && config('app.debug') == false){
            return redirect(route('maintenance'));
        }
        return parent::handle($request, $next);
    }
    protected $except = [
        '/maintenance'
    ];
}
