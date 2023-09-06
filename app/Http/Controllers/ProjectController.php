<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectListService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function list(Request $request, ProjectListService $project)
    {
        $projects = $project->list($request->user());

        return new ProjectResource($projects);
    }

    public function listProjectAccess(Project $project)
    {
        return new ProjectResource($project->access);
    }

    public function view(Project $project)
    {
        return new ProjectResource($project);
    }

    public function store(CreateProjectRequest $request)
    {
        $project = $request->user()->projects()->create([
            'name' => $request->name
        ]);

        return new ProjectResource($project);
    }

    public function edit(CreateProjectRequest $request, Project $project)
    {
        $project->update([
            'name' => $request->name
        ]);

        return new ProjectResource($project);
    }

    public function delete(Project $project)
    {
        $project->delete();

        return response()->json(__('messages.deleted_success'), 202);
    }
}
