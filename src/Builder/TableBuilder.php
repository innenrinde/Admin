<?php

namespace App\Builder;

use Doctrine\ORM\EntityManagerInterface;

readonly class TableBuilder
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    /**
     * Return columns with true types used for table component
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
     * Create a new table line
     * @param string $type
     * @param array $columns
     * @param array $data
     * @return mixed|null
     * @throws \Exception
     */
    public function create(string $type, array $columns, array $data): object|null
    {
        $this->validateColumns($columns, $data);

        $entity = new $type;
        $this->fillData($entity, $columns, $data);

        return $entity;
    }

    /**
     * Save data for an existing table line
     * @param string $type
     * @param array $columns
     * @param array $data
     * @return mixed|null
     * @throws \Exception
     */
    public function save(string $type, array $columns, array $data): object|null
    {
        $this->validateColumns($columns, $data);

        $entity = $this->em->getRepository($type)->find($data['id']);
        $this->fillData($entity, $columns, $data);

        return $entity;
    }

    /**
     * Validate all columns by defined constraints
     * @param array $columns
     * @param array $data
     * @return void
     * @throws \Exception
     */
    private function validateColumns(array $columns, array $data): void
    {
        foreach ($columns as $column) {
            if (isset($column['constraints'])) {
                $this->validateColumn($column, $data);
            }
        }
    }

    /**
     * Validate a column by defined constraints
     * @param array $column
     * @param array $data
     * @return void
     * @throws \Exception
     */
    private function validateColumn(array $column, array $data): void
    {
        foreach ($column['constraints'] as $constraint => $message) {
            if (!(new $constraint())->isValid($data, $column['field'])) {
                throw new \Exception($message);
            }
        }
    }

    /**
     * Fill entity data
     * @param object $entity
     * @param array $columns
     * @param array $data
     * @return void
     */
    private function fillData(object $entity, array $columns, array $data): void
    {
        foreach ($columns as $column) {
            if (!isset($column['isPk'])) {
                $entity->{'set'.ucFirst($column['field'])}($data[$column['field']]);
            }
        }
    }
}