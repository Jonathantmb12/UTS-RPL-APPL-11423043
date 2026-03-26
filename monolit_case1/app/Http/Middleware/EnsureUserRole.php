<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized - authentication required',
                ], 401);
            }
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized - insufficient permissions',
                ], 403);
            }
            return abort(403, 'Unauthorized - insufficient permissions');
        }

        return $next($request);
    }
}
