<?php

namespace App\Builder\Constraints;

interface ConstraintInterface
{
    /**
     * @param array $container source of values
     * @param string $field field to be validated
     * @param bool $isCreate if is create action or not
     * @return bool
     */
    public function isValid(array $container, string $field, bool $isCreate = true): bool;
}