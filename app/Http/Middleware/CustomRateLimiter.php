<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class CustomRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $key, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request, $key);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Çok fazla istek gönderildi. Lütfen ' . $seconds . ' saniye sonra tekrar deneyin.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }

    /**
     * Resolve the request signature.
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        if ($key === 'login') {
            return 'login:' . $request->ip();
        }
        
        if ($key === 'register') {
            return 'register:' . $request->ip();
        }
        
        if ($key === 'api') {
            return 'api:' . $request->ip();
        }
        
        if ($key === 'email-verification') {
            return 'email-verification:' . $request->user()?->id ?? $request->ip();
        }
        
        return $key . ':' . $request->ip();
    }
}
