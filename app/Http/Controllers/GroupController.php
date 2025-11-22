<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupService;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index()
    {
        $groups = $this->groupService->getAllGroups();
        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:groups,name',
            'group_img_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $group = $this->groupService->createGroup($request->only(['name', 'group_img_url']));

        return response()->json([
            'message' => 'Grupo creado exitosamente',
            'group' => $group
        ], 201);
    }

    public function show($id)
    {
        $group = $this->groupService->getGroupById($id);
        
        if (!$group) {
            return response()->json(['message' => 'Grupo no encontrado'], 404);
        }

        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:groups,name,' . $id,
            'group_img_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $updated = $this->groupService->updateGroup($id, $request->only(['name', 'group_img_url']));

        if (!$updated) {
            return response()->json(['message' => 'Error al actualizar grupo'], 400);
        }

        $group = $this->groupService->getGroupById($id);

        return response()->json([
            'message' => 'Grupo actualizado exitosamente',
            'group' => $group
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->groupService->deleteGroup($id);
        
        if (!$deleted) {
            return response()->json(['message' => 'Error al eliminar grupo'], 400);
        }

        return response()->json([
            'message' => 'Grupo eliminado exitosamente'
        ]);
    }

    public function assignUser(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $assigned = $this->groupService->assignUserToGroup($request->user_id, $groupId);

        if (!$assigned) {
            return response()->json(['message' => 'Error al asignar usuario'], 400);
        }

        return response()->json([
            'message' => 'Usuario asignado al grupo exitosamente'
        ]);
    }

    public function removeUser(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'ban_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $bannedBy = auth()->id(); // ID del usuario autenticado (admin o assistant)
        
        $removed = $this->groupService->removeUserFromGroup(
            $request->user_id, 
            $request->ban_reason,
            $bannedBy
        );

        if (!$removed) {
            return response()->json(['message' => 'Error al banear usuario'], 400);
        }

        return response()->json([
            'message' => 'Usuario baneado exitosamente'
        ]);
    }

    public function unbanUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $unbanned = $this->groupService->unbanUser($request->user_id);

        if (!$unbanned) {
            return response()->json(['message' => 'Error al desbanear usuario'], 400);
        }

        return response()->json([
            'message' => 'Usuario desbaneado exitosamente'
        ]);
    }

    public function bannedUsers()
    {
        $users = $this->groupService->getBannedUsers();
        return response()->json($users);
    }

    public function usersWithoutGroup()
    {
        $users = $this->groupService->getUsersWithoutGroup();
        return response()->json($users);
    }
}
