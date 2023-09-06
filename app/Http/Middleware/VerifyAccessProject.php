<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class VerifyAccessProject
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        $project = $request->route('project');

        if (is_numeric($project)) {
            $project = Project::find($project);
        }

        if ($request->user()->accessProjects()->where('project_id', $project->id)->exists()) {
            return $next($request);
        }

        return response("__('messages.not_authorized')",401);
    }
}
