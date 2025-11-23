<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Collection;

class GroupService
{
    protected $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }


    public function createGroup($data)
    {
        try {
            $result = $this->groupRepository->create($data);
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el grupo',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateGroup($id, $data)
    {
        try {
            $result = $this->groupRepository->update($id, $data);
            return response()->json([
                'message' => 'Grupo actualizado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el grupo',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteGroup($id)
    {
        try {
            $result = $this->groupRepository->delete($id);
            return response()->json([
                'message' => 'Grupo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el grupo',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function assignUserToGroup($userId, $groupId)
    {
        try {
            $result = $this->groupRepository->assignUser($userId, $groupId);
            return response()->json([
                'message' => 'Usuario asignado al grupo exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al asignar usuario al grupo',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function removeUserFromGroup($userId, $banReason, $bannedBy)
    {
        try {
            $result = $this->groupRepository->removeUser($userId, $banReason, $bannedBy);
            return response()->json([
                'message' => 'Usuario baneado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al banear usuario',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function unbanUser($userId)
    {
        try {
            $result = $this->groupRepository->unbanUser($userId);
            return response()->json([
                'message' => 'Usuario desbaneado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al desbanear usuario',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function groups()
    {
        try {
            return $this->groupRepository->groups();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los grupos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function userDetail($id)
    {
        try {
            $result = $this->groupRepository->userDetail($id);
            return $result;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener Detalle del usuario',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
