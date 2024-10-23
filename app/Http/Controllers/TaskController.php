<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show','getComments']);
    }

    public function index(Request $request)
    {
        $query = Task::query();
        $user = null;
    
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            // Se o usuário não estiver autenticado, apenas tarefas públicas serão exibidas
        }
    
        // Filtragem de tarefas com base nos parâmetros do request
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
                    $query->where('completed', true);
                    break;
                case 'incomplete':
                    $query->where('completed', false);
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
            // Por padrão, exibir tarefas públicas e privadas do usuário autenticado
            $query->where('is_public', true);
            if ($user) {
                $query->orWhere('user_id', $user->id);
            }
        }
    
        // Ordenação por campo 'order'
        $query->orderBy('order', 'asc');
    
        // Obtenção das tarefas
        $tasks = $query->get();
    
        // Atribuir nome de usuário para cada tarefa
        foreach ($tasks as $task) {
            $task->user_name = $task->user ? $task->user->name : 'Usuário desconhecido';
        }
    
        return response()->json($tasks, 200);
    }

    public function store(Request $request)
    {
        // Validação dos dados da tarefa
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'status_id' => 'required|string',
        ]);

        // Criação da tarefa
        // is completed and
        $task = Task::create(array_merge($validatedData, ['user_id' => Auth::id()]));

        return response()->json($task, 201);

    }

    public function show($id)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'Você não está autenticado.'], 401);
        }

        $task = Task::findOrFail($id);

        if ($task->is_public) {
            return response()->json($task, 200);
        }

        if (Auth::check() && Auth::id() === $task->user_id) {
            return response()->json($task, 200);
        }

        return response()->json(['error' => 'Você não tem permissão para acessar esta tarefa.'], 403);
    }

    public function comment(Request $request, $taskId)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'Você precisa estar logado para comentar.'], 401);
        }

        // Verifica se a tarefa existe
        $task = Task::find($taskId);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada.'], 404);
        }

        // Validação dos dados do comentário
        $validatedData = $request->validate([
            'content' => 'required|string|max:500', // Exemplo de validação
        ]);

        // Criação do comentário
        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'content' => $validatedData['content'], // Usando os dados validados
        ]);

        return response()->json($comment, 201);
    }

    public function getComments($taskId)
    {
        // Verifica se a tarefa existe
        $task = Task::find($taskId);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada.'], 404);
        }

        // Carrega os comentários da tarefa
        $comments = $task->comments;

        return response()->json($comments, 200);
    }

    public function update(Request $request, $id)
    {
        // verifica se a tarefa existe e se é do utilizador, se não for, retorna erro
        $task = Task::findOrFail($id);
        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Você não tem permissão para editar esta tarefa.'], 403);
        }
        // Validação dos dados da tarefa
        $validatedData = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'status_id' => 'string',
            // order
            'order' => 'integer',
            
        ]);
        // Atualização da tarefa
        $task->update($validatedData);
        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'Você não está autenticado.'], 401);
        }

        Task::destroy($id);
        return response()->json(null, 204);
    }
}
