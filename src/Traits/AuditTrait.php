<?php

namespace Decoda\Audit\Traits;

use Swaggest\JsonDiff\JsonDiff;
use Decoda\Audit\Models\AuditModel;

// CLASS
trait AuditTrait
{
    protected array $changedData;
    /**
     * Takes an array of model $returnTypes
     * and returns an array of Audits,
     * arranged by object and event.
     * Optionally filter by $events
     * (string or array of strings).
     *
     * @param array|string|null $events
     *
     * @internal Due to a typo this function has never worked in a released version.
     *           It will be refactored soon without announcing a new major release
     *           so do not build on the signature or functionality.
     */
    public function getAudits(array $objects, $events = null): array
    {
        if (empty($objects)) {
            return [];
        }

        // Get the primary keys from the objects
        $objectIds = array_column($objects, $this->primaryKey);

        // Start the query
        $auditModel = new AuditModel;
        $query = $auditModel->where('source', $this->table)->whereIn('source_id', $objectIds);
        // $query = model(AuditModel::class)->builder()->where('source', $this->table)->whereIn('source_id', $objectIds);

        if (is_string($events)) {
            $query = $query->where('event', $events);
        } elseif (is_array($events)) {
            $query = $query->whereIn('event', $events);
        }

        // Index by objectId, event
        $array = [];
        // @phpstan-ignore-next-line
        while ($audit = $query->getUnbufferedRow()) {
            if (empty($array[$audit->{$this->primaryKey}])) {
                $array[$audit->{$this->primaryKey}] = [];
            }
            if (empty($array[$audit->{$this->primaryKey}][$audit->event])) {
                $array[$audit->{$this->primaryKey}][$audit->event] = [];
            }

            $array[$audit->{$this->primaryKey}][$audit->event][] = $audit;
        }

        return $array;
    }

    // record successful insert events
    protected function auditInsert(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $audit = [
            'source'    => $this->table,
            'source_id' => $data['id'], // @phpstan-ignore-line
            'event'     => 'insert',
            'summary'   => json_encode($data['data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ];
        service('audit')->add($audit);

        return $data;
    }

    // record successful update events
    protected function auditBeforeUpdate(array $data)
    {
        $fields = $this->getFieldData($this->table);
        $foreingKeys = $this->getForeignKeyData($this->table);

        foreach ($data['id'] as $sourceId) {

            $changedData = (array) $this->first($sourceId);

            // Format json fields for comparison
            foreach ($fields as $field) {
                if ($field->type == 'json' && in_array($field->name, array_keys($changedData)) && in_array($field->name, array_keys($data['data']))) {

                    $jsonDiff = new JsonDiff(
                        json_decode($changedData[$field->name]),
                        json_decode($data['data'][$field->name]),
                        JsonDiff::REARRANGE_ARRAYS + JsonDiff::COLLECT_MODIFIED_DIFF
                    );

                    if ($jsonDiff->getDiffCnt() == 0) {
                        unset($changedData[$field->name]);
                        continue;
                    }

                    $changedData[$field->name] = (array) $jsonDiff->getModifiedOriginal();
                } elseif ($field->type != 'json' && in_array($field->name, array_keys($changedData)) && in_array($field->name, array_keys($data['data']))) {

                    if ($changedData[$field->name] == $data['data'][$field->name]) {
                        unset($changedData[$field->name]);
                    }
                }
            }

            // Find and unset, if necessary, foreing keys
            foreach ($foreingKeys as $foreingKey) {
                foreach ($foreingKey->column_name as $columnName) {
                    if (!in_array($columnName, array_keys($data['data']))) {
                        unset($changedData[$columnName]);
                    }
                }
            }

            unset($changedData['updated_at']);

            $this->changedData = $changedData;
        }

        return $data;
    }

    protected function auditAfterUpdate(array $data)
    {
        foreach ($data['id'] as $sourceId) {

            $changedData = array_intersect_key($this->changedData, array_flip($this->auditableFields));

            if (!empty($changedData) || empty($this->auditableFields)) {

                $audit = [
                    'source'    => $this->table,
                    'source_id' => $sourceId,
                    'event'     => 'update',
                    'summary'   => json_encode($changedData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ];
                service('audit')->add($audit);
            }
        }

        return $data;
    }

    // record successful delete events
    protected function auditDelete(array $data)
    {
        if (! $data['result']) {
            return false;
        }
        if (empty($data['id'])) {
            return false;
        }

        $audit = [
            'source'  => $this->table,
            'event'   => 'delete',
            'summary' => ($data['purge']) ? 'purge' : 'soft',
        ];

        // add an entry for each ID
        $audit = service('audit');

        foreach ($data['id'] as $id) {
            $audit['source_id'] = $id;
            $audit->add($audit);
        }

        return $data;
    }
}
