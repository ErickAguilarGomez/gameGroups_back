<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('role', 'group', 'socialNetwork')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?User
    {
        return $this->model->with('role', 'group', 'socialNetwork')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->with('role', 'socialNetwork')->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function getConnectedUsers(int $minutes = 5): Collection
    {
        $since = now()->subMinutes($minutes);

        return $this->model->with('role', 'socialNetwork')
            ->whereNotNull('last_seen')
            ->where('last_seen', '>=', $since)
            ->orderBy('last_seen', 'desc')
            ->get();
    }

    public function updateLastSeen(int $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->update(['last_seen' => now()]);
    }

    public function getByAccountStatus(string $status): Collection
    {
        return $this->model->with('role', 'socialNetwork')
            ->where('account_status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function approveAccount(int $id): bool
    {
        return $this->update($id, [
            'account_status' => 'approved',
            'rejection_reason' => null,
        ]);
    }

    public function rejectAccount(int $id, string $reason): bool
    {
        return $this->update($id, [
            'account_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function getUsersByTab(int $tab, ?int $perPage = null, ?int $page = null, ?string $search = null)
    {
        $query = $this->model->with('role', 'group', 'socialNetwork')
            ->whereNotIn('role_id', [1, 3]) // Excluir admin (1) y assistant (3)
            ->orderBy('created_at', 'desc');

        // Aplicar filtro de búsqueda si existe
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Aplicar filtro según tab
        switch ($tab) {
            case 2:
                $query->where('account_status', 'approved')
                      ->where('photo_status', 'pending');
                break;

            case 3:
                $query->where('account_status', 'rejected');
                break;

            case 4:
                $query->where('account_status', 'pending');
                break;

            default:
                $query->where('account_status', 'approved');
                break;
        }

        $perPage = $perPage ?? 10;
        $page = $page ?? 1;
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
