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
        $announcement = $this->announcementRepository->index($request);
        return $announcement;
    }
    public function store($request)
    {
        $announcement = $this->announcementRepository->store($request);
        return $announcement;
    }
    public function update($request)
    {
        $announcement = $this->announcementRepository->update($request);
        return $announcement;
    }
    public function destroy($id)
    {
        $announcement = $this->announcementRepository->destroy($id);
        return $announcement;
    }
    public function show($id)
    {
        $announcement = $this->announcementRepository->show($id);
        return $announcement;
    }
}
