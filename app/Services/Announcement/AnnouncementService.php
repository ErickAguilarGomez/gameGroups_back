<?php

namespace App\Services\Announcement;

use App\Repositories\Announcement\AnnouncementRepository;
use Exception;

class AnnouncementService
{
    protected $announcementRepository;

    public function __construct()
    {
        $this->announcementRepository = new AnnouncementRepository();
    }


    public function index($request)
    {
        try {
            $announcement = $this->announcementRepository->index($request);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function store($request)
    {
        try {
            $announcement = $this->announcementRepository->store($request);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function update($request, $id)
    {
        try {
            $announcement = $this->announcementRepository->update($request, $id);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function destroy($id)
    {
        try {
            $announcement = $this->announcementRepository->destroy($id);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
}
