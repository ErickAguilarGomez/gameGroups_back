<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AuthService;

class RegisterController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'birthdate' => 'required|date|before:today',
            'photo_url' => 'nullable|string|url',
            'social_network_id' => 'nullable|required_with:nickname|exists:social_networks,id',
            'nickname' => 'nullable|required_with:social_network_id|string|max:255',
        ], [
            'social_network_id.required_with' => 'Debes seleccionar una red social si proporcionas un nickname',
            'nickname.required_with' => 'Debes proporcionar un nickname si seleccionas una red social',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        if (isset($data['social_network_id'])) {
            $data['social_network_id'] = (int) $data['social_network_id'];
        }
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $user = $this->authService->register($data);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
        ], 201);
    }
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo_url' => 'required|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $updated = $this->authService->updatePhoto($user, $request->photo_url);

        if ($updated) {
            return response()->json([
                'message' => 'Foto actualizada. Pendiente de aprobación.',
            ]);
        }

        return response()->json([
            'message' => 'Error al actualizar la foto',
        ], 500);
    }
}
