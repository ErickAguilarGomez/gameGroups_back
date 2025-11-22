<?php

namespace App\Services;

use App\Repositories\RegistrationReviewRepository;
use Illuminate\Database\Eloquent\Collection;

class RegistrationReviewService
{
    protected $registrationReviewRepository;

    public function __construct(RegistrationReviewRepository $registrationReviewRepository)
    {
        $this->registrationReviewRepository = $registrationReviewRepository;
    }

    /**
     * Obtener usuarios pendientes de aprobación
     */
    public function getPendingRegistrations(): Collection
    {
        return $this->registrationReviewRepository->getPendingRegistrations();
    }

    /**
     * Aprobar usuario (cuenta y foto si existe)
     */
    public function approveRegistration(int $userId, bool $approvePhoto = true): bool
    {
        return $this->registrationReviewRepository->approveRegistration($userId, $approvePhoto);
    }

    /**
     * Rechazar usuario
     */
    public function rejectRegistration(int $userId, string $reason, bool $rejectPhoto = true): bool
    {
        return $this->registrationReviewRepository->rejectRegistration($userId, $reason, $rejectPhoto);
    }

    /**
     * Obtener registros aprobados
     */
    public function getApprovedRegistrations(): Collection
    {
        return $this->registrationReviewRepository->getApprovedRegistrations();
    }

    /**
     * Obtener registros rechazados
     */
    public function getRejectedRegistrations(): Collection
    {
        return $this->registrationReviewRepository->getRejectedRegistrations();
    }
}
