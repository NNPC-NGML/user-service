<?php

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
    /////// Create Department
    Route::post('/create_department',[DepartmentController::class, 'create'])->name('create_department');
    //////// Update a Department
    Route::put('/update_department/{id}',[DepartmentController::class, 'update'])->name('update_department');
    ////////View All Department
    Route::get('/department',[DepartmentController::class, 'index'])->name('view_all_department');
    ////////View a Department
    Route::get('/department/{id}',[DepartmentController::class, 'show'])->name('view_department');
});
