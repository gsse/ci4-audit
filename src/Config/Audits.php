<?php

namespace Decoda\Audit\Config;

use CodeIgniter\Config\BaseConfig;

class Audit extends BaseConfig
{
    // Session key in that contains the integer ID of a logged in user
    public $sessionUserId = 'logged_in';

    public $sessionCompanyId = 'logged_in';

    // Whether to continue instead of throwing exceptions
    public $silent = true;
}
