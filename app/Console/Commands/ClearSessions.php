<?php

// clear session command
namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearSessions extends Command
{
    protected $signature = 'sessions:clear';
    protected $description = 'Clear all sessions';
    public function handle()
    {
        $this->info('Clearing sessions...');
        $this->laravel['session']->getHandler()->gc(config('session.lifetime'));
        $this->info('Sessions cleared!');
    }
    
}

