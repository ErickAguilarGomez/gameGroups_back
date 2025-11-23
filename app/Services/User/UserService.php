<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    /**
     * Obtener usuario por ID
     */
    public function getUserById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Crear nuevo usuario
     */
    public function createUser(array $data)
    {
        // Hashear password si existe
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Asignar rol por defecto si no se especifica
        if (!isset($data['role_id'])) {
            $data['role_id'] = 2; // user
        }

        return $this->userRepository->create($data);
    }

    /**
     * Actualizar usuario
     */
    public function updateUser(int $id, array $data): bool
    {
        // Hashear password si se está actualizando
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }


    /**
     * Actualizar última conexión
     */
    public function updateLastSeen(int $id): bool
    {
        return $this->userRepository->updateLastSeen($id);
    }

    /**
     * Obtener usuarios pendientes de aprobación
     */
    public function getPendingUsers(): Collection
    {
        return $this->userRepository->getByAccountStatus('pending');
    }

    /**
     * Aprobar cuenta de usuario
     */
    public function approveUser(int $id): bool
    {
        return $this->userRepository->approveAccount($id);
    }

    /**
     * Rechazar cuenta de usuario
     */
    public function rejectUser(int $id, string $reason): bool
    {
        return $this->userRepository->rejectAccount($id, $reason);
    }

    /**
     * Verificar si el usuario puede realizar una acción
     */
    public function canPerformAction($currentUser, int $targetUserId, string $action): bool
    {
        // Admin puede hacer todo
        if ($currentUser->role_id === 1 || $currentUser->role_id === 3) { // 1: admin, 3: assistant
            return true;
        }

        // Usuario solo puede modificar sus propios datos
        if ($action === 'update' || $action === 'view') {
            return $currentUser->id === $targetUserId;
        }

        return false;
    }

    /**
     * Buscar usuario por email
     */
    public function getUserByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Actualizar foto de usuario
     */
    public function updateUserPhoto(int $id, string $photoUrl): bool
    {
        return $this->userRepository->update($id, [
            'photo_url' => $photoUrl,
            'photo_status' => 'pending',
        ]);
    }

    public function getUsersByTab(int $tab, ?int $perPage = null, ?int $page = null, ?string $search = null)
    {
        return $this->userRepository->getUsersByTab($tab, $perPage, $page, $search);
    }

    public function getUserCounts()
    {
        return $this->userRepository->getUserCounts();
    }
}
