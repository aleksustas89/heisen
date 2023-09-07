<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceLogoutUnActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->force_logout == 1) {

            $request->session()->invalidate();

            $request->user()->force_logout = 0;
            $request->user()->save();

            return redirect()->refresh();
        }

        if ($request->user()->active == 0) {

            $request->session()->invalidate();
            $request->user()->save();

            return redirect()->refresh();
        }


        return $next($request);
    }
}
