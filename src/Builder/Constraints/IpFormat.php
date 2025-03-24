<?php

namespace App\Builder\Constraints;

/**
 * Check if a value is a valid IP address
 */
class IpFormat implements ConstraintInterface
{
    /**
     * @param array $container
     * @param string $field
     * @param bool $isCreate
     * @return bool
     */
    public function isValid(array $container, string $field, bool $isCreate = true): bool
    {
        return isset($container[$field]) && preg_match('/^(\d+\.)(\d+\.)(\d+\.)(\d+)$/', $container[$field]);
    }
}