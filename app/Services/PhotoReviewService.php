<?php

namespace App\Services;

use App\Repositories\PhotoReviewRepository;
use Illuminate\Database\Eloquent\Collection;

class PhotoReviewService
{
    protected $photoReviewRepository;

    public function __construct(PhotoReviewRepository $photoReviewRepository)
    {
        $this->photoReviewRepository = $photoReviewRepository;
    }

    /**
     * Obtener usuarios con fotos pendientes de revisión
     */
    public function getPendingPhotos(): Collection
    {
        return $this->photoReviewRepository->getPendingPhotos();
    }

    /**
     * Aprobar foto de usuario
     */
    public function approvePhoto(int $userId): bool
    {
        return $this->photoReviewRepository->approvePhoto($userId);
    }

    /**
     * Rechazar foto de usuario
     */
    public function rejectPhoto(int $userId, string $reason = null): bool
    {
        return $this->photoReviewRepository->rejectPhoto($userId, $reason);
    }

    /**
     * Obtener fotos aprobadas
     */
    public function getApprovedPhotos(): Collection
    {
        return $this->photoReviewRepository->getApprovedPhotos();
    }

    /**
     * Obtener fotos rechazadas
     */
    public function getRejectedPhotos(): Collection
    {
        return $this->photoReviewRepository->getRejectedPhotos();
    }
}
