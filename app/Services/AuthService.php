<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Autenticar usuario
     */
    public function login(string $email, string $password): ?User
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            $user->load(['role', 'socialNetwork']);
            
            return $user;
        }

        return null;
    }

    /**
     * Verificar si el usuario está aprobado
     */
    public function isUserApproved(User $user): bool
    {
        return $user->account_status === 'approved';
    }

    /**
     * Obtener mensaje de rechazo
     */
    public function getRejectionMessage(User $user): string
    {
        if ($user->account_status === 'pending') {
            return 'Tu cuenta está pendiente de aprobación por un administrador';
        }

        return 'Tu cuenta ha sido rechazada. Razón: ' . ($user->rejection_reason ?? 'No especificado');
    }

    /**
     * Registrar nuevo usuario
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 2; // Usuario normal
        $data['photo_status'] = isset($data['photo_url']) ? 'pending' : null;

        return $this->userRepository->create($data);
    }

    /**
     * Cerrar sesión
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Obtener usuario autenticado
     */
    public function getCurrentUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if ($user) {
            $user->load('role');
        }
        
        return $user;
    }

    /**
     * Actualizar foto de perfil
     */
    public function updatePhoto(User $user, string $photoUrl): bool
    {
        return $this->userRepository->update($user->id, [
            'photo_url' => $photoUrl,
            'photo_status' => 'pending',
        ]);
    }
}
