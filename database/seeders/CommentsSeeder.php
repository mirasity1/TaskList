<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder
{
    // Generate comments for random tasks
    public function run()
    {
        // Contar o total de usuários e tarefas
        $userCount = User::count();
        $taskCount = Task::count();

        // Verifica se há usuários e tarefas no banco de dados
        if ($userCount === 0 || $taskCount === 0) {
            return; // Não gera comentários se não houver usuários ou tarefas
        }

        for ($i = 0; $i < 5; $i++) {
            // Seleciona um usuário aleatório
            $randomUserIndex = rand(0, $userCount - 1); 
            $user = User::skip($randomUserIndex)->first(); // Pula para o usuário no índice aleatório
            
            // Seleciona uma tarefa aleatória
            $randomTaskIndex = rand(0, $taskCount - 1);
            $task = Task::skip($randomTaskIndex)->first(); // Pula para a tarefa no índice aleatório

            Comment::create([
                'content' => 'Comentário aleatório ' . ($i + 1),
                'user_id' => $user ? $user->id : null, // Verifica se o usuário existe
                'task_id' => $task ? $task->id : null, // Verifica se a tarefa existe
            ]);
        }
    }
}
