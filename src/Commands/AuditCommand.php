<?php

declare(strict_types=1);

namespace Decoda\Audit\Commands;

use CodeIgniter\CLI\BaseCommand;

abstract class AuditCommand extends BaseCommand
{
    /**
     * Command grouping.
     */
    protected $group = 'Audit';

    /**
     * location to save.
     */
    protected string $path = WRITEPATH . 'audit';
}
