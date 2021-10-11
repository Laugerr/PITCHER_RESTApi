<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
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
Route::get('users', [UserController::class, 'index'])->middleware('admin');
Route::get('users/checkstatus', [UserController::class, 'userOnlineStatus'])->middleware('auth:sanctum');
Route::get('users/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::post('users', [UserController::class, 'store'])->middleware('admin');
Route::post('users/avatar', [UserController::class, 'avatar_create'])->middleware('auth:sanctum');
Route::delete('users/{id}', [UserController::class, 'deleteUser'])->middleware('admin');

//========================= POST MODULE ======================

Route::get('posts', [PostController::class, 'index'])->middleware('auth:sanctum');
Route::get('posts/{id}', [PostController::class, 'show'])->middleware('auth:sanctum');
Route::post('posts/', [PostController::class, 'create'])->middleware('auth:sanctum');
Route::post('posts/{id}/comments', [PostController::class, 'commentCreate'])->middleware('auth:sanctum');
Route::get('posts/{id}/comments', [PostController::class, 'indexComment'])->middleware('auth:sanctum');
Route::patch('posts/{id}', [PostController::class, 'update'])->middleware('auth:sanctum');
Route::delete('posts/{id}', [PostController::class, 'destroy'])->middleware('auth:sanctum');
Route::post('posts/{id}/like', [PostController::class, 'store_likes'])->middleware('auth:sanctum');
Route::post('posts/{id}/like', [PostController::class, 'store_likes'])->middleware('auth:sanctum');
Route::delete('posts/{id}/like', [PostController::class, 'deletePostLike'])->middleware('auth:sanctum');
Route::get('posts/{id}/like', [PostController::class, 'getPostLike'])->middleware('auth:sanctum');

//====================== CATEGORY MODULE ======================

Route::post('/categories', [CategoryController::class, 'create'])->middleware('auth:sanctum');
Route::get('/categories', [CategoryController::class, 'index'])->middleware('auth:sanctum');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->middleware('auth:sanctum');
Route::get('/categories/{id}/posts', [CategoryController::class, 'postsByCategory'])->middleware('auth:sanctum');
Route::patch('/categories/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');

//====================== COMMENT MODULE ======================

Route::get('/comments/{id}', [CommentController::class, 'show'])->middleware('auth:sanctum');
Route::patch('/comments/{id}', [CommentController::class, 'update'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
