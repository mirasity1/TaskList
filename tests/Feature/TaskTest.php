<?php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase; // To Grant database refresh after each test

    public function test_can_fetch_tasks()
    {
        // Crie tarefas de exemplo
        $this->artisan('db:seed'); // Run seed

        // Faça uma requisição à sua API
        $response = $this->getJson('/api/tasks');

        // Verifique se a resposta é bem-sucedida
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'description', 'status_id', 'user_id'],
        ]);
    }

    public function test_authenticated_user_can_create_task()
    {
        // Crie um usuário com UserFactory
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
        
        

        $taskData = [
            'title' => 'Nova Tarefa',
            'description' => 'Descrição da nova tarefa',
            'status_id' => 1,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201); // Verifica se a criação foi bem-sucedida
        $this->assertDatabaseHas('tasks', $taskData); // Verifica se a tarefa foi salva no banco de dados
    }
}
