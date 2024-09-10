<?php

namespace Decoda\Audit;

use Decoda\Audit\Config\Audit as AuditConfig;
use Decoda\Audit\Models\AuditModel;

// CLASS
class Audit
{
    /**
     * Our configuration instance.
     *
     * @var AuditConfig
     */
    protected $config;

    /**
     * Audit rows waiting to add to the database.
     *
     * @var array
     */
    protected $queue = [];

    /**
     * Store the configuration
     *
     * @param AuditConfig $config The Audit configuration to use
     */
    public function __construct(AuditConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Checks the session for a logged in user based on config
     *
     * @return int The current user ID, 0 for "not logged in", -1 for CLI
     *
     * @deprecated This will be removed in the next major release; use codeigniter4/authentication-implementation
     */
    public function sessionUserId(): string
    {
        if (is_cli()) {
            return 0;
        }

        return session($this->config->sessionUserId) ?? 0;
    }

    public function sessionCompanyId(): string
    {
        if (is_cli()) {
            return 0;
        }

        return session($this->config->sessionCompanyId) ?? 0;
    }

    /**
     * Return the current queue (mostly for testing)
     */
    public function getQueue(): array
    {
        return $this->queue;
    }

    /**
     * Add an audit row to the queue
     *
     * @param array|null $audit The row to cache for insert
     */
    public function add(?array $audit = null)
    {
        if (empty($audit)) {
            return false;
        }

        // Add common data
        $audit['company_id']    = $this->sessionCompanyId();
        $audit['user_id']       = $this->sessionUserId(); // @phpstan-ignore-line
        $audit['created_at']    = date('Y-m-d H:i:s');

        $this->queue[] = $audit;
    }

    /**
     * Batch insert all audits from the queue
     *
     * @return $this
     */
    public function save(): self
    {
        if (! empty($this->queue)) {
            $auditModel = new AuditModel();
            $auditModel->insertBatch($this->queue);
        }

        return $this;
    }
}
