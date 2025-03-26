<?php

namespace App\Builder\Constraints;

/**
 * Check if a value is a valid IP address
 */
class EmailFormat implements ConstraintInterface
{
    /**
     * @param array $container
     * @param string $field
     * @param bool $isCreate
     * @return bool
     */
    public function isValid(array $container, string $field, bool $isCreate = true): bool
    {
        return isset($container[$field]) && filter_var($container[$field], FILTER_VALIDATE_EMAIL);
    }
}