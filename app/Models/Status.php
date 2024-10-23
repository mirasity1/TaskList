<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasMany;



class Status extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'statuses';

    use HasFactory;

    // name, description e ordem 
    protected $fillable = [
        'name',
        'description',
        'order',
    ];
    
    

    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }
}
