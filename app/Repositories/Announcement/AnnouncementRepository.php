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
            $query = DB::table('announcement')
                ->where(DB::raw('DATE(end_date)'), '>=', $today)
                ->whereNull('deleted_at');
            if ($request->has('per_page') && $request->has('page')) {
                $stm = $query->paginate($request->per_page, ['*'], 'page', $request->page);
            } else {
                $stm = $query->get();
            }
        }
        if ($request->status == "inactive") {
            $query = DB::table('announcement')
                ->where(function ($query) use ($today) {
                    $query->where(DB::raw('DATE(end_date)'), '<', $today)
                        ->whereNull('deleted_at');
                })
                ->orWhereNotNull('deleted_at');
            if ($request->has('per_page') && $request->has('page')) {
                $stm = $query->paginate($request->per_page, ['*'], 'page', $request->page);
            } else {
                $stm = $query->get();
            }
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
                'is_video' => $request->is_video ?? 0,
                'created_at' => now(),
            ]);
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function update($request)
    {
        try {
            $announcement = DB::table('announcement')->where('id', $request->id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'url' => $request->url,
                'user_id' => $request->user_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_video' => $request->is_video ?? 0,
                'updated_at' => now(),
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
    public function show($id)
    {
        try {
            $announcement = DB::table('announcement')->where('id', $id)->first();
            return $announcement;
        } catch (Exception $e) {
            return $e;
        }
    }
}
