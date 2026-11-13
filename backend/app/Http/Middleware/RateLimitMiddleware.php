<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $maxAttempts = '60', int $decayMinutes = 1): Response
    {
        $key = $request->ip() . ':' . $request->path();

        if (RateLimiter::tooManyAttempts($key, (int) $maxAttempts)) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'method' => $request->method(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
            ], 429, [
                'Retry-After' => RateLimiter::availableIn($key),
            ]);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
