<?php

namespace App\Repositories\Token;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TokenRepository
{
    public function findUserByEmail($email)
    {
        return DB::table('users')->where('email', $email)->first();
    }

    public function createToken($user, $tokenName)
    {
        $plainTextToken = Str::random(40);
        $token = hash('sha256', $plainTextToken);

        $tokenId = DB::table('personal_access_tokens')->insertGetId([
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id,
            'name' => $tokenName,
            'token' => $token,
            'abilities' => '["*"]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $tokenId . '|' . $plainTextToken;
    }

    public function revokeToken($tokenId)
    {
        return DB::table('personal_access_tokens')->where('id', $tokenId)->delete();
    }

    public function revokeAllTokens($userId)
    {
        return DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\Models\User')
            ->where('tokenable_id', $userId)
            ->delete();
    }

    public function listTokens($userId)
    {
        return DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\Models\User')
            ->where('tokenable_id', $userId)
            ->select('id', 'name', 'created_at', 'last_used_at')
            ->get();
    }
}
