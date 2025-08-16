<?php

use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\JwtAuthenticate;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::withoutMiddleware([JwtAuthenticate::class])->group(function () {
 Route::post('register', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
    Route::post('forgot-password',[PasswordResetController::class,'forgotPassword']);
Route::post('reset-password',[PasswordResetController::class,'resetPassword'])->name('password.reset');
});

Route::group([

    'middleware' =>  JwtAuthenticate::class,
    

], function ($router) {

  # Authentication group
   
    Route::post('logout',[AuthController::class,'logout'] );
    Route::post('refresh',[AuthController::class,'refresh'] );
    Route::get('me', [AuthController::class,'me']);
    Route::get('user',[AuthController::class,'getAuthenticatedUser']);
    Route::put('user',[AuthController::class,'updateUser']);

  #Project group
  Route::get('projects',[ProjectController::class,'index']);
  Route::post('projects',[ProjectController::class,'create']);
  Route::get('projects/{project}',[ProjectController::class,'show']);
  Route::put('projects/{project}',[ProjectController::class,'update']);
  Route::delete('projects/{project}',[ProjectController::class,'delete']);
  Route::post('projects/{project}/members',[ProjectController::class,'addMember']);
  Route::post('projects/{project}/members/{user}',[ProjectController::class,'removeMember']);

  #Task Route Group
   Route::post('projects/{project}/tasks',[TaskController::class,'create']);
   Route::get('projects/{project}/tasks',[TaskController::class, 'index']); 
   Route::get('tasks/{task}',[TaskController::class, 'show']);
   Route::delete('tasks/{task}',[TaskController::class, 'delete']);
   Route::put('tasks/{task}/assign',[TaskController::class, 'assignTaskToUser']);
   Route::put('tasks/{task}/status',[TaskController::class, 'updateTaskStatus']);

   #dashboard Group
  Route::get('dashboard/stats',[DashboardController::class,'stats']);
  Route::get('dashboard/recent-activity',[DashboardController::class,'recentActivity']);
  Route::get('users/{user}/tasks',[DashboardController::class,"showUserTasks"]);
  Route::get('users/{user}/tasks1',[DashboardController::class,"showUserTasks1"]);

  #password reset Group

  
});

/**
 * GET /api/dashboard/r
 * 
 * 
 * 

 * 
 */

