<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return view('welcome');
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('todo-lists/create', [TodoListController::class, 'store'])->middleware('auth:api');
Route::post('tasks/create', [TaskController::class, 'store'])->middleware('auth:api');

Route::get('todos', [TodoListController::class, 'index']);
Route::get('todo/{id}', [TodoListController::class, 'getById']);
Route::put('todo/{id}', [TodoListController::class, 'update']);
Route::delete('todo/{id}', [TodoListController::class, 'destroy']);

Route::get('tasks/', [TaskController::class, 'index']);
Route::get('task/{id}', [TaskController::class, 'getById']);
Route::put('task/{id}', [TaskController::class, 'update']);
Route::delete('task/{id}', [TaskController::class, 'destroy']);
