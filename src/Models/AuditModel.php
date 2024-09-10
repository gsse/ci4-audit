<?php

namespace Decoda\Audit\Models;

use CodeIgniter\Model;
use Decoda\Audit\Entities\Audit;

class AuditModel extends Model
{
    protected $table          = 'audits';
    protected $primaryKey     = 'id';
    protected $returnType     = Audit::class;
    protected $useTimestamps  = false;
    protected $useSoftDeletes = false;
    protected $skipValidation = true;
    protected $allowedFields  = ['source', 'source_id', 'company_id', 'user_id', 'event', 'summary', 'created_at'];
}
