<?php

namespace App\Builder\Constraints;

/**
 * Check if a value is a valid IP address
 */
class IpFormat
{
    /**
     * @param array $container
     * @param string $field
     * @return bool
     */
    public function isValid(array $container, string $field): bool
    {
        return isset($container[$field]) && preg_match('/^(\d+\.)(\d+\.)(\d+\.)(\d+)$/', $container[$field]);
    }
}