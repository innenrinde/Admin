<?php

namespace App\Api;

/**
 * Manage list of options to know what data will be triggered
 * 'columns' = we need list of a table columns
 * 'rows' = we need list of a table rows (supposing to have pagination)
 */
readonly class ListOptions
{
    /**
     * @var bool
     */
    public bool $withColumns;

    /**
     * @var bool
     */
    public bool $withRows;

    /**
     * @var bool
     */
    public bool $withValues;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        // list of options by key
        $list = isset($data['list']) && gettype($data['list']) === "array" ? $data['list'] : [];

        // check presence of 'columns' key
        $this->withColumns = in_array('columns', $list);

        // check presence of 'rows' key
        $this->withRows = in_array('rows', $list);

        // check presence of 'values' key
        $this->withValues = in_array('values', $list);
    }
}