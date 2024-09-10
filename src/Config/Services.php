<?php

namespace Decoda\Audit\Config;

use Config\Services as BaseServices;
use Decoda\Audit\Audit;
use Decoda\Audit\Config\Audit as AuditConfig;

class Services extends BaseServices
{
    public static function audit(?AuditConfig $config = null, bool $getShared = true): Audit
    {
        if ($getShared) {
            return static::getSharedInstance('audit', $config);
        }

        // If no config was injected then load one
        if (empty($config)) {
            $config = config('Audit');
        }

        return new Audit($config);
    }
}
