<?php

namespace App\Repositories\Questionary;

use Illuminate\Support\Facades\DB;
use Exception;

class QuestionaryRepository
{
    public function index($request)
    {
        try {
            $stm = null;
            $today = date('Y-m-d');

            if ($request->status == "active") {
                $query = DB::table('questionaries')
                    ->where(DB::raw('DATE(end_date)'), '>=', $today)
                    ->whereNull('deleted_at');

                if ($request->has('per_page') && $request->has('page')) {
                    $stm = $query->paginate($request->per_page, ['*'], 'page', $request->page);
                } else {
                    $stm = $query->get();
                }
            } elseif ($request->status == "inactive") {
                $query = DB::table('questionaries')
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
            } else {
                $query = DB::table('questionaries')->whereNull('deleted_at');
                if ($request->has('per_page') && $request->has('page')) {
                    $stm = $query->paginate($request->per_page, ['*'], 'page', $request->page);
                } else {
                    $stm = $query->get();
                }
            }
            return $stm;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function show($id)
    {
        try {
            $questionary = DB::table('questionaries')->where('id', $id)->first();

            if ($questionary) {
                $questions = DB::table('questions')->where('questionary_id', $id)->select('id', 'question')->get();
                $questionary->questions = $questions;
            }

            return $questionary;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function showWithStats($status = null, $userId = null)
    {
        try {
            $results = DB::select('CALL sp_get_questionaries_with_stats(?, ?)', [$status, $userId]);

            foreach ($results as $row) {
                if (isset($row->questions)) {
                    $row->questions = json_decode($row->questions);
                }
            }

            return $results;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $questionaryId = DB::table('questionaries')->insertGetId([
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            if ($request->has('questions') && is_array($request->questions)) {
                foreach ($request->questions as $q) {
                    $questionText = is_array($q) ? ($q['question'] ?? null) : $q;

                    if ($questionText) {
                        DB::table('questions')->insert([
                            'question' => $questionText,
                            'questionary_id' => $questionaryId,
                            'created_by' => $request->user_id,
                            'updated_by' => $request->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return $this->show($questionaryId);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            DB::table('questionaries')->where('id', $request->id)->update([
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'updated_by' => $request->user_id,
                'updated_at' => now(),
            ]);

            if ($request->has('questions') && is_array($request->questions)) {
                $existingIds = DB::table('questions')
                    ->where('questionary_id', $request->id)
                    ->pluck('id')
                    ->toArray();

                $incomingIds = [];

                foreach ($request->questions as $q) {
                    if (isset($q['id']) && in_array($q['id'], $existingIds)) {
                        DB::table('questions')->where('id', $q['id'])->update([
                            'question' => $q['question'],
                            'updated_by' => $request->user_id,
                            'updated_at' => now(),
                        ]);
                        $incomingIds[] = $q['id'];
                    } else {
                        $questionText = is_array($q) ? $q['question'] : $q;

                        $newId = DB::table('questions')->insertGetId([
                            'question' => $questionText,
                            'questionary_id' => $request->id,
                            'created_by' => $request->user_id,
                            'updated_by' => $request->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $incomingIds[] = $newId;
                    }
                }

                $toDelete = array_diff($existingIds, $incomingIds);
                if (!empty($toDelete)) {
                    DB::table('questions')->whereIn('id', $toDelete)->delete();
                }
            }

            DB::commit();
            return $this->show($request->id);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function destroy($id)
    {
        try {
            $affected = DB::table('questionaries')->where('id', $id)->update([
                'deleted_at' => now(),
            ]);
            return $affected;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function storeResponse($request)
    {
        try {
            $id = DB::table('questionaries_response')->insertGetId([
                'user_id' => $request->user_id,
                'question_id' => $request->question_id,
                'questionary_id' => $request->questionary_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return DB::table('questionaries_response')->where('id', $id)->first();
        } catch (Exception $e) {
            return $e;
        }
    }

    public function updateResponse($request)
    {
        try {
            if ($request->has('id')) {
                DB::table('questionaries_response')->where('id', $request->id)->update([
                    'question_id' => $request->question_id,
                    'updated_at' => now(),
                ]);
                return DB::table('questionaries_response')->where('id', $request->id)->first();
            }

            return null;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function destroyResponse($id)
    {
        try {
            return DB::table('questionaries_response')->where('id', $id)->update([
                'deleted_at' => now()
            ]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getUsersByOption($questionId)
    {
        try {
            $users = DB::table('questionaries_response')
                ->join('users as u', 'questionaries_response.user_id', '=', 'u.id')
                ->where('questionaries_response.question_id', $questionId)
                ->whereNull('questionaries_response.deleted_at')
                ->select('u.id', 'u.name', 'u.email', 'u.photo_url', 'questionaries_response.created_at as response_date')
                ->get();

            return $users;
        } catch (Exception $e) {
            return $e;
        }
    }
}
