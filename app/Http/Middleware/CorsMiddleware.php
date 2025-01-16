<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = ['http://localhost:4200']; // Add other allowed origins if needed

        $origin = $request->headers->get('Origin');

        if (in_array($origin, $allowedOrigins)) {
            $headers = [
                'Access-Control-Allow-Origin'      => $origin,
                'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers'     => 'Content-Type, Accept, Authorization, X-Requested-With, Application'
            ];

            if ($request->getMethod() == "OPTIONS") {
                return response()->json('{"method":"OPTIONS"}', 200, $headers);
            }

            $response = $next($request);

            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }

            return $response;
        }

        return $next($request);
    }
}
