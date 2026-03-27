<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\AgentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the          RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|                                   
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public API routes (no token needed)
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// Protected API routes (token required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
});

Route::post('/agent', [AgentController::class, 'run'])->middleware('auth:sanctum');
Route::get('/agent/status/{jobId}', [AgentController::class, 'status'])->middleware('auth:sanctum');

