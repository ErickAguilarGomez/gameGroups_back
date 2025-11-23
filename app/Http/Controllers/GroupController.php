<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupService;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    protected function transformGroupData($group): array
    {
        if (!$group) {
            return [];
        }

        $transformed = [
            'id' => (int) $group->id,
            'name' => $group->name,
            'group_img_url' => $group->group_img_url ?? null,
        ];

        if (isset($group->users)) {
            $transformed['users'] = collect($group->users)->map(fn($user) => $this->transformUserInGroup($user))->toArray();
            $transformed['users_count'] = (int) ($group->users_count ?? count($transformed['users']));
        }

        return $transformed;
    }

    protected function transformUserInGroup($user): array
    {
        if (!$user) {
            return [];
        }

        return [
            'id' => (int) $user->id,
            'name' => $user->name ?? null,
            'photo_url' => $user->photo_url ?? null,
            'photo_status' => $user->photo_status ?? null,
            'ban_reason' => $user->ban_reason ?? null,
            'banned_at' => $user->banned_at ?? null,
        ];
    }

    protected function transformUserDetail($user): array
    {
        if (!$user) {
            return [];
        }

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname ?? null,
            'email' => $user->email,
            'birthdate' => $user->birthdate,
            'photo_url' => $user->photo_url ?? null,
            'photo_status' => $user->photo_status ?? null,
            'group_id' => $user->group_id ? (int) $user->group_id : null,
            'banned_at' => $user->banned_at ?? null,
            'ban_reason' => $user->ban_reason ?? null,
            'banned_by' => $user->banned_by ? (int) $user->banned_by : null,
            'created_at' => $user->created_at ?? null,
            'updated_at' => $user->updated_at ?? null,
            'country' => $user->country ?? null,
            'country_slug' => $user->country_slug ?? null,
            'social_network_name' => $user->social_network_name ?? null,
            'logo_url' => $user->logo_url ?? null,
        ];
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'group_img_url']);
        return $this->groupService->createGroup($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'group_img_url']);
        return $this->groupService->updateGroup($id, $data);
    }

    public function destroy($id)
    {
        return $this->groupService->deleteGroup($id);
    }

    public function assignUser(Request $request, $groupId)
    {
        $userId = $request->input('user_id');
        return $this->groupService->assignUserToGroup($userId, $groupId);
    }

    public function removeUser(Request $request, $groupId)
    {
        $userId = $request->input('user_id');
        $banReason = $request->input('ban_reason');
        $bannedBy = auth()->id();
        return $this->groupService->removeUserFromGroup(
            $userId,
            $banReason,
            $bannedBy
        );
    }

    public function unbanUser(Request $request)
    {
        $userId = $request->input('user_id');
        return $this->groupService->unbanUser($userId);
    }

    public function groups()
    {
        $groups = $this->groupService->groups();

        $transformed = [
            'users_without_group' => collect($groups['users_without_group'] ?? [])->map(fn($user) => $this->transformUserInGroup($user))->toArray(),
            'users_without_group_count' => (int) ($groups['users_without_group_count'] ?? 0),
            'users_banned' => collect($groups['users_banned'] ?? [])->map(fn($user) => $this->transformUserInGroup($user))->toArray(),
            'users_banned_count' => (int) ($groups['users_banned_count'] ?? 0),
            'groups_with_users' => collect($groups['groups_with_users'] ?? [])->map(fn($group) => $this->transformGroupData($group))->toArray(),
        ];

        return response()->json($transformed);
    }

    public function userDetail($id)
    {
        $user = $this->groupService->userDetail($id);
        return response()->json($this->transformUserDetail($user));
    }
}
