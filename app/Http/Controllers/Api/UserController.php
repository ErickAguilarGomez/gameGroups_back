<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Cargar relaciones necesarias
        $user->load('role', 'socialNetwork');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name ?? 'user',
            'role_id' => $user->role_id,
            'last_seen' => $user->last_seen,
            'photo_url' => $user->photo_url,
            'photo_status' => $user->photo_status,
            'account_status' => $user->account_status,
            'group_id' => $user->group_id,
            'nickname' => $user->nickname,
            'birthdate' => $user->birthdate,
            'social_network_id' => $user->social_network_id,
            'social_network' => $user->socialNetwork,
            'banned_at' => $user->banned_at,
            'ban_reason' => $user->ban_reason,
            'banned_by' => $user->banned_by,
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo_url' => 'required|string|url',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $updated = $this->userService->updateUserPhoto($user->id, $request->photo_url);

        if (!$updated) {
            return response()->json(['message' => 'Error al actualizar foto'], 400);
        }

        return response()->json([
            'message' => 'Foto actualizada exitosamente',
            'photo_url' => $request->photo_url,
            'photo_status' => 'pending',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'photo_url' => 'nullable|string|url',
            'social_network_id' => 'nullable|exists:social_networks,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'nickname', 'birthdate', 'photo_url', 'social_network_id']);
        $data['photo_status'] = 'pending';

        // Si hay contraseña nueva, agregarla
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $updated = $this->userService->updateUser($user->id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Error al actualizar perfil'], 400);
        }

        // Obtener usuario actualizado con relaciones
        $updatedUser = $this->userService->getUserById($user->id);
        $updatedUser->load('role', 'socialNetwork');

        return response()->json([
            'id' => $updatedUser->id,
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'role' => $updatedUser->role->name ?? 'user',
            'role_id' => $updatedUser->role_id,
            'last_seen' => $updatedUser->last_seen,
            'photo_url' => $updatedUser->photo_url,
            'photo_status' => $updatedUser->photo_status,
            'account_status' => $updatedUser->account_status,
            'group_id' => $updatedUser->group_id,
            'nickname' => $updatedUser->nickname,
            'birthdate' => $updatedUser->birthdate,
            'social_network_id' => $updatedUser->social_network_id,
            'social_network' => $updatedUser->socialNetwork,
            'banned_at' => $updatedUser->banned_at,
            'ban_reason' => $updatedUser->ban_reason,
            'banned_by' => $updatedUser->banned_by,
        ]);
    }

   
    public function index(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }

    public function getUsersByTab(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tab = (int) $request->input('tab', 1);
        $perPage = $request->input('per_page') ? (int) $request->input('per_page') : 10;
        $page = $request->input('page') ? (int) $request->input('page') : 1;
        $search = $request->input('search', null);
        
        $result = $this->userService->getUsersByTab($tab, $perPage, $page, $search);
        
        // Si es paginado, devolver estructura con meta
        if ($result instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return response()->json([
                'data' => $result->items(),
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'from' => $result->firstItem(),
                'to' => $result->lastItem()
            ]);
        }
        
        // Si no es paginado, devolver array simple
        return response()->json($result);
    }

    public function getCounters(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $counters = [
            'activeUsers' => $this->userService->getUsersByTab(1, null, null, null)->count(),
            'pendingPhotos' => $this->userService->getUsersByTab(2, null, null, null)->count(),
            'rejectedUsers' => $this->userService->getUsersByTab(3, null, null, null)->count(),
            'pendingApproval' => $this->userService->getUsersByTab(4, null, null, null)->count(),
        ];

        return response()->json($counters);
    }

    public function connectedUsers(Request $request)
    {
        $minutes = (int) $request->get('minutes', 5);
        $users = $this->userService->getConnectedUsers($minutes);
        return response()->json($users);
    }


    public function show(Request $request, $id)
    {
        if (!$request->user()->isAdminOrAssistant() && $request->user()->id != $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user);
    }



    // Métodos específicos para CEO
    public function approvePhoto(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $updated = $this->userService->updateUser($request->user_id, [
            'photo_status' => 'approved',
            'photo_rejection_reason' => null
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Error al aprobar foto'], 400);
        }

        return response()->json(['message' => 'Foto aprobada exitosamente']);
    }

    public function rejectPhoto(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string'
        ]);

        $updated = $this->userService->updateUser($request->user_id, [
            'photo_status' => 'rejected',
            'photo_rejection_reason' => $request->reason
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Error al rechazar foto'], 400);
        }

        return response()->json(['message' => 'Foto rechazada exitosamente']);
    }

    public function approveAccount(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $updated = $this->userService->updateUser($request->user_id, [
            'account_status' => 'approved',
            'rejection_reason' => null
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Error al aprobar cuenta'], 400);
        }

        return response()->json(['message' => 'Cuenta aprobada exitosamente']);
    }

    public function rejectAccount(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string'
        ]);

        $updated = $this->userService->updateUser($request->user_id, [
            'account_status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Error al rechazar cuenta'], 400);
        }

        return response()->json(['message' => 'Cuenta rechazada exitosamente']);
    }

    public function approveWithPhoto(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $updated = $this->userService->updateUser($request->user_id, [
            'account_status' => 'approved',
            'photo_status' => 'approved',
            'rejection_reason' => null,
            'photo_rejection_reason' => null
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Error al aprobar usuario'], 400);
        }

        return response()->json(['message' => 'Usuario aprobado exitosamente con foto']);
    }

    public function approveWithoutPhoto(Request $request)
    {
        if (!$request->user()->isAdminOrAssistant()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = $this->userService->getUserById($request->user_id);
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $photoStatus = null;
        if ($user->photo_url && in_array($user->photo_status, ['pending', 'rejected'])) {
            $photoStatus = $user->photo_status;
        }

        $updated = $this->userService->updateUser($request->user_id, [
            'account_status' => 'approved',
            'photo_status' => $photoStatus,
            'rejection_reason' => null,
            'photo_rejection_reason' => ($photoStatus === 'rejected') ? $user->photo_rejection_reason : null,
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Error al aprobar usuario'], 400);
        }

        return response()->json(['message' => 'Usuario aprobado exitosamente sin foto']);
    }

    public function updateViaPost(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $id = $request->user_id;

        if (!$request->user()->isAdmin() && $request->user()->id != $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        $updated = $this->userService->updateUser($id, $validated);

        if (!$updated) {
            return response()->json(['message' => 'Error al actualizar usuario'], 400);
        }

        $user = $this->userService->getUserById($id);
        return response()->json($user);
    }

    public function destroyViaPost(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $deleted = $this->userService->deleteUser($request->user_id);
        
        if (!$deleted) {
            return response()->json(['message' => 'Error al eliminar usuario'], 400);
        }
        
        return response()->json(['message' => 'User deleted']);
    }
}
