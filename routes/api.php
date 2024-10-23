<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Task1Controller;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', [AuthController::class, 'user']);


Route::middleware(['auth:api'])->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasksv1', [Task1Controller::class, 'index']);   // Filtragem e ordenação

    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::post('/tasks/{taskId}/comment', [TaskController::class, 'comment']); // Rota para adicionar um comentário
    Route::post('/tasksv1', [Task1Controller::class, 'store']); // Criar uma nova tarefa
    Route::put('/tasksv1/{uuid}', [Task1Controller::class, 'update']); // Atualizar uma tarefa pelo UUID
    Route::delete('/tasksv1/{uuid}', [Task1Controller::class, 'destroy']); // Excluir uma tarefa pelo UUID
    
});

Route::get('/tasksv1/{uuid}', [Task1Controller::class, 'show']); // Detalhes da tarefa pelo UUID
// refresh /api/auth/refresh
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::apiResource('statuses', StatusController::class);
Route::get('/tasks/{taskId}/comments', [TaskController::class, 'getComments']); // Nova rota para obter comentários
Route::get('/tasks/{id}', [TaskController::class, 'show']);



Route::apiResource('tasks', TaskController::class);
