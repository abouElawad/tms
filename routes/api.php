<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\JwtAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::withoutMiddleware('jwt.auth')->group(function () {
 Route::post('register', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
});

Route::group([

    'middleware' =>  ['jwt.auth'],
    

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
});
/**


 */

