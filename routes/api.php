<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'user'], function (){
    Route::get('/detail', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
    Route::post('/update/{user}', [UserController::class, 'update'])->middleware('auth:sanctum') ;
    Route::delete('/destroy/{user}', [UserController::class, 'destroy'])->middleware('auth:sanctum') ; 
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']); 
});

Route::middleware('auth:sanctum')->group(function (){

   Route::post('/email-verification', [EmailVerificationController::class, 'email_verification']);
});