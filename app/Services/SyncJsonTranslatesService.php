<?php
namespace App\Services;

use Illuminate\Http\Request;
use App\Models\ProjectToken;
use App\Models\Translate;
use Exception;

class SyncJsonTranslatesService
{
    public function sync(ProjectToken $projectToken, Request $request)
    {
        $jsonData = $request->json()->all();

        if (empty($jsonData)) {
            throw new Exception("The request is not a valid JSON.");
        }

        $language = $projectToken->project->languages->where('code', $jsonData['language'])->first();
        $translates = collect();

        if (!$language) {
            throw new Exception("Language ".$jsonData['language']." not found in database!");
        }

        foreach ($jsonData['translates'] as $translate) {
            $translates->push([
                'project_id' => $projectToken->project->id,
                'language_id' => $language->id,
                'key' => isset($translate['key']) ? $translate['key'] : "",
                'value' => isset($translate['value']) ? $translate['value'] : ""
            ]);
        }

        foreach ($translates->chunk(100) as $chunkTranslates) {
            Translate::upsert($chunkTranslates->toArray(), ['language_id', 'key'], ['value']);
        }
    }
}
