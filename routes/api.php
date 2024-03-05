<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UnitController;

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
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{userId}', [UserController::class, 'show'])->name('users.show');
    /////// Create Department
    Route::put('/users/{userId}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete_user',[UserController::class, 'delete'])->name('delete_user')->middleware('auth');
    Route::post('/create_department',[DepartmentController::class, 'create'])->name('create_department');
    //////// Update a Department
    Route::put('/update_department/{id}',[DepartmentController::class, 'update'])->name('update_department');
    ////////View All Department
    Route::get('/department',[DepartmentController::class, 'index'])->name('view_all_department');
    ////////View a Department
    Route::get('/department/{id}',[DepartmentController::class, 'show'])->name('view_department');
    ////////Delete a Department
    Route::delete('/department/{id}',[DepartmentController::class, 'destroy'])->name('delete_department');

    //Location
    Route::delete('/locations/{id}', [LocationController::class, 'delete'])->name('locations.delete');
    Route::patch('/locations/{id}', [LocationController::class, 'updateLocation'])->name('locations.update');

    Route::post('/create_location', [LocationController::class, 'create'])->name('locations.create');
    Route::get('/locations/{locationId}', [LocationController::class, 'show'])->name('locations.show');
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    //Unit
    Route::post('/create_unit',[UnitController::class, 'create'])->name('create_unit');

});
