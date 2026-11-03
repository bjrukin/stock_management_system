<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearSessions extends Command
{
    protected $signature = 'sessions:clear';
    protected $description = 'Clear all session files';

    public function handle()
    {
        $files = File::files(storage_path('framework/sessions'));

        foreach ($files as $file) {
            File::delete($file);
        }

        $this->info('All session files have been cleared.');
    }
}
