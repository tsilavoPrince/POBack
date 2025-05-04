<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\InterviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrimaireController;
use App\Http\Controllers\SecondaireController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TertiaireController;



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


Route::get('/notifications/unread', [NotificationController::class, 'unreadCount']);
Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);

/**/

Route::post('/utilisateurs', [UtilisateurController::class, 'store']);

//formulaire de d'interview
Route::post('/interviews', [InterviewController::class, 'store']);
Route::post('/interview/auto', [InterviewController::class, 'autoInsert']);
//Route::middleware('auth:sanctum')->get('/depenses', [DepenseController::class, 'index']);
Route::put('/profile/update', [InterviewController::class, 'update']);

//login
Route::post('/login' , [AuthController::class, 'login']);

//Route::middleware('auth:sanctum')->get('/depenses', [DepenseController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/depenses', [DepenseController::class, 'index']);

    Route::get('/graphe', [DepenseController::class, 'graphe']);


    //maka ilai nom
    Route::get('/profile', [AuthController::class, 'profile']);

    //Route::get('/me', [AuthController::class, 'me']);
    Route::get('/nom', [AuthController::class, 'getNomUtilisateur']);


    Route::post('/logout', [AuthController::class, 'logout']);

    // donner

    Route::get('/primaires', [PrimaireController::class, 'index']);
    Route::get('/secondaires', [SecondaireController::class, 'secondaire']);
    Route::get('/tertiaires', [TertiaireController::class, 'tertiaire']);

    //modification des information 

    Route::put('/primaires/update', [PrimaireController::class, 'update']);
    Route::put('/secondaires/update', [SecondaireController::class, 'update']);
    Route::put('/tertiaires/update', [TertiaireController::class, 'update']);
    
    //total dep
    Route::get('/depenses/total', [DepenseController::class, 'getTotal']);

    //budget
    Route::get('/interviews/budget', [InterviewController::class, 'getBudget']);

    //salaire
    Route::put('/interviews/update-budget', [InterviewController::class, 'updateBudget']);
    //notification
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);


    Route::get('/notifications/unread', [NotificationController::class, 'unreadCount']);
//suprime le notification
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

});
