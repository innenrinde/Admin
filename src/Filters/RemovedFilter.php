<?php

namespace App\Filters;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class RemovedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        $className = $targetEntity->getReflectionClass()->name;
        if ($className !== User::class && $className !== Task::class) {
            return '';
        }

        return sprintf('%s.removed = %s', $targetTableAlias, $this->getParameter('removed'));
    }
}