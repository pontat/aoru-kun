<?php

use App\Http\Controllers\LineUserController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::post('/webhook', WebhookController::class);

Route::get('/lineUsers/{lineId}', [LineUserController::class, 'findByLineId']);

Route::get('/tasks/{lineUserId}', [TaskController::class, 'findAllBylineUserId']);
Route::post('/tasks', [TaskController::class, 'create']);
Route::post('/tasks/{id}', [TaskController::class, 'update']);
