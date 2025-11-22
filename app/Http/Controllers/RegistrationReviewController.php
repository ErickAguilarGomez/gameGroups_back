<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RegistrationReviewService;

class RegistrationReviewController extends Controller
{
    protected $registrationReviewService;

    public function __construct(RegistrationReviewService $registrationReviewService)
    {
        $this->registrationReviewService = $registrationReviewService;
    }

    /**
     * Obtener solicitudes de registro pendientes
     */
    public function pending()
    {
        $users = $this->registrationReviewService->getPendingRegistrations();
        return response()->json($users);
    }

    /**
     * Aprobar registro de usuario
     */
    public function approve(Request $request, $userId)
    {
        $photoDecision = $request->input('photo_decision', 'approve');
        $approvePhoto = $photoDecision === 'approve';
        
        $approved = $this->registrationReviewService->approveRegistration($userId, $approvePhoto);

        if (!$approved) {
            return response()->json([
                'message' => 'No se pudo aprobar el registro'
            ], 400);
        }

        return response()->json([
            'message' => 'Usuario aprobado exitosamente',
        ]);
    }

    /**
     * Rechazar registro de usuario
     */
    public function reject(Request $request, $userId)
    {
        $reason = $request->input('reason', 'No especificado');
        
        $rejected = $this->registrationReviewService->rejectRegistration($userId, $reason);

        if (!$rejected) {
            return response()->json([
                'message' => 'No se pudo rechazar el registro'
            ], 400);
        }

        return response()->json([
            'message' => 'Solicitud de registro rechazada',
        ]);
    }

    /**
     * Estadísticas de registros
     */
    public function stats()
    {
        $pending = $this->registrationReviewService->getPendingRegistrations()->count();
        $approved = $this->registrationReviewService->getApprovedRegistrations()->count();
        $rejected = $this->registrationReviewService->getRejectedRegistrations()->count();

        return response()->json([
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ]);
    }
}
