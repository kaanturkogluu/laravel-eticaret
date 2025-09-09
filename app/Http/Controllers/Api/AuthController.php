<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * API token ile giriş yap
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string|in:read,write,delete,admin',
            'expires_in_days' => 'integer|min:1|max:365'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Giriş bilgileri hatalı'
            ], 401);
        }

        $user = Auth::user();
        
        // E-posta doğrulama kontrolü
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'E-posta adresinizi doğrulamanız gerekiyor'
            ], 403);
        }

        // API token oluştur
        $abilities = $request->get('abilities', ['read']);
        $expiresInDays = $request->get('expires_in_days', 30);
        
        $apiToken = ApiToken::createToken(
            $user,
            $request->device_name,
            $abilities,
            $expiresInDays
        );

        return response()->json([
            'success' => true,
            'message' => 'Giriş başarılı',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                ],
                'token' => $apiToken->plainTextToken ?? 'token-not-available',
                'token_name' => $apiToken->name,
                'abilities' => $apiToken->abilities,
                'expires_at' => $apiToken->expires_at?->toISOString(),
            ]
        ]);
    }

    /**
     * Mevcut token'ları listele
     */
    public function tokens(Request $request)
    {
        $user = $request->user();
        
        $tokens = $user->activeApiTokens()
            ->select(['id', 'name', 'abilities', 'last_used_at', 'expires_at', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens
        ]);
    }

    /**
     * Token'ı yenile
     */
    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token_id' => 'required|integer|exists:api_tokens,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $apiToken = $user->apiTokens()->findOrFail($request->token_id);

        if (!$apiToken->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Token devre dışı'
            ], 400);
        }

        $newToken = $apiToken->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Token yenilendi',
            'data' => [
                'token' => $newToken,
                'expires_at' => $apiToken->expires_at?->toISOString(),
            ]
        ]);
    }

    /**
     * Token'ı devre dışı bırak
     */
    public function revokeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token_id' => 'required|integer|exists:api_tokens,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $apiToken = $user->apiTokens()->findOrFail($request->token_id);

        $apiToken->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Token devre dışı bırakıldı'
        ]);
    }

    /**
     * Tüm token'ları devre dışı bırak
     */
    public function revokeAllTokens(Request $request)
    {
        $user = $request->user();
        
        $user->apiTokens()->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Tüm token\'lar devre dışı bırakıldı'
        ]);
    }

    /**
     * Mevcut kullanıcı bilgilerini getir
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $currentToken = $request->attributes->get('api_token');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'email_verified_at' => $user->email_verified_at?->toISOString(),
                ],
                'current_token' => [
                    'id' => $currentToken->id,
                    'name' => $currentToken->name,
                    'abilities' => $currentToken->abilities,
                    'last_used_at' => $currentToken->last_used_at?->toISOString(),
                    'expires_at' => $currentToken->expires_at?->toISOString(),
                ]
            ]
        ]);
    }

    /**
     * Çıkış yap (token'ı devre dışı bırak)
     */
    public function logout(Request $request)
    {
        $currentToken = $request->attributes->get('api_token');
        
        if ($currentToken) {
            $currentToken->revoke();
        }

        return response()->json([
            'success' => true,
            'message' => 'Çıkış yapıldı'
        ]);
    }
}
