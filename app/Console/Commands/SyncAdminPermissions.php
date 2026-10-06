<?php

namespace App\Console\Commands;

use App\Services\Authorization\PermissionSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('permissions:sync')]
#[Description('Synchronize registered Admin permissions without deleting unknown permissions')]
class SyncAdminPermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(PermissionSyncService $permissionSyncService): int
    {
        $result = $permissionSyncService->sync();

        $this->info('Admin permission synchronization completed.');
        $this->table(['Result', 'Count'], [
            ['Registered', $result['registered']->count()],
            ['Created', $result['created']->count()],
            ['Already existing', $result['existing']->count()],
            ['Unregistered/obsolete (preserved)', $result['obsolete']->count()],
        ]);

        if ($result['created']->isNotEmpty()) {
            $this->line('Created: '.$result['created']->implode(', '));
        }

        if ($result['obsolete']->isNotEmpty()) {
            $this->warn('Preserved unregistered permissions: '.$result['obsolete']->implode(', '));
        }

        return self::SUCCESS;
    }
}
