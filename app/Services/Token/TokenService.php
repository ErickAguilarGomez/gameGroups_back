<?php

namespace App\Services\Token;

use App\Repositories\Token\TokenRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenService
{
    protected $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function createToken($request)
    {
        $user = $this->tokenRepository->findUserByEmail($request->email);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        $token = $this->tokenRepository->createToken($user, $request->token_name);
        $user->load('role');

        return [
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function revokeToken($user)
    {
        $tokenId = $user->currentAccessToken()->id;
        return $this->tokenRepository->revokeToken($tokenId);
    }

    public function revokeAllTokens($user)
    {
        return $this->tokenRepository->revokeAllTokens($user->id);
    }

    public function listTokens($user)
    {
        return $this->tokenRepository->listTokens($user->id);
    }
}
