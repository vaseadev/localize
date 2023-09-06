<?php
namespace App\Services;

use App\Services\ImportTranslatesService;
use App\Models\Language;
use App\Models\Project;
use App\Http\Requests\ImportJsonFileRequest;
use Exception;

class JsonFileService
{
    public function parse(ImportJsonFileRequest $request, Project $project)
    {
        $languageCode = $request->language;
        $array = json_decode(file_get_contents($request->file), true);
        $language = $project->languages->where('code', $languageCode)->first();
        $translates = [];

        if (!$language) {
            throw new Exception("Language $languageCode not found in database!");
        }

        foreach ($array as $arr) {
            array_push($translates, [
                'language_id' => $language->id,
                'key' => isset($arr['key']) ? $arr['key'] : "",
                'value' => isset($arr['value']) ? $arr['value'] : ""
            ]);
        }

        $import = $project->imports()->create([
            'user_id' => $request->user()->id,
            'type' => 'json'
        ]);

        $import->importTranslates()->createMany($translates);

        return $import;
    }
}
