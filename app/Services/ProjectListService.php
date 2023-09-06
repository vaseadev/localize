<?php
namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectListService
{
    public function list(User $user)
    {
        if ($user->isAdmin()) {
            $projects = Project::latest()->paginate(15);
        } else {
            $projects = $user->accessProjects()->latest()->paginate(15);
        }

        return $projects;
    }

    public function listLanguages(Project $project, Request $request)
    {
        if ($request->get('per_page')) {
            $languages = $project->languages()->latest()->paginate($request->get('per_page', 15));
        } else {
            $languages = $project->languages()->latest()->get();
        }

        return $languages;
    }
}
