<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Task;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Task1Controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show', 'getComments']);
    }
    public function index(Request $request)
    {
        $query = Task::query();
        $user = null;
    
        // Verificar autenticação do usuário
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            // Tratar a exceção caso o usuário não esteja autenticado
        }
    
        // Filtragem de tarefas
        if ($request->has('filter')) {
            switch ($request->input('filter')) {
                case 'public':
                    $query->where('is_public', true);
                    break;
                case 'private':
                    if ($user) {
                        $query->where('is_public', false)->where('user_id', $user->id);
                    }
                    break;
                case 'completed':
                    $query->where('is_completed', true);
                    break;
                case 'incomplete':
                    $query->where('is_completed', false);
                    break;
                case 'my_tasks':
                    if ($user) {
                        $query->where('user_id', $user->id);
                    }
                    break;
                default:
                    $query->where('is_public', true);
                    if ($user) {
                        $query->orWhere('user_id', $user->id);
                    }
                    break;
            }
        } else {
            // Exibir tarefas públicas e, se autenticado, privadas do usuário
            $query->where('is_public', true);
            if ($user) {
                $query->orWhere('user_id', $user->id);
            }
        }
    
        // Ordenação
        $query->orderBy('order', 'asc');
        $query->orderBy('created_at', 'desc');
        $tasks = $query->get();
    
        foreach ($tasks as $task) {
            $task->user_name = $task->user ? $task->user->name : 'Usuário desconhecido';
        }
        
        return response()->json($tasks); // Retorna as tarefas como JSON
    }


    // Função para visualizar os detalhes de uma tarefa
    public function show($uuid)
    {
        // Log da solicitação
        \Log::info("Visualizando detalhes da tarefa com UUID: {$uuid}");

        $task = Task::with('user:id,name')->where('uuid', $uuid)->firstOrFail();

        // Verificar autenticação do usuário
        try {
            $user = JWTAuth::parseToken()->authenticate();
            \Log::info("Usuário autenticado: ", ['user_id' => $user->id]);
        } catch (JWTException $e) {
            $user = null;
            \Log::warning("Usuário não autenticado ao visualizar tarefa.");
        }

        // Verifica se a tarefa é privada e o usuário não é o dono
        if (!$task->is_public && (!$user || $user->id !== $task->user_id)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retornar os detalhes da tarefa
        return response()->json($task);
    }

    // Função para armazenar uma nova tarefa
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        \Log::info("Usuário autenticado: ", ['user_id' => $user->id]);
    
        // Validação dos dados da requisição
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);
    
        // Criar uma nova tarefa com os dados validados
        $task = new Task($data);
        $task->user_id = $user->id;
     
        $StatusCount = Status::count();


        $random2Index = rand(0, $StatusCount - 1); // Gera um índice aleatório
        $Status = Status::skip($random2Index)->first(); // Pula para o usuário no índice aleatório


        if ($Status) {
            $task->status_id =  $Status ? $Status->id : null; // Verifica se o status existe 
        } else {
            \Log::warning("Nenhum status disponível para atribuir à tarefa.");
            $task->status_id = null; // Atribui null se não houver status
        }
    
        $task->order = Task::count() + 1; // Definindo a ordem da tarefa
        $task->save(); // Salvando a tarefa no banco de dados
    
        // Log da tarefa criada
        \Log::info("Tarefa criada: ", ['task_id' => $task->id, 'title' => $task->title]);

        // vai buscar o user name e devolve com a task
        $task->user_name = $task->user ? $task->user->name : 'Usuário desconhecido';
    
        // Certifique-se de retornar a tarefa com os dados apropriados
        return response()->json([
            'message' => 'Tarefa criada com sucesso',
            'task' => $task // Retornando a tarefa criada
        ], 201);
    }

    // Função para atualizar uma tarefa
    public function update(Request $request, $uuid)
    {
        $user = JWTAuth::parseToken()->authenticate();
        \Log::info("Usuário autenticado: ", ['user_id' => $user->id]);
    
        // Encontrar a tarefa pelo UUID
        $task = Task::where('uuid', $uuid)->firstOrFail();
    
        // Verificar se o usuário é o dono da tarefa
        if ($user->id !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Validação dos dados da requisição
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'is_completed' => 'required|boolean',
        ]);
    
        // Atualizar a tarefa com os dados validados
        $task->fill($data);
        $task->save(); // Salvando a tarefa no banco de dados
    
        // Log da tarefa atualizada
        \Log::info("Tarefa atualizada: ", ['task_id' => $task->id, 'title' => $task->title]);
    
        // Certifique-se de retornar a tarefa com os dados apropriados
        return response()->json([
            'message' => 'Tarefa atualizada com sucesso',
            'task' => $task // Retornando a tarefa atualizada
        ]);
    }

    // Função para excluir uma tarefa
    public function destroy($uuid)
    {
        $user = JWTAuth::parseToken()->authenticate();
        \Log::info("Usuário autenticado: ", ['user_id' => $user->id]);
    
        // Encontrar a tarefa pelo UUID
        $task = Task::where('uuid', $uuid)->firstOrFail();
    
        // Verificar se o usuário é o dono da tarefa
        if ($user->id !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Log da tarefa excluída
        \Log::info("Tarefa excluída: ", ['task_id' => $task->id, 'title' => $task->title]);
    
        // Excluir a tarefa
        $task->delete();
    
        // Certifique-se de retornar a resposta apropriada
        return response()->json(['message' => 'Tarefa excluída com sucesso']);
    }
    
}
