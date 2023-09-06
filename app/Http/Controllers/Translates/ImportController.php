<?php

namespace App\Http\Controllers\Translates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;
use App\Http\Requests\ImportJsonFileRequest;
use App\Services\GoogleSheetsService;
use App\Services\ImportTranslatesService;
use App\Services\SyncJsonTranslatesService;
use App\Services\JsonFileService;
use App\Models\Project;
use App\Models\Import;
use App\Models\ProjectToken;

class ImportController extends Controller
{
    public function __construct(ImportTranslatesService $importService) {
        $this->importService = $importService;
    }

    public function sheets(Request $request, Project $project, GoogleSheetsService $sheetsService)
    {
        try {
            $import = $sheetsService->validateImport($request, $project);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }

        return response()->json([
            'raport' => $this->importService->raportImport($import),
            'import' => $import
        ]);
    }

    public function json(ImportJsonFileRequest $request, Project $project, JsonFileService $jsonService) {
        try {
            $import = $jsonService->parse($request, $project);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }

        return response()->json([
            'raport' => $this->importService->raportImport($import),
            'import' => $import
        ]);
    }

    public function store(int $projectId, Import $import)
    {
        if ($this->importService->import($import)) {
            return response()->json([
                'message' => __('messages.success_save')
            ]);
        }

        return response()->json([
            'message' => __('messages.fail_save')
        ], 403);
    }

    public function delete(int $projectId, Import $import)
    {
        $import->delete();

        return response()->json([
            'message' => __('messages.success_save')
        ]);
    }

    public function sync(string $token, Request $request, SyncJsonTranslatesService $syncJsonService)
    {
        try {
            $syncJsonService->sync($request->projectToken, $request);
            return response()->json([
                'message' => __('messages.success_save')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function last(Project $project) {
        return response()->json([
            'import' => $project->imports()->latest()->first()
        ]);
    }
}
