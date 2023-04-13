<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstanceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $instance_id = $request->route()->parameter('instance_id');
        if ($instance_id != null && !\Auth::user()->canAccessInstance($instance_id)) {
            return redirect()->route('getInstanceContext');
        }
        return $next($request);
    }
}
