<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function () {
    Route::post('/create_user',[UserController::class, 'create'])->name('create_user');

    //Location
    Route::delete('/locations/{id}', [LocationController::class, 'delete'])->name('locations.delete');
    Route::post('/create_location', [LocationController::class, 'create'])->name('locations.create');
});
