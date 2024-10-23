<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run()
    {
        // verifica se os status estao criados caso estejam nao cria
        if (Status::count() === 0) {
            Status::create([
                'name' => 'Pendente',
                'description' => 'Tarefa ainda não iniciada',
                'order' => 1,
                // random color
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)
            ]);
            Status::create([
                'name' => 'Em andamento',
                'description' => 'Tarefa em andamento',
                'order' => 2,
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)

            ]);
            Status::create([
                'name' => 'Concluída',
                'description' => 'Tarefa concluída',
                'order' => 3,
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)

            ]);
        }
        
    }
}
