<?php

namespace App\Http\Controllers\Announcement;

use App\Http\Controllers\Controller;
use App\Services\Announcement\AnnouncementService;
use Illuminate\Http\Request;
use Exception;

class AnnouncementController extends Controller
{
    protected $announcementService;
    public function __construct()
    {
        $this->announcementService = new AnnouncementService();
    }
    public function index(Request $request)
    {
        $announcement = $this->announcementService->index($request);
        return $announcement;
    }

    public function store(Request $request)
    {
        $announcement = $this->announcementService->store($request);
        return $announcement;
    }

    public function update(Request $request, $id)
    {
        $announcement = $this->announcementService->update($request, $id);
        return $announcement;
    }

    public function destroy($id)
    {
        $announcement = $this->announcementService->destroy($id);
        return $announcement;
    }
}
