<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;

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
    Route::put('/users/{userId}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete_user',[UserController::class, 'delete'])->name('delete_user')->middleware('auth');
    Route::post('/create_department',[DepartmentController::class, 'create'])->name('create_department');
    Route::put('/update_department/{id}',[DepartmentController::class, 'update'])->name('update_department');

    //Location
    Route::delete('/locations/{id}', [LocationController::class, 'delete'])->name('locations.delete');
});
