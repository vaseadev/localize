<?php
namespace App\Services;

use App\Models\Project;

class FilterTranslatesService
{
    public function paginate(Project $project, array $filters, int $perPage)
    {
        $translates = $project->translates();

        if ($filters['language_id']) {
            $translates->where('language_id', $filters['language_id']);
        }

        if ($filters['key']) {
            $translates->where('key', 'like', '%'.$filters['key'].'%');
        }

        return $translates->latest()->paginate($perPage);
    }
}
