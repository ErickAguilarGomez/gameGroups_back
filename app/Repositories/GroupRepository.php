<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class GroupRepository
{

    public function create(array $data)
    {
        return DB::table('groups')->insertGetId($data);
    }

    public function update(int $id, array $data): bool
    {
        return DB::table('groups')->where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        $users = DB::table('users')->where('group_id', $id)->update(['group_id' => null]);
        return DB::table('groups')->where('id', $id)->delete() > 0;
    }


    public function assignUser(int $userId, int $groupId): bool
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update(['group_id' => $groupId]) > 0;
    }

    public function removeUser(int $userId, ?string $banReason = null, ?int $bannedBy = null): bool
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update([
                'group_id' => null,
                'banned_at' => now(),
                'ban_reason' => $banReason,
                'banned_by' => $bannedBy,
            ]) > 0;
    }

    public function unbanUser(int $userId): bool
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update([
                'banned_at' => null,
                'ban_reason' => null,
                'banned_by' => null,
            ]) > 0;
    }


    public function groups()
    {
        $selected_user_fields = [
            'u.id',
            'u.name',
            'u.photo_url',
            'u.photo_status'
        ];

        $users_without_group = DB::table('users as u')
            ->select($selected_user_fields)
            ->where('u.account_status', 'approved')
            ->whereNull('u.banned_at')
            ->whereNull('u.group_id')
            ->whereNotIn('u.role_id', [1, 3])
            ->whereNull('u.deleted_at')
            ->get();

        $users_banned = DB::table('users as u')
            ->select(array_merge(
                $selected_user_fields,
                ['u.ban_reason', 'u.banned_at']
            ))
            ->where('u.account_status', 'approved')
            ->whereNotNull('u.banned_at')
            ->whereNotIn('u.role_id', [1, 3])
            ->whereNull('u.deleted_at')
            ->get();

        $groups_with_users = DB::table('groups as g')
            ->select('g.id', 'g.name', 'g.group_img_url')
            ->whereNull('g.deleted_at')
            ->get()
            ->map(function ($group) use ($selected_user_fields) {

                $group->users = DB::table('users as u')
                    ->select($selected_user_fields)
                    ->where('u.group_id', $group->id)
                    ->where('u.account_status', 'approved')
                    ->whereNull('u.banned_at')
                    ->whereNotIn('u.role_id', [1, 3])
                    ->whereNull('u.deleted_at')
                    ->get();

                $group->users_count = $group->users->count();

                return $group;
            });

        return [
            'users_without_group' => $users_without_group,
            'users_without_group_count' => $users_without_group->count(),
            'users_banned' => $users_banned,
            'users_banned_count' => $users_banned->count(),
            'groups_with_users' => $groups_with_users,
        ];
    }

    public function userDetail($id)
    {
        return DB::table('users as u')
            ->select(
                'u.id',
                'u.name',
                'u.nickname',
                'u.email',
                'u.birthdate',
                'u.photo_url',
                'u.photo_status',
                'u.group_id',
                'u.banned_at',
                'u.ban_reason',
                'u.banned_by',
                'u.created_at',
                'u.updated_at',
                'u.country',
                'u.country_slug',
                'sn.name as social_network_name',
                'sn.logo_url'
            )
            ->leftJoin('social_networks as sn', 'u.social_network_id', '=', 'sn.id')
            ->where('u.id', $id)
            ->where('u.account_status', 'approved')
            ->whereNotIn('u.role_id', [1, 3])
            ->whereNull('u.deleted_at')
            ->first();
    }
}
