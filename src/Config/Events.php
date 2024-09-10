<?php

namespace Decoda\Audit\Config;

use CodeIgniter\Events\Events;

Events::on('post_system', static function () {
    service('audit')->save();
});
