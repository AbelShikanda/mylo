<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = $request->user();

        if (! $admin || ! $admin instanceof \App\Models\Identity\Administrator) {
            return response()->json(['errors' => ['permission' => ['Unauthorized.']]], 403);
        }

        if (! $admin->hasPermission($permission)) {
            return response()->json(['errors' => ['permission' => ['You do not have the required permission.']]], 403);
        }

        return $next($request);
    }
}
