<?php

namespace App\Api\Constraints;

/**
 * Check if a value is empty or not
 */
class NullForCreation implements ConstraintInterface
{
    /**
     * @param array $container
     * @param string $field
     * @param bool $isCreate
     * @return bool
     */
    public function isValid(array $container, string $field, bool $isCreate = true): bool
    {
        if (!$isCreate) {
            return isset($container[$field]) && $container[$field];
        }

        return true;
    }
}