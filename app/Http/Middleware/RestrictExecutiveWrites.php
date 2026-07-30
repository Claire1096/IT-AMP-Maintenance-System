<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictExecutiveWrites
{
    public function handle(Request $request, Closure $next): Response
    {
        $isWrite = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])
            && ! $request->routeIs('logout');

        if ($isWrite && auth()->check() && auth()->user()->role === 'executive') {
            return back()->with('error', 'Executive accounts are view-only and cannot make changes.');
        }

        return $next($request);
    }
}
