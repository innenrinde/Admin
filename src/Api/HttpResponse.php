<?php

namespace App\Api;

class HttpResponse
{
    private ?array $columns = null;

    private ?array $rows = null;

    private ?array $values = null;

    private ?array $pager = null;

    /**
     * @param array|null $columns
     */
    public function setColumns(?array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return array|null
     */
    public function getColumns(): ?array
    {
        return $this->columns;
    }

    /**
     * @return bool
     */
    public function hasColumns(): bool
    {
        return $this->columns && count($this->columns) > 0;
    }

    /**
     * @param array $data
     * @param callable $parseRow
     */
    public function setRows(array $data, callable $parseRow): void
    {
        $this->rows = array_map($parseRow, $data['rows']);
        $this->pager = $data['pager'];
    }

    /**
     * @return array|null
     */
    public function getRows(): ?array
    {
        return $this->rows;
    }

    /**
     * @return bool
     */
    public function hasRows(): bool
    {
        return $this->rows && count($this->rows) > 0;
    }

    /**
     * @return array|null
     */
    public function getPager(): ?array
    {
        return $this->pager;
    }

    /**
     * @return bool
     */
    public function hasPager(): bool
    {
        return $this->pager !== null;
    }

    /**
     * @param array $data
     */
    public function setValues(array $data): void
    {
        $this->values = $data;
    }

    /**
     * @return array|null
     */
    public function getValues(): ?array
    {
        return $this->values;
    }

    /**
     * @return bool
     */
    public function hasValues(): bool
    {
        return $this->values && count($this->values) > 0;
    }
}