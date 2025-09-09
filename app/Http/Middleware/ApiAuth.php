<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $ability = '*'): Response
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return $this->unauthorizedResponse('API token gerekli');
        }

        $apiToken = $this->findValidToken($token);

        if (!$apiToken) {
            return $this->unauthorizedResponse('Geçersiz API token');
        }

        if (!$apiToken->isValid()) {
            return $this->unauthorizedResponse('API token süresi dolmuş veya devre dışı');
        }

        if (!$this->hasAbility($apiToken, $ability)) {
            return $this->forbiddenResponse('Bu işlem için yetkiniz yok');
        }

        // Token'ı kullan (last_used_at güncelle)
        $apiToken->use();

        // Kullanıcıyı request'e ekle
        $request->setUserResolver(function () use ($apiToken) {
            return $apiToken->user;
        });

        // API token'ı request'e ekle
        $request->attributes->set('api_token', $apiToken);

        return $next($request);
    }

    /**
     * Request'ten token'ı al
     */
    private function getTokenFromRequest(Request $request): ?string
    {
        // Authorization header'dan Bearer token
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // X-API-TOKEN header'dan
        if ($request->hasHeader('X-API-TOKEN')) {
            return $request->header('X-API-TOKEN');
        }

        // Query parameter'dan
        if ($request->has('api_token')) {
            return $request->get('api_token');
        }

        return null;
    }

    /**
     * Geçerli token'ı bul
     */
    private function findValidToken(string $token): ?ApiToken
    {
        $hashedToken = hash('sha256', $token);
        
        return ApiToken::active()
            ->where('token', $hashedToken)
            ->with('user')
            ->first();
    }

    /**
     * Token'ın belirli yetkiye sahip olup olmadığını kontrol et
     */
    private function hasAbility(ApiToken $apiToken, string $ability): bool
    {
        if ($ability === '*') {
            return true;
        }

        $abilities = $apiToken->abilities ?? [];
        
        return in_array('*', $abilities) || in_array($ability, $abilities);
    }

    /**
     * 401 Unauthorized response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => 'UNAUTHORIZED'
        ], 401);
    }

    /**
     * 403 Forbidden response
     */
    private function forbiddenResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => 'FORBIDDEN'
        ], 403);
    }
}
