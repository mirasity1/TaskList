<?php

namespace App\Models;

use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'title',
        'description',
        'is_public',
        'user_id',
        'status_id',
        'is_completed',
        'order',
        'uuid',          // Novo campo UUID
    ];

    // Tipos de dados para casting
    protected $casts = [
        'is_public' => 'boolean',
        'order' => 'integer',
        'status_id' => 'string',
    ];

    // Adiciona timestamps automáticos
    public $timestamps = true;

    // Boot method para gerar UUID automaticamente
    protected static function boot()
    {
        parent::boot();

        // Atribuir UUID automaticamente ao criar uma nova tarefa
        static::creating(function ($task) {
            $task->uuid = Str::uuid()->toString();  // Assegure-se de converter para string
            \Log::info("Gerando UUID: {$task->uuid}");  // Log do UUID gerado
        });
    }

    // Relacionamento com o modelo User (pertence a um usuário)
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    // Relacionamento com o modelo Status (pertence a um status)
    public function status() : BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
