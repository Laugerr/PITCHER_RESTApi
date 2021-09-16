<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\User;

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

// ========================= AUTHENTICATION MODULE ======================

Route::post('auth/register', [AuthController::class , 'register']);
Route::post('auth/login', [AuthController::class , 'login']);
Route::post('auth/logout', [AuthController::class , 'logout']);
Route::post('auth/password-reset',[AuthController::class, 'passwordReset'])->name('password.reset');
Route::post('auth/password-reset/{token}',[AuthController::class, 'resetNewPassword']);


// ========================= USER MODULE ======================

Route::get('users/profile', [UserController::class, 'profile']);
Route::get('users', [UserController::class, 'index']);
Route::get('users/checkstatus', [UserController::class, 'userOnlineStatus'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
