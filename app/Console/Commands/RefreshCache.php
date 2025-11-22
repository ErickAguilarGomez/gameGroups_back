<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia todos los cachés de Laravel (route, view, config, cache, event)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine();
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('event:clear');
        $this->call('cache:clear');
        $this->newLine();
        return 0;
    }
}
