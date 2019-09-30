<?php

namespace AppBundle\Doctrine\Filter;

use AppBundle\Entity\EnabledInterface;
use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class EnabledFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->reflClass->implementsInterface(EnabledInterface::class)) {
            return '';
        }

        return $targetTableAlias.'.enabled = 1';
    }
}
