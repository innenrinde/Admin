<?php

namespace App\Api;

/**
 * Manage sorting mode
 */
readonly class Sorting
{
    /**
     * @var string
     */
    public string $sortBy;

    /**
     * @var string
     */
    public string $sortDir;

    /**
     * @param array $data
     * @param array $columns
     */
    public function __construct(array $data, array $columns)
    {
        // get available columns
        $columnsFields = array_map(function($column) {
            return $column['field'];
        }, $columns);

        // detect sorting column
        $this->sortBy = isset($data['sortBy']) && in_array($data['sortBy'], $columnsFields) ? $data['sortBy'] : current($columnsFields);

        // detect sorting direction
        $this->sortDir = isset($data['sortDir']) && $data['sortDir'] === 'asc' ? 'ASC' : 'DESC';
    }
}