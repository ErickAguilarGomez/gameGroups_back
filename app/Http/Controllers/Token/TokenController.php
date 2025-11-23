<?php

namespace App\Http\Controllers\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Token\TokenService;

class TokenController extends Controller
{
    protected $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Generate a Personal Access Token for API access
     * Este endpoint es para backends externos que necesitan un token Bearer
     */
    public function createToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'token_name' => 'required|string',
        ]);

        $result = $this->tokenService->createToken($request);

        return response()->json($result);
    }

    /**
     * Revoke current token
     */
    public function revokeToken(Request $request)
    {
        $this->tokenService->revokeToken($request->user());
        return response()->json(['message' => 'Token revoked']);
    }

    /**
     * Revoke all user tokens
     */
    public function revokeAllTokens(Request $request)
    {
        $this->tokenService->revokeAllTokens($request->user());
        return response()->json(['message' => 'All tokens revoked']);
    }

    /**
     * List user tokens
     */
    public function listTokens(Request $request)
    {
        $tokens = $this->tokenService->listTokens($request->user());
        return response()->json($tokens);
    }
}
