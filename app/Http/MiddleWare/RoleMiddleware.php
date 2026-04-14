<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        $hasRole = match($role) {
            'buyer' => $user->isBuyer(),
            'vendor' => $user->isVendor(),
            'admin' => $user->isAdmin(),
            default => false,
        };

        if (!$hasRole) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
