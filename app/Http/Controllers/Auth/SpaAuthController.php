<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

class SpaAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $user = $this->authService->login($credentials['email'], $credentials['password']);

        if (!$user) {
            return response()->json(['message' => 'Credenciales inválidas'], 422);
        }

        if (!$this->authService->isUserApproved($user)) {
            $this->authService->logout();
            $message = $this->authService->getRejectionMessage($user);
            return response()->json(['message' => $message], 403);
        }
        $request->session()->regenerate();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname,
            'birthdate' => $user->birthdate,
            'email' => $user->email,
            'photo_rejection_reason' => $user->photo_rejection_reason,
            'account_status' => $user->account_status,
            'rejection_reason' => $user->rejection_reason,
            'role' => $user->role->name ?? 'user',
            'role_id' => $user->role_id,
            'group_id' => $user->group_id,
            'ban_reason' => $user->ban_reason,
            'banned_by' => $user->banned_by,
            'last_seen' => $user->last_seen,
            'social_network_id' => $user->social_network_id,
            'social_network' => $user->socialNetwork,
            'photo_url' => $user->photo_url,
            'photo_status' => $user->photo_status,
            'banned_at' => $user->banned_at,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $sessionCookie = cookie()->forget('laravel_session', '/', config('session.domain'));
        $xsrfCookie = cookie()->forget('XSRF-TOKEN', '/', config('session.domain'));
        return response()
            ->json(['message' => 'Logged out'])
            ->cookie($sessionCookie)
            ->cookie($xsrfCookie);
    }

    public function me()
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        // Cargar la relación socialNetwork si no está cargada
        $user->load('socialNetwork');
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name ?? 'user',
            'role_id' => $user->role_id,
            'last_seen' => $user->last_seen,
            'nickname' => $user->nickname,
            'social_network_id' => $user->social_network_id,
            'social_network' => $user->socialNetwork,
        ]);
    }
}
