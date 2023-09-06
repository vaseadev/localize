<?php
namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Exception;
use Illuminate\Http\Request;
use App\Services\ImportTranslatesService;
use App\Models\Project;

class GoogleSheetsService
{
    public function __construct() {
        $this->client = $this->getClient();
        $this->service = new Google_Service_Sheets($this->client);
    }

    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(config('app.credentials_google'));
        return $client;
    }

    private function parseLanguages(Project $project, array $docLanguages) {
        unset($docLanguages[0]);
        $parsedLanguages = [];

        foreach ($docLanguages as $index => $languageCode) {
            $dbLanguage = $project->languages->where('code', strtolower(trim($languageCode)))->first();

            if (!$dbLanguage) {
                throw new Exception("Language $languageCode not found in database!");
            }

            $parsedLanguages[$dbLanguage->id] = $index;
        }

        return $parsedLanguages;
    }

    private function parseRow(array $row, array $parsedLanguages, array &$translates) {
        foreach ($parsedLanguages as $idLang => $orderLang) {
            array_push($translates, [
                'language_id' => $idLang,
                'key' => $row[0],
                'value' => isset($row[$orderLang]) ? $row[$orderLang] : "",
            ]);
        }
    }

    public function validateImport(Request $request, Project $project)
    {
        $range = 'Sheet1';
        $sheetId = $request->sheetId; // 1L8Da_RgTUX6erTwv9lQABp2eWzgFBFkGbuLLumuJYP8
        $response = $this->service->spreadsheets_values->get($sheetId, $range);
        $rows = $response->getValues();
        $translates = [];

        if (count($rows[0]) < 2) {
            throw new Exception("No languages given!");
        }

        $parsedLanguages = $this->parseLanguages($project, $rows[0]);
        unset($rows[0]);

        foreach($rows as $row) {
            if (!$row[0]) {
                throw new Exception("A row has no key!");
            }

            $this->parseRow($row, $parsedLanguages, $translates);
        }

        $import = $project->imports()->create([
            'user_id' => $request->user()->id,
            'type' => 'google_sheets'
        ]);

        $import->importTranslates()->createMany($translates);

        return $import;
    }
}
