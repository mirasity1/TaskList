<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User; // Para associar tarefas a usuários
use App\Models\Status;
use Illuminate\Support\Str;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Contar o total de usuários
        $userCount = User::count();

        $StatusCount = Status::count();

        // Gerar múltiplas tarefas aleatórias
        for ($i = 0; $i < 10; $i++) {
            // Seleciona um usuário aleatório
            $randomIndex = rand(0, $userCount - 1); // Gera um índice aleatório
            $user = User::skip($randomIndex)->first(); // Pula para o usuário no índice aleatório
            
            $random2Index = rand(0, $StatusCount - 1); // Gera um índice aleatório
            $Status = Status::skip($random2Index)->first(); // Pula para o usuário no índice aleatório


            Task::create([
                'title' => 'Tarefa Aleatória ' . ($i + 1),
                'description' => 'Descrição da tarefa aleatória ' . ($i + 1),
                'is_public' => (bool)rand(0, 1), // Gera um valor booleano aleatório
                'user_id' => $user ? $user->id : null, // Verifica se o usuário existe
                'status_id' => $Status ? $Status->id : null, // Verifica se o status existe
                'is_completed' => (bool)rand(0, 1), // Gera um valor booleano aleatório
                'order' => $i + 1, // Ordem sequencial
                'uuid' => Str::uuid()->toString(),   // Gera UUID manualmente
            ]);
        }
    }

}
