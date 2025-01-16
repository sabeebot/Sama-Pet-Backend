<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $ad = Auth::guard('admin')->user();
        $admin = Admin::where('id', $ad->id)->first();
        if (!$admin || !$admin->hasPermission($permission)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
