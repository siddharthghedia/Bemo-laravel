<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
], function ($router) {

    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

});

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    //board apis
    Route::post('/create-board', [\App\Http\Controllers\Api\BoardController::class, 'create']);
    Route::post('/delete-board', [\App\Http\Controllers\Api\BoardController::class, 'delete']);

    //cards apis
    Route::get('/list-card', [\App\Http\Controllers\Api\CardController::class, 'list']);
    Route::post('/create-card', [\App\Http\Controllers\Api\CardController::class, 'create']);
    Route::post('/update-card', [\App\Http\Controllers\Api\CardController::class, 'update']);
    Route::post('/delete-card', [\App\Http\Controllers\Api\CardController::class, 'delete']);
});

