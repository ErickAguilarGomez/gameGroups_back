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

    protected function transformAnnouncementData($announcement): array
    {
        if (!$announcement) {
            return [];
        }

        return [
            'id' => isset($announcement->id) ? (int) $announcement->id : null,
            'title' => $announcement->title ?? null,
            'description' => $announcement->description ?? null,
            'url' => $announcement->url ?? null,
            'user_id' => isset($announcement->user_id) ? (int) $announcement->user_id : null,
            'start_date' => $announcement->start_date ?? null,
            'end_date' => $announcement->end_date ?? null,
            'is_video' => isset($announcement->is_video) ? (int) $announcement->is_video : 0,
            'created_at' => $announcement->created_at ?? null,
            'updated_at' => $announcement->updated_at ?? null,
            'deleted_at' => $announcement->deleted_at ?? null,
        ];
    }
    public function index(Request $request)
    {
        try {
            $announcements = $this->announcementService->index($request);
            if ($announcements instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $transformedItems = collect($announcements->items())->map(fn($ann) => $this->transformAnnouncementData($ann));
                return response()->json([
                    'data' => $transformedItems,
                    'current_page' => $announcements->currentPage(),
                    'last_page' => $announcements->lastPage(),
                    'per_page' => $announcements->perPage(),
                    'total' => $announcements->total(),
                    'from' => $announcements->firstItem(),
                    'to' => $announcements->lastItem()
                ]);
            }

            // Handle collection results
            $transformed = collect($announcements)->map(fn($ann) => $this->transformAnnouncementData($ann));
            return response()->json($transformed);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching announcements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $announcement = $this->announcementService->store($request);
            return $announcement;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating announcement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $announcement = $this->announcementService->update($request);
            return $announcement;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating announcement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $announcement = $this->announcementService->destroy($request->id);
            return $announcement;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting announcement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $id_parameter = $request->id;
            $announcement = $this->announcementService->show($id_parameter);
            return response()->json($this->transformAnnouncementData($announcement));
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching announcement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
