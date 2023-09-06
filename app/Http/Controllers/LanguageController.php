<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ProjectListService;

class LanguageController extends Controller
{
    public function list(Project $project, Request $request, ProjectListService $listService)
    {
        return new LanguageResource($listService->listLanguages($project, $request));
    }

    public function view(int $projectId, Language $language)
    {
        return new LanguageResource($language);
    }

    public function store(StoreLanguageRequest $request, Project $project)
    {
        $language = $project->languages()->create([
            'code' => $request->code
        ]);

        return new LanguageResource($language);
    }

    public function edit(StoreLanguageRequest $request, int $projectId, Language $language)
    {
        $language->update([
            'code' => $request->code
        ]);

        return new LanguageResource($language);
    }

    public function delete(int $projectId, Language $language)
    {
        $language->delete();

        return response()->json(__('messages.deleted_success'), 202);
    }
}
