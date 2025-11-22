<?php

namespace App\Repositories\Announcement;

use Illuminate\Support\Facades\DB;
use Exception;

class AnnouncementRepository
{

    public function __construct() {}

    public function index($request)
    {
        $stm = null;
        $today = date('Y-m-d');
        if ($request->status == "active") {
            $stm = DB::table('announcement')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->whereNull('deleted_at')
                ->paginate($request->per_page, ['*'], 'page', $request->page);
        }
        if ($request->status == "next") {
            $stm = DB::table('announcement')
                ->where('start_date', '>', $today)
                ->whereNull('deleted_at')
                ->paginate($request->per_page, ['*'], 'page', $request->page);
        }
        if ($request->status == "inactive") {
            $stm = DB::table('announcement')
                ->where(function ($query) use ($today) {
                    $query->where('end_date', '<', $today)
                        ->whereNull('deleted_at');
                })
                ->orWhereNotNull('deleted_at')
                ->paginate($request->per_page, ['*'], 'page', $request->page);
        }


        return $stm;
    }
    public function store($request)
    {
        try {
            $announcement = DB::table('announcement')->insert([
                'title' => $request->title ?? null,
                'description' => $request->description ?? null,
                'url' => $request->url ?? null,
                'user_id' => $request->user_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function update($request, $id)
    {
        try {
            $announcement = DB::table('announcement')->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'url' => $request->url,
                'user_id' => $request->user_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function destroy($id)
    {
        try {
            $announcement = DB::table('announcement')->where('id', $id)->update([
                'deleted_at' => now(),
            ]);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
}
