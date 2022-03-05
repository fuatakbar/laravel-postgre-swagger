<?php

use App\Http\Controllers\{Api\V1\AuthController, LogicalTestController};
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

// lexicoGraphically result will showed on this route
Route::get('/logical-test', [LogicalTestController::class, 'index']);

// all of development test route

// AUTH
Route::group(['middleware' => 'api', 'prefix' => 'api/v1'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/who-am-i', [AuthController::class, 'profile']);
});

