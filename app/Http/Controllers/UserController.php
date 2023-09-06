<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list()
    {
        return new UserResource(User::get());
    }

    public function view(User $user)
    {
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return new UserResource($user);
    }

    public function edit(EditUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        return new UserResource($user);
    }

    public function editPassword(Request $request, User $user)
    {
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return new UserResource($user);
    }

    public function addProjectAccess(User $user, Project $project)
    {
        $check = $user->accessProjects()->where('project_id', $project->id);

        if ($check->exists()) {
            return response()->json([
                'message' => __('messages.already_exists')
            ], 404);
        }

        $user->accessProjects()->attach($project->id);

        return response()->json([
            'message' => __('messages.success_save'),
            'user' => $user
        ], 201);
    }

    public function removeProjectAccess(User $user, Project $project)
    {
        $check = $user->accessProjects()->where('project_id', $project->id);

        if (!$check->exists()) {
            return response()->json([
                'message' => __('messages.not_exists')
            ], 404);
        }

        $user->accessProjects()->detach($project->id);

        return response()->json([
            'message' => __('messages.deleted_success')
        ], 202);
    }

    public function searchProjectAccess(string $userName, Project $project)
    {
        $projectUsers = $project->access()->where("name", "like", "%$userName%")->get();
        $users = User::where("name", "like", "%$userName%")->get();

        return new UserResource($users->diff($projectUsers));
    }

    public function delete(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => __('messages.deleted_success')
        ], 202);
    }
}
