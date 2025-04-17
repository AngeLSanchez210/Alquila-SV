<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Denegade
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
        if (!auth()->check()) {
            // Para solicitudes AJAX o API
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado'], 404);
            }
            
            // Para solicitudes normales, devuelve un 404
            abort(404);
        }

        return $next($request);
    }
}