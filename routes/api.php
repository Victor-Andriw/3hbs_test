<?php

use App\Http\Controllers\Airlines\AirlinesController;
use App\Http\Controllers\Airports\AirportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Flights\FlightsController;

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

Route::post('/auth/login', [LoginController::class, 'authenticate']);
Route::post('/auth/logout', [LoginController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) { return $request->user(); });

    Route::prefix('airports')->group(function () {
        Route::get('/get-list', [AirportsController::class, 'getAirports']);
        Route::post('/store', [AirportsController::class, 'store']);
        Route::post('/delete', [AirportsController::class, 'delete']);
    });

    Route::prefix('airlines')->group(function () {
        Route::get('/get-list', [AirlinesController::class, 'getAirlines']);
        Route::post('/store', [AirlinesController::class, 'store']);
        Route::post('/delete', [AirlinesController::class, 'delete']);
    });
    
    Route::prefix('flights')->group(function () {
        Route::get('/get-list', [FlightsController::class, 'getFlights']);
        Route::get('/get-catalogs', [FlightsController::class, 'getCatalogs']);
        Route::post('/store', [FlightsController::class, 'store']);
        Route::post('/delete', [FlightsController::class, 'delete']);
    });
});

