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
        return response()->json($groups);
    }

    public function userDetail($id)
    {
        $user = $this->groupService->userDetail($id);
        return response()->json($user);
    }
}
