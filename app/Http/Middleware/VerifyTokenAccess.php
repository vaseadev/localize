<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ProjectToken;

class VerifyTokenAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $scope)
    {
        $projectToken = ProjectToken::where('token', $request->route('token'))->first();

        if (!$projectToken) {
            return response([
                'message' => 'This project token does not exist!'
            ], 404);
        }

        if ($projectToken->scope != $scope && $projectToken->scope != "full_access") {
            return response([
                'message' => "This token does not have access for this action!"
            ], 401);
        }

        $request->merge([
            'projectToken' => $projectToken
        ]);

        return $next($request);

    }
}
