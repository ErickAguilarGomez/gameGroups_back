<?php

namespace App\Repositories\RegistrationReview;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RegistrationReviewRepository
{
    /**
     * Obtener usuarios pendientes de aprobación
     */
    public function getPendingRegistrations(): Collection
    {
        return DB::table('users')
            ->where('account_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Obtener usuarios aprobados
     */
    public function getApprovedRegistrations(): Collection
    {
        return DB::table('users')
            ->where('account_status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener usuarios rechazados
     */
    public function getRejectedRegistrations(): Collection
    {
        return DB::table('users')
            ->where('account_status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Aprobar registro
     */
    public function approveRegistration(int $userId, bool $approvePhoto = true): bool
    {
        $data = [
            'account_status' => 'approved',
            'rejection_reason' => null,
        ];

        if ($approvePhoto) {
            $data['photo_status'] = 'approved';
        }

        return DB::table('users')->where('id', $userId)->update($data) > 0;
    }

    /**
     * Rechazar registro
     */
    public function rejectRegistration(int $userId, string $reason, bool $rejectPhoto = true): bool
    {
        $data = [
            'account_status' => 'rejected',
            'rejection_reason' => $reason,
        ];

        if ($rejectPhoto) {
            $data['photo_status'] = 'rejected';
            $data['photo_url'] = null;
        }

        return DB::table('users')->where('id', $userId)->update($data) > 0;
    }
}
