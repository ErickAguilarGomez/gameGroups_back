<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Group;

class GroupService
{
    protected $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Obtener todos los grupos
     */
    public function getAllGroups(): Collection
    {
        return $this->groupRepository->all();
    }

    /**
     * Obtener grupo por ID
     */
    public function getGroupById(int $id): ?Group
    {
        return $this->groupRepository->findById($id);
    }

    /**
     * Crear nuevo grupo
     */
    public function createGroup(array $data): Group
    {
        return $this->groupRepository->create($data);
    }

    /**
     * Actualizar grupo
     */
    public function updateGroup(int $id, array $data): bool
    {
        return $this->groupRepository->update($id, $data);
    }

    /**
     * Eliminar grupo
     */
    public function deleteGroup(int $id): bool
    {
        return $this->groupRepository->delete($id);
    }

    /**
     * Obtener usuarios de un grupo
     */
    public function getGroupUsers(int $groupId): Collection
    {
        return $this->groupRepository->getUsersByGroup($groupId);
    }

    /**
     * Asignar usuario a un grupo
     */
    public function assignUserToGroup(int $userId, int $groupId): bool
    {
        return $this->groupRepository->assignUser($userId, $groupId);
    }

    /**
     * Remover usuario de un grupo (banearlo)
     */
    public function removeUserFromGroup(int $userId, ?string $banReason = null, ?int $bannedBy = null): bool
    {
        return $this->groupRepository->removeUser($userId, $banReason, $bannedBy);
    }

    /**
     * Desbanear usuario
     */
    public function unbanUser(int $userId): bool
    {
        return $this->groupRepository->unbanUser($userId);
    }

    /**
     * Obtener usuarios baneados
     */
    public function getBannedUsers(): Collection
    {
        return $this->groupRepository->getBannedUsers();
    }

    /**
     * Obtener usuarios sin grupo
     */
    public function getUsersWithoutGroup(): Collection
    {
        return $this->groupRepository->getUsersWithoutGroup();
    }
}
