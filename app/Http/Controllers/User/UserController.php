<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\User\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    protected function transformUserData($user): array
    {
        if (!$user) {
            return [];
        }

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role_name ?? 'user',
            'role_id' => (int) $user->role_id,
            'last_seen' => $user->last_seen,
            'photo_url' => $user->photo_url,
            'photo_status' => $user->photo_status,
            'account_status' => $user->account_status ?? null,
            'group_id' => $user->group_id ? (int) $user->group_id : null,
            'group_name' => $user->group_name ?? null,
            'group_img_url' => $user->group_img_url ?? null,
            'nickname' => $user->nickname ?? null,
            'birthdate' => $user->birthdate ?? null,
            'social_network_name' => $user->social_network_name ?? null,
            'social_network_id' => $user->social_network_id ? (int) $user->social_network_id : null,
            'social_network_logo_url' => $user->social_network_logo_url ?? null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'banned_at' => $user->banned_at ?? null,
            'ban_reason' => $user->ban_reason ?? null,
            'banned_by' => $user->banned_by ? (int) $user->banned_by : null,
            'photo_rejection_reason' => $user->photo_rejection_reason ?? null,
            'rejection_reason' => $user->rejection_reason ?? null,
            'country' => $user->country ?? null,
            'country_slug' => $user->country_slug ?? null,
        ];
    }



    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $fullUser = $this->userService->getUserById($user->id);
        if (!$fullUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'id' => (int) $fullUser->id,
            'name' => $fullUser->name,
            'email' => $fullUser->email,
            'role' => $fullUser->role_name ?? 'user',
            'role_id' => (int) $fullUser->role_id,
            'last_seen' => $fullUser->last_seen,
            'photo_url' => $fullUser->photo_url,
            'photo_status' => $fullUser->photo_status,
            'account_status' => $fullUser->account_status,
            'group_id' => $fullUser->group_id ? (int) $fullUser->group_id : null,
            'group_name' => $fullUser->group_name ?? null,
            'group_img_url' => $fullUser->group_img_url ?? null,
            'nickname' => $fullUser->nickname,
            'birthdate' => $fullUser->birthdate,
            'social_network_id' => $fullUser->social_network_id ? (int) $fullUser->social_network_id : null,
            'social_network_name' => $fullUser->social_network_name ?? null,
            'social_network_logo_url' => $fullUser->social_network_logo_url ?? null,
            'banned_at' => $fullUser->banned_at,
            'ban_reason' => $fullUser->ban_reason,
            'banned_by' => $fullUser->banned_by ? (int) $fullUser->banned_by : null,
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
            'country' => 'nullable|string|max:255',
            'country_slug' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'nickname', 'birthdate', 'photo_url', 'social_network_id', 'country', 'country_slug']);

        if (isset($data['social_network_id'])) {
            $data['social_network_id'] = (int) $data['social_network_id'];
        }

        $currentPhoto = $user->photo_url;
        if ($request->photo_url != $currentPhoto) {
            $data['photo_status'] = 'pending';
        }

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
        // $updatedUser is now a stdClass, no need to load()

        return response()->json([
            'id' => (int) $updatedUser->id,
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'role' => $updatedUser->role_name ?? 'user',
            'role_id' => (int) $updatedUser->role_id,
            'last_seen' => $updatedUser->last_seen,
            'photo_url' => $updatedUser->photo_url,
            'photo_status' => $updatedUser->photo_status,
            'account_status' => $updatedUser->account_status,
            'group_id' => $updatedUser->group_id ? (int) $updatedUser->group_id : null,
            'group_name' => $updatedUser->group_name ?? null,
            'group_img_url' => $updatedUser->group_img_url ?? null,
            'nickname' => $updatedUser->nickname,
            'birthdate' => $updatedUser->birthdate,
            'social_network_id' => $updatedUser->social_network_id ? (int) $updatedUser->social_network_id : null,
            'social_network_name' => $updatedUser->social_network_name,
            'social_network_logo_url' => $updatedUser->social_network_logo_url,
            'social_network' => $updatedUser->social_network_id ? [
                'id' => (int) $updatedUser->social_network_id,
                'name' => $updatedUser->social_network_name,
                'logo_url' => $updatedUser->social_network_logo_url,
            ] : null,
            'banned_at' => $updatedUser->banned_at,
            'ban_reason' => $updatedUser->ban_reason,
            'banned_by' => $updatedUser->banned_by ? (int) $updatedUser->banned_by : null,
        ]);
    }


    public function index(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 3])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = $this->userService->getAllUsers();
        $transformedUsers = $users->map(fn($user) => $this->transformUserData($user));
        return response()->json($transformedUsers);
    }

    public function getUsersByTab(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 3])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tab = (int) $request->input('tab', 1);
        $perPage = $request->input('per_page') ? (int) $request->input('per_page') : 10;
        $page = $request->input('page') ? (int) $request->input('page') : 1;
        $search = $request->input('search', null);

        $result = $this->userService->getUsersByTab($tab, $perPage, $page, $search);

        // Si es paginado, devolver estructura con meta
        if ($result instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $transformedItems = collect($result->items())->map(fn($user) => $this->transformUserData($user));
            return response()->json([
                'data' => $transformedItems,
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'from' => $result->firstItem(),
                'to' => $result->lastItem()
            ]);
        }

        // Si no es paginado, devolver array simple transformado
        $transformedResult = collect($result)->map(fn($user) => $this->transformUserData($user));
        return response()->json($transformedResult);
    }

    public function getCounters(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 3])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $counts = $this->userService->getUserCounts();

        $counters = [
            'activeUsers' => $counts->activeUsers,
            'pendingPhotos' => $counts->pendingPhotos,
            'rejectedUsers' => $counts->rejectedUsers,
            'pendingApproval' => $counts->pendingApproval,
        ];

        return response()->json($counters);
    }


    public function show(Request $request, $id)
    {
        $isAuthorized = in_array($request->user()->role_id, [1, 3]) || $request->user()->id == $id;

        if (!$isAuthorized) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($this->transformUserData($user));
    }



    // Métodos específicos para CEO
    public function approvePhoto(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 3])) {
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
        if (!in_array($request->user()->role_id, [1, 3])) {
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
        if (!in_array($request->user()->role_id, [1, 3])) {
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
        if (!in_array($request->user()->role_id, [1, 3])) {
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
        if (!in_array($request->user()->role_id, [1, 3])) {
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
        if (!in_array($request->user()->role_id, [1, 3])) {
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

        // 1: admin
        $isAuthorized = $request->user()->role_id === 1 || $request->user()->id == $id;

        if (!$isAuthorized) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);
        $validated['updated_at'] = now();


        $updated = $this->userService->updateUser($id, $validated);

        if (!$updated) {
            return response()->json(['message' => 'Error al actualizar usuario'], 400);
        }

        $user = $this->userService->getUserById($id);
        return response()->json($this->transformUserData($user));
    }

    public function destroyViaPost(Request $request)
    {
        // 1: admin
        if ((int) $request->user()->role_id !== 1) {
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
