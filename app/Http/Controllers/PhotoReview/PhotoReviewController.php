<?php

namespace App\Http\Controllers\PhotoReview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PhotoReview\PhotoReviewService;

class PhotoReviewController extends Controller
{
    protected $photoReviewService;

    public function __construct(PhotoReviewService $photoReviewService)
    {
        $this->photoReviewService = $photoReviewService;
    }


    public function pending()
    {
        $users = $this->photoReviewService->getPendingPhotos();
        return response()->json($users);
    }

    public function approve(Request $request, $userId)
    {
        $approved = $this->photoReviewService->approvePhoto($userId);

        if (!$approved) {
            return response()->json([
                'message' => 'No se pudo aprobar la foto'
            ], 400);
        }

        return response()->json([
            'message' => 'Foto aprobada exitosamente'
        ]);
    }


    public function reject(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $rejected = $this->photoReviewService->rejectPhoto($userId, $request->reason);

        if (!$rejected) {
            return response()->json([
                'message' => 'No se pudo rechazar la foto'
            ], 400);
        }

        return response()->json([
            'message' => 'Foto rechazada',
            'reason' => $request->reason
        ]);
    }


    public function stats()
    {
        $pending = $this->photoReviewService->getPendingPhotos()->count();
        $approved = $this->photoReviewService->getApprovedPhotos()->count();
        $rejected = $this->photoReviewService->getRejectedPhotos()->count();

        $stats = [
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ];

        return response()->json($stats);
    }
}
