<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RegistrationReviewRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener usuarios pendientes de aprobación
     */
    public function getPendingRegistrations(): Collection
    {
        return $this->model->where('account_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Obtener usuarios aprobados
     */
    public function getApprovedRegistrations(): Collection
    {
        return $this->model->where('account_status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener usuarios rechazados
     */
    public function getRejectedRegistrations(): Collection
    {
        return $this->model->where('account_status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Aprobar registro
     */
    public function approveRegistration(int $userId, bool $approvePhoto = true): bool
    {
        $user = $this->model->find($userId);
        
        if (!$user) {
            return false;
        }

        $data = [
            'account_status' => 'approved',
            'rejection_reason' => null,
        ];

        if ($approvePhoto) {
            $data['photo_status'] = 'approved';
        }

        return $user->update($data);
    }

    /**
     * Rechazar registro
     */
    public function rejectRegistration(int $userId, string $reason, bool $rejectPhoto = true): bool
    {
        $user = $this->model->find($userId);
        
        if (!$user) {
            return false;
        }

        $data = [
            'account_status' => 'rejected',
            'rejection_reason' => $reason,
        ];

        if ($rejectPhoto) {
            $data['photo_status'] = 'rejected';
            $data['photo_url'] = null;
        }

        return $user->update($data);
    }
}
