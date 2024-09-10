<?php

namespace Decoda\Audit\Entities;

use CodeIgniter\Entity\Entity;

class Audit extends Entity
{
    protected $table      = 'audits';
    protected $primaryKey = 'id';
    protected $dates      = ['created_at'];
    protected $casts      = [
        'source_id'     => 'int',
        'user_id'       => 'string',
        'company_id'    => 'string'
    ];
}
