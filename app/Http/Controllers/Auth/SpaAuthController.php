<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use Illuminate\Support\Facades\DB;

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

        $user_data = DB::table('users as u')
            ->where('u.id', $user->id)
            ->select(
                'u.id',
                'u.email',
                'u.name',
                'u.nickname',
                'u.birthdate',
                'u.photo_rejection_reason',
                'u.account_status',
                'u.rejection_reason',
                'u.role_id',
                'g.id as group_id',
                'g.name as group_name',
                'g.group_img_url',
                'u.ban_reason',
                'u.banned_by',
                'u.last_seen',
                'sn.id as social_network_id',
                'sn.name as social_network_name',
                'sn.logo_url as social_network_logo_url',
                'u.photo_url',
                'u.photo_status',
                'u.banned_at',
                'u.country',
                'u.country_slug'
            )
            ->leftJoin('social_networks as sn', 'u.social_network_id', '=', 'sn.id')
            ->leftJoin('groups as g', 'u.group_id', '=', 'g.id')
            ->join('roles as r', 'u.role_id', '=', 'r.id')
            ->first();

        $request->session()->regenerate();
        return response()->json($user_data);
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
        $get_user_data = DB::table('users as u')->where('u.id', $user->id)
            ->select(
                'u.id',
                'u.email',
                'u.name',
                'u.nickname',
                'u.birthdate',
                'u.photo_rejection_reason',
                'u.account_status',
                'u.rejection_reason',
                'u.role_id',
                'g.id as group_id',
                'g.name as group_name',
                'g.img_url as group_img_url',
                'u.ban_reason',
                'u.banned_by',
                'u.last_seen',
                'sn.id as social_network_id',
                'sn.name as social_network_name',
                'sn.logo_url as social_network_logo_url',
                'u.photo_url',
                'u.photo_status',
                'u.banned_at',
                'u.country',
                'u.country_slug'
            )
            ->leftJoin('social_networks as sn', 'u.social_network_id', '=', 'sn.id')
            ->leftJoin('groups as g', 'u.group_id', '=', 'g.id')
            ->join('roles as r', 'u.role_id', '=', 'r.id')
            ->first();

        return response()->json($get_user_data);
    }
}
