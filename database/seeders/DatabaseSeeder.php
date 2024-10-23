<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Verifica se o usuário padrão já existe
        if (!User::where('email', 'admin@mirasity.pt')->exists()) {
            // Cria um usuário padrão
            User::factory()->create([
                'name' => 'Default User',
                'email' => 'admin@mirasity.pt',
            ]);
        }
        // Cria 10 usuários aleatórios
        User::factory(10)->create();

        
        $this -> call(StatusSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(CommentsSeeder::class);
    }
}
