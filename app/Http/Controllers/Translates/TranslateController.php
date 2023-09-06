<?php

namespace App\Http\Controllers\Translates;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslateRequest;
use App\Http\Resources\TranslateResource;
use App\Models\Language;
use App\Models\Project;
use App\Models\Translate;
use App\Services\FilterTranslatesService;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    public function list(Request $request, Project $project, FilterTranslatesService $filter)
    {
        $filterTranslates = $filter->paginate($project, [
            'language_id' => $request->get('language_id'),
            'key' => $request->get('key')
        ], $request->get('per_page', 15));

        return new TranslateResource($filterTranslates);
    }

    public function store(Project $project, Language $language, StoreTranslateRequest $request)
    {
        $projectTranslate = $project->translates()->create([
            'language_id' => $language->id,
            'key' => $request->key,
            'value' => $request->value
        ]);

        return new TranslateResource($projectTranslate);
    }

    public function view(int $projectId, Translate $translate)
    {
        return new TranslateResource($translate);
    }

    public function edit(StoreTranslateRequest $request, int $projectId, Translate $translate)
    {
        $translate->update([
            'key' => $request->key,
            'value' => $request->value
        ]);

        return new TranslateResource($translate);
    }

    public function delete(int $projectId, Translate $translate)
    {
        $translate->delete();

        return response()->json(__('messages.deleted_success'), 202);
    }
}
