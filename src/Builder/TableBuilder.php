<?php

namespace App\Builder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

readonly class TableBuilder
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $userPasswordHasher
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
        $this->validateColumns($columns, $data, false);

        $entity = $this->em->getRepository($type)->find($data['id']);
        $this->fillData($entity, $columns, $data);

        return $entity;
    }

    /**
     * Validate all columns by defined constraints
     * @param array $columns
     * @param array $data
     * @param bool $isCreate
     * @return void
     * @throws \Exception
     */
    private function validateColumns(array $columns, array $data, bool $isCreate = true): void
    {
        foreach ($columns as $column) {
            if (isset($column['constraints'])) {
                $this->validateColumn($column, $data, $isCreate);
            }
        }
    }

    /**
     * Validate a column by defined constraints
     * @param array $column
     * @param array $data
     * @param bool $isCreate
     * @return void
     * @throws \Exception
     */
    private function validateColumn(array $column, array $data, bool $isCreate): void
    {
        foreach ($column['constraints'] as $constraint => $message) {
            if (!(new $constraint())->isValid($data, $column['field'], $isCreate)) {
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
     * @throws \DateMalformedStringException
     */
    private function fillData(object $entity, array $columns, array $data): void
    {
        foreach ($columns as $column) {
            if (isset($column['isPk'])) {
                continue;
            }

            $value = match ($column['type']) {
                ChoiceType::class => $this->choiceType($column, $data),
                CollectionType::class => $this->collectionType($column, $data),
                DateTimeType::class => $this->dateTimeType($column, $data),
                PasswordType::class => $this->passwordType($column, $data, $entity),
                default => $data[$column['field']] ?? "",
            };

            $entity->{'set' . ucFirst($column['field'])}($value);

        }
    }

    /**
     * We suppose that a column of choice type is a reference to an entity
     * So we will try to find an existing entity
     * @param array $column
     * @param array $data
     * @return object|null
     */
    private function choiceType(array $column, array $data): object|null
    {
        if (isset($column['entity'])) {
            return $this->em->getRepository($column['entity'])->find($data[$column['field']] ?? "");
        }

        return null;
    }

    /**
     * Value for a column 'collection' is an array
     * @param array $column
     * @param array $data
     * @return array
     */
    private function collectionType(array $column, array $data): array
    {
        $value = $data[$column['field']] ?? "";

        return is_array($value) ? $value : explode(',', $value);
    }

    /**
     * Value for a column 'dateTime' is an object
     * @param array $column
     * @param array $data
     * @return \DateTime
     * @throws \DateMalformedStringException
     */
    private function dateTimeType(array $column, array $data): \DateTime
    {
        $value = $data[$column['field']] ?? "";

        return new \DateTime($value);
    }

    /**
     * Generate a password
     * @param array $column
     * @param array $data
     * @param PasswordAuthenticatedUserInterface $user
     * @return string
     */
    private function passwordType(array $column, array $data, PasswordAuthenticatedUserInterface $user): string
    {
        $plainPassword = $data[$column['field']] ?? null;
        if ($plainPassword) {
            return $this->userPasswordHasher->hashPassword($user, $plainPassword);
        }

        return "";
    }
}