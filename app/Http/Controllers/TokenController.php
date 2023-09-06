<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectTokenRequest;
use App\Http\Resources\ProjectTokenResource;
use App\Models\Project;
use App\Models\ProjectToken;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function list(Project $project)
    {
        $projectTokens = $project->tokens()->paginate(15);
        return new ProjectTokenResource($projectTokens);
    }

    public function view(int $projectId, ProjectToken $token)
    {
        return new ProjectTokenResource($token);
    }

    public function store(CreateProjectTokenRequest $request, Project $project)
    {
        $projectToken = $project->tokens()->create([
            'user_id' => $request->user()->id,
            'token' => hash('sha256', Str::random(40)),
            'name' => $request->name,
            'scope' => $request->scope
        ]);

        return new ProjectTokenResource($projectToken);
    }

    public function edit(CreateProjectTokenRequest $request, int $projectId, ProjectToken $token)
    {
        $token->update([
            'name' => $request->name,
            'scope' => $request->scope
        ]);

        return new ProjectTokenResource($token);
    }

    public function delete(int $projectId, ProjectToken $token)
    {
        $token->delete();

        return response()->json(__('messages.deleted_success'), 202);
    }
}
