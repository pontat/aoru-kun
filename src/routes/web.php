<?php

use App\Http\Controllers\Auth\LineAuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/line-login', [LineAuthController::class, 'login'])->middleware('guest');

Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/history', [TaskController::class, 'history']);

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/tasks/{targetDate}', [TaskController::class, 'findAllByAuthUserAndTargetDate']);
    Route::post('/tasks', [TaskController::class, 'create']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);

    Route::get('/tasks/history/{targetMonth}', [TaskController::class, 'findAllByAuthUserAndTargetMonth']);
});

require __DIR__ . '/auth.php';
