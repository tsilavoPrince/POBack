<?php

use App\Http\Controllers\AuthController;

use App\Http\Controllers\InterviewController;
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
use App\Http\Controllers\UtilisateurController;

Route::post('/utilisateurs', [UtilisateurController::class, 'store']);


//formulaire de d'interview
Route::post('/interviews', [InterviewController::class, 'store']);

//login
Route::post('/login' , [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

