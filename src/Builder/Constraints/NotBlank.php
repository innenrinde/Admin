<?php

namespace App\Builder\Constraints;

/**
 * Check if a value is empty or not
 */
class NotBlank
{
    /**
     * @param array $container
     * @param string $field
     * @return bool
     */
    public function isValid(array $container, string $field): bool
    {
        return isset($container[$field]) && $container[$field];
    }
}