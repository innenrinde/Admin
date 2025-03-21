<?php

namespace App\Services;

use App\Entity\Category;

class TableBuilder
{
    /**
     * @param array $columns
     * @return array
     */
    public function getColumns(array $columns): array
    {
        return array_map(function ($column) {
            $column['type'] = (new $column['type']())->getBlockPrefix();
            return $column;
        }, $columns);
    }

    /**
     * @param string $type
     * @param array $columns
     * @param array $data
     * @return Category
     */
    public function create(string $type, array $columns, array $data): Category
    {
        $entity = new $type;
        $entity->setTitle($data['title']);

        return $entity;
    }
}