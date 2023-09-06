<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\Translates\TranslateController;
use App\Http\Controllers\Translates\ImportController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->controller(AuthController::class)->group(function() {
    Route::post('login', 'login')->middleware('guest:sanctum');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::prefix('projects')->controller(ProjectController::class)->middleware('auth:sanctum')->group(function() {
    Route::get('/', 'list');
    Route::get('/{project}/access', 'listProjectAccess')->middleware('role:admin');
    Route::post('/', 'store')->middleware('role:admin');
    Route::get('{project}', 'view')->middleware('access.project');
    Route::delete('{project}', 'delete')->middleware('role:admin');
    Route::post('{project}', 'edit')->middleware('role:admin');
});

Route::prefix('projects/{project}/languages')->controller(LanguageController::class)->middleware(['auth:sanctum', 'access.project'])->group(function() {
    Route::get('/', 'list');
    Route::post('/', 'store');
    Route::get('{language}', 'view')->middleware('access:language');
    Route::delete('{language}', 'delete')->middleware('access:language');
    Route::post('{language}', 'edit')->middleware('access:language');
});

Route::prefix('projects/{project}/tokens')->controller(TokenController::class)->middleware(['auth:sanctum', 'role:developer', 'access.project'])->group(function() {
    Route::get('/', 'list');
    Route::post('/', 'store');
    Route::get('{token}', 'view')->middleware('access:token');
    Route::post('{token}', 'edit')->middleware('access:token');
    Route::delete('{token}', 'delete')->middleware('access:token');
});

Route::prefix('projects/{project}/translates')->controller(TranslateController::class)->middleware(['auth:sanctum', 'access.project'])->group(function() {
    Route::get('/', 'list');
    Route::post('/languages/{language}', 'store');
    Route::get('{translate}', 'view')->middleware('access:translate');
    Route::post('{translate}', 'edit')->middleware('access:translate');
    Route::delete('{translate}', 'delete')->middleware('access:translate');
});

Route::prefix('projects/{project}/import')->controller(ImportController::class)->middleware(['auth:sanctum', 'access.project'])->group(function() {
    Route::post('/sheets', 'sheets');
    Route::post('/json', 'json');
    Route::post('{import}', 'store');
    Route::delete('{import}', 'delete');
    Route::get('/last', 'last');
});

Route::post('sync/{token}', [ImportController::class, 'sync'])->middleware(['token.access:full_access']);;

Route::prefix('users')->controller(UserController::class)->middleware(['auth:sanctum', 'role:admin'])->group(function() {
    Route::get('/', 'list');
    Route::post('/', 'store');
    Route::get('{user}', 'view');
    Route::delete('{user}', 'delete');
    Route::post('{user}', 'edit');
    Route::post('{user}/password', 'editPassword');
    Route::post('{user}/projects/{project}', 'addProjectAccess')->middleware('role:admin');
    Route::delete('{user}/projects/{project}', 'removeProjectAccess')->middleware('role:admin');
    Route::get('{name}/search/projects/{project}', 'searchProjectAccess')->middleware('role:admin');
});
