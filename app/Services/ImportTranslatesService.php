<?php
namespace App\Services;

use App\Models\Language;
use App\Models\Project;
use App\Models\Import;
use App\Models\ImportTranslate;
use App\Models\Translate;

class ImportTranslatesService
{
    private function existKeys(Import $import) {
        return $import->importTranslates()->join('translates', function($join) {
            $join->on('import_translates.key', '=', 'translates.key');
            $join->on('import_translates.language_id', '=', 'translates.language_id');
        })->get(['import_translates.language_id', 'import_translates.key']);
    }

    public function raportImport(Import $import) {
        return [
            'importedTranslates' => $import->importTranslates()->count(),
            'existKeys' => $this->existKeys($import)
        ];
    }

    public function import(Import $import)
    {
        $importedTranslates = $import->importTranslates()->get(['language_id', 'key', 'value'])->collect();

        foreach ($importedTranslates->chunk(100) as $chunkTranslates) {
            foreach ($chunkTranslates as $translate) {
                $translate['project_id'] = $import->project->id;
            }

            Translate::upsert($chunkTranslates->toArray(), ['language_id', 'key'], ['value']);
        }

        return $import->delete();
    }
}
