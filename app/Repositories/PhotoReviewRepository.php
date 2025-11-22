<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PhotoReviewRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener usuarios con fotos pendientes
     */
    public function getPendingPhotos(): Collection
    {
        return $this->model->where('photo_status', 'pending')
            ->whereNotNull('photo_url')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Obtener usuarios con fotos aprobadas
     */
    public function getApprovedPhotos(): Collection
    {
        return $this->model->where('photo_status', 'approved')
            ->whereNotNull('photo_url')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Obtener usuarios con fotos rechazadas
     */
    public function getRejectedPhotos(): Collection
    {
        return $this->model->where('photo_status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Aprobar foto
     */
    public function approvePhoto(int $userId): bool
    {
        $user = $this->model->find($userId);
        
        if (!$user) {
            return false;
        }

        return $user->update(['photo_status' => 'approved']);
    }

    /**
     * Rechazar foto
     */
    public function rejectPhoto(int $userId, ?string $reason = null): bool
    {
        $user = $this->model->find($userId);
        
        if (!$user) {
            return false;
        }

        return $user->update([
            'photo_status' => 'rejected',
            'photo_url' => null,
            'rejection_reason' => $reason,
        ]);
    }
}
