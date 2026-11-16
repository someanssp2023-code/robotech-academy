<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\BackupController;

class AutoBackupCommand extends Command
{
    protected $signature   = 'backup:auto';
    protected $description = 'Automatically create a full database backup (runs via scheduler)';

    public function handle(): int
    {
        $this->info('[AutoBackup] Starting scheduled database backup…');

        try {
            $controller = new BackupController();
            $controller->autoBackup();
            $this->info('[AutoBackup] ✅ Backup completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('[AutoBackup] ❌ Failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}