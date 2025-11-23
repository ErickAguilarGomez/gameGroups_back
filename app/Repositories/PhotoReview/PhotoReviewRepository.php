<?php

namespace App\Repositories\PhotoReview;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PhotoReviewRepository
{
    /**
     * Obtener usuarios con fotos pendientes
     */
    public function getPendingPhotos(): Collection
    {
        return DB::table('users')
            ->where('photo_status', 'pending')
            ->whereNotNull('photo_url')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Obtener usuarios con fotos aprobadas
     */
    public function getApprovedPhotos(): Collection
    {
        return DB::table('users')
            ->where('photo_status', 'approved')
            ->whereNotNull('photo_url')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Obtener usuarios con fotos rechazadas
     */
    public function getRejectedPhotos(): Collection
    {
        return DB::table('users')
            ->where('photo_status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Aprobar foto
     */
    public function approvePhoto(int $userId): bool
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update(['photo_status' => 'approved']) > 0;
    }

    /**
     * Rechazar foto
     */
    public function rejectPhoto(int $userId, ?string $reason = null): bool
    {
        return DB::table('users')
            ->where('id', $userId)
            ->update([
                'photo_status' => 'rejected',
                'photo_url' => null,
                'rejection_reason' => $reason,
            ]) > 0;
    }
}
