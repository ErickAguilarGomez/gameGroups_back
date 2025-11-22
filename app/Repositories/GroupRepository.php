<?php

namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository
{
    protected $model;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener todos los grupos
     */
    public function all(): Collection
    {
        return $this->model->with('users')->get();
    }

    /**
     * Buscar grupo por ID
     */
    public function findById(int $id): ?Group
    {
        return $this->model->with('users')->find($id);
    }

    /**
     * Crear nuevo grupo
     */
    public function create(array $data): Group
    {
        return $this->model->create($data);
    }

    /**
     * Actualizar grupo
     */
    public function update(int $id, array $data): bool
    {
        $group = $this->findById($id);
        
        if (!$group) {
            return false;
        }

        return $group->update($data);
    }

    /**
     * Eliminar grupo
     */
    public function delete(int $id): bool
    {
        $group = $this->findById($id);
        
        if (!$group) {
            return false;
        }

        return $group->delete();
    }

    /**
     * Obtener usuarios de un grupo
     */
    public function getUsersByGroup(int $groupId): Collection
    {
        $group = $this->findById($groupId);
        
        if (!$group) {
            return collect([]);
        }

        return $group->users;
    }

    /**
     * Asignar usuario a un grupo
     */
    public function assignUser(int $userId, int $groupId): bool
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return false;
        }

        $user->group_id = $groupId;
        return $user->save();
    }

    /**
     * Remover usuario de un grupo (banearlo)
     */
    public function removeUser(int $userId, ?string $banReason = null, ?int $bannedBy = null): bool
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return false;
        }

        $user->group_id = null;
        $user->banned_at = now();
        $user->ban_reason = $banReason;
        $user->banned_by = $bannedBy;
        
        return $user->save();
    }

    /**
     * Desbanear usuario (vuelve a sin grupo)
     */
    public function unbanUser(int $userId): bool
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return false;
        }

        $user->banned_at = null;
        $user->ban_reason = null;
        $user->banned_by = null;
        
        return $user->save();
    }

    /**
     * Obtener usuarios baneados
     */
    public function getBannedUsers(): Collection
    {
        return \App\Models\User::whereNotNull('banned_at')
            ->where('account_status', 'approved')
            ->whereNotIn('role_id', [1, 3])
            ->whereNull('deleted_at')
            ->with(['socialNetwork', 'bannedBy'])
            ->get();
    }

    public function getUsersWithoutGroup(): Collection
    {
        return \App\Models\User::whereNull('group_id')
            ->whereNull('banned_at')
            ->where('account_status', 'approved')
            ->whereNotIn('role_id', [1, 3])
            ->whereNull('deleted_at')
            ->with('socialNetwork')
            ->get();
    }
}
