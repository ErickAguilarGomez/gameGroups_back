<?php

namespace App\Http\Controllers\Questionary;

use App\Http\Controllers\Controller;
use App\Services\Questionary\QuestionaryService;
use Illuminate\Http\Request;
use Exception;

class QuestionaryController extends Controller
{
    protected $questionaryService;

    public function __construct()
    {
        $this->questionaryService = new QuestionaryService();
    }

    public function index(Request $request)
    {
        try {
            $questionaries = $this->questionaryService->index($request);
            return response()->json($questionaries);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching questionaries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $id = $request->id;
            $questionary = $this->questionaryService->show($id);
            return response()->json($questionary);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching questionary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showWithStats(Request $request)
    {
        try {
            $userId = $request->user() ? $request->user()->id : ($request->user_id ?? null);
            $request->merge(['user_id' => $userId]);
            $questionary = $this->questionaryService->showWithStats($request);
            return response()->json($questionary);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching questionary stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $questionary = $this->questionaryService->store($request);
            return response()->json($questionary);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating questionary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $questionary = $this->questionaryService->update($request);
            return response()->json($questionary);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating questionary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            $result = $this->questionaryService->destroy($id);
            return response()->json(['success' => (bool)$result]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting questionary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Response endpoints

    public function storeResponse(Request $request)
    {
        try {
            $response = $this->questionaryService->storeResponse($request);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error saving response',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateResponse(Request $request)
    {
        try {
            $response = $this->questionaryService->updateResponse($request);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating response',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyResponse(Request $request)
    {
        try {
            $id = $request->id;
            $result = $this->questionaryService->destroyResponse($id);
            return response()->json(['success' => (bool)$result]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting response',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsersByOption(Request $request)
    {
        try {
            $questionId = $request->question_id;
            $users = $this->questionaryService->getUsersByOption($questionId);
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
