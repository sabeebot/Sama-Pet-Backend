<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAbilities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,  ...$abilities): Response
    {
        foreach ($abilities as $ability) {
            if (!$request->user() || !$request->user()->tokenCan($ability)) {
                return response()->json(['error' => 'Unauthorized, not an admin'], 403);
            }
        }
        return $next($request);
    }
}
