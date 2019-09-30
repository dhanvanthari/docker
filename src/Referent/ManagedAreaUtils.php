<?php

namespace AppBundle\Referent;

use AppBundle\Entity\EntityPostAddressInterface;
use AppBundle\Utils\AreaUtils;

class ManagedAreaUtils extends AreaUtils
{
    public static function getLocalCodes(EntityPostAddressInterface $entity): array
    {
        $localCode = static::getLocalCode($entity);

        return array_merge([$localCode], static::getRelatedCodes($localCode));
    }

    public static function getLocalCode(EntityPostAddressInterface $entity): string
    {
        if (self::CODE_FRANCE === $entity->getCountry()) {
            return static::getCodeFromPostalCode($entity->getPostalCode());
        }

        return static::getCodeFromCountry($entity->getCountry());
    }
}
