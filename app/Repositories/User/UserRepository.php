<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function all(): Collection
    {
        return DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('groups', 'users.group_id', '=', 'groups.id')
            ->leftJoin('social_networks', 'users.social_network_id', '=', 'social_networks.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.nickname',
                'users.birthdate',
                'users.role_id',
                'users.group_id',
                'users.social_network_id',
                'users.photo_url',
                'users.photo_status',
                'users.photo_rejection_reason',
                'users.account_status',
                'users.rejection_reason',
                'users.banned_at',
                'users.ban_reason',
                'users.banned_by',
                'users.last_seen',
                'users.country',
                'users.country_slug',
                'users.deleted_at',
                'users.created_at',
                'users.updated_at',
                'users.country',
                'users.country_slug',
                'roles.name as role_name',
                'groups.name as group_name',
                'social_networks.name as social_network_name',
                'social_networks.logo_url as social_network_icon'
            )
            ->orderBy('users.created_at', 'desc')
            ->get();
    }

    public function findById(int $id)
    {
        return DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('groups', 'users.group_id', '=', 'groups.id')
            ->leftJoin('social_networks', 'users.social_network_id', '=', 'social_networks.id')
            ->select(
                'users.*',
                'roles.name as role_name',
                'groups.name as group_name',
                'social_networks.name as social_network_name',
                'social_networks.logo_url as social_network_icon'
            )
            ->where('users.id', $id)
            ->first();
    }

    public function findByEmail(string $email)
    {
        return DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('social_networks', 'users.social_network_id', '=', 'social_networks.id')
            ->select(
                'users.*',
                'roles.name as role_name',
                'social_networks.name as social_network_name',
                'social_networks.logo_url as social_network_icon'
            )
            ->where('users.email', $email)
            ->first();
    }

    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('users')->insertGetId($data);
        return $this->findById($id);
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = now();
        return DB::table('users')->where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return DB::table('users')->where('id', $id)->delete() > 0;
    }


    public function updateLastSeen(int $id): bool
    {
        return DB::table('users')->where('id', $id)->update(['last_seen' => now()]) > 0;
    }

    public function getByAccountStatus(string $status): Collection
    {
        return DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('social_networks', 'users.social_network_id', '=', 'social_networks.id')
            ->select(
                'users.*',
                'roles.name as role_name',
                'social_networks.name as social_network_name'
            )
            ->where('users.account_status', $status)
            ->orderBy('users.created_at', 'desc')
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
        $query = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('groups', 'users.group_id', '=', 'groups.id')
            ->leftJoin('social_networks', 'users.social_network_id', '=', 'social_networks.id')
            ->select(
                'users.*',
                'roles.name as role_name',
                'groups.name as group_name',
                'social_networks.name as social_network_name'
            )
            ->whereNotIn('users.role_id', [1, 3])
            ->orderBy('users.created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        switch ($tab) {
            case 2:
                $query->where('users.account_status', 'approved')
                    ->where('users.photo_status', 'pending');
                break;

            case 3:
                $query->where('users.account_status', 'rejected');
                break;

            case 4:
                $query->where('users.account_status', 'pending');
                break;

            default:
                $query->where('users.account_status', 'approved');
                break;
        }

        $perPage = $perPage ?? 10;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUserCounts()
    {
        return DB::table('users')
            ->whereNotIn('role_id', [1, 3])
            ->selectRaw('
                COUNT(CASE WHEN account_status = "approved" THEN 1 END) as activeUsers,
                COUNT(CASE WHEN account_status = "approved" AND photo_status = "pending" THEN 1 END) as pendingPhotos,
                COUNT(CASE WHEN account_status = "rejected" THEN 1 END) as rejectedUsers,
                COUNT(CASE WHEN account_status = "pending" THEN 1 END) as pendingApproval
            ')
            ->first();
    }
}
