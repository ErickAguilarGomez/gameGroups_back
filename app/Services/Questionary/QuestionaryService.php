<?php

namespace App\Services\Questionary;

use App\Repositories\Questionary\QuestionaryRepository;

class QuestionaryService
{
    protected $questionaryRepository;

    public function __construct()
    {
        $this->questionaryRepository = new QuestionaryRepository();
    }

    public function index($request)
    {
        return $this->questionaryRepository->index($request);
    }

    public function show($id)
    {
        return $this->questionaryRepository->show($id);
    }

    public function showWithStats($request)
    {
        return $this->questionaryRepository->showWithStats($request->status, $request->user_id);
    }

    public function store($request)
    {
        return $this->questionaryRepository->store($request);
    }

    public function update($request)
    {
        return $this->questionaryRepository->update($request);
    }

    public function destroy($id)
    {
        return $this->questionaryRepository->destroy($id);
    }

    public function storeResponse($request)
    {
        return $this->questionaryRepository->storeResponse($request);
    }

    public function updateResponse($request)
    {
        return $this->questionaryRepository->updateResponse($request);
    }

    public function destroyResponse($id)
    {
        return $this->questionaryRepository->destroyResponse($id);
    }

    public function getUsersByOption($questionId)
    {
        return $this->questionaryRepository->getUsersByOption($questionId);
    }
}
