<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

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

Route::middleware('auth.token')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
    Route::get('/status/{file_id}', [FileController::class, 'status']);
    Route::post('/update-status', [FileController::class, 'updateStatus']);
});


