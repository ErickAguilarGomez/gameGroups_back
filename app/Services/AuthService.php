<?php

namespace App\Services;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    public function login(string $email, string $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $genericUser = Auth::user();
            $fullUser = $this->userRepository->findById($genericUser->id);

            return $fullUser;
        }

        return null;
    }

    /**
     * Verificar si el usuario está aprobado
     */
    public function isUserApproved($user): bool
    {
        return $user->account_status === 'approved';
    }

    /**
     * Obtener mensaje de rechazo
     */
    public function getRejectionMessage($user): string
    {
        if ($user->account_status === 'pending') {
            return 'Tu cuenta está pendiente de aprobación por un administrador';
        }

        return 'Tu cuenta ha sido rechazada. Razón: ' . ($user->rejection_reason ?? 'No especificado');
    }

    /**
     * Registrar nuevo usuario
     */
    public function register(array $data)
    {
        unset($data['password_confirmation']);
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
    public function getCurrentUser()
    {
        $genericUser = Auth::user();

        if ($genericUser) {
            // Get complete user data from repository
            return $this->userRepository->findById($genericUser->id);
        }

        return null;
    }

    /**
     * Actualizar foto de perfil
     */
    public function updatePhoto($user, string $photoUrl): bool
    {
        return $this->userRepository->update($user->id, [
            'photo_url' => $photoUrl,
            'photo_status' => 'pending',
        ]);
    }
}
