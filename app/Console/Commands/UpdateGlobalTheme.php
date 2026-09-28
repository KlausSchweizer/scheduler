<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateGlobalTheme extends Command
{
    protected $signature = 'theme:toggle {mode}';
    protected $description = 'Atualiza o tema global do sistema';

    public function handle()
    {
        $mode = $this->argument('mode');
        Cache::put('global_theme', $mode);

        $this->info("Tema global alterado para: {$mode}");
    }
}
