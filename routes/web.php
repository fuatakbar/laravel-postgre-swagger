<?php

use App\Http\Controllers\LogicalTestController;
use App\Http\Controllers\Api\V1\{
    AuthController,
    EventController
};

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

// PRIVATE ROUTE
// all of development test route
Route::group(['middleware' => 'api', 'prefix' => 'api/v1'], function () {
    // AUTH
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/who-am-i', [AuthController::class, 'profile']);

    // EVENT
    Route::get('/event', [EventController::class, 'showList']);
    Route::get('/event/{id}', [EventController::class, 'showDetail']);
    Route::post('/event', [EventController::class, 'create']);
    Route::post('/event/{id}', [EventController::class, 'update']);
    Route::delete('/event/{id}', [EventController::class, 'delete']);
});

// PUBLIC ROUTE
// lexicoGraphically result will showed on this route
Route::get('/logical-test', [LogicalTestController::class, 'index']);

// event route
Route::get('/event/upcomings', [EventController::class, 'publicUpcoming']);

