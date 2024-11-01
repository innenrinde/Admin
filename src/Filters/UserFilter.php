<?php

namespace App\Filters;

use App\Entity\User;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class UserFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->getReflectionClass()->name !== User::class) {
            return '';
        }

        return sprintf('%s.removed = %s', $targetTableAlias, $this->getParameter('removed'));
    }
}