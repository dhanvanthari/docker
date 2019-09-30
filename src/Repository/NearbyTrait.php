<?php

namespace AppBundle\Repository;

use AppBundle\Geocoder\Coordinates;
use Doctrine\ORM\QueryBuilder;

trait NearbyTrait
{
    public function getNearbyExpression(): string
    {
        return '(6371 * acos(cos(radians(:latitude)) * cos(radians(n.postAddress.latitude))
            * cos(radians(n.postAddress.longitude) - radians(:longitude)) + sin(radians(:latitude)) *
            sin(radians(n.postAddress.latitude))))';
    }

    /**
     * Calculates the distance (in Km) between the subject entity and the provided geographical
     * points in a select statement. You can use this template to apply your constraints
     * by using the 'distance_between' attribute.
     *
     * Setting the hidden flag to false allow you to get an array as result containing
     * the entity and the calculated distance.
     *
     *
     * @return QueryBuilder
     */
    public function createNearbyQueryBuilder(Coordinates $coordinates, bool $hidden = true)
    {
        $hidden = $hidden ? 'hidden' : '';

        return $this
            ->createQueryBuilder('n')
            ->addSelect($this->getNearbyExpression().' as '.$hidden.' distance_between')
            ->setParameter('latitude', $coordinates->getLatitude())
            ->setParameter('longitude', $coordinates->getLongitude())
            ->where('n.postAddress.latitude IS NOT NULL')
            ->andWhere('n.postAddress.longitude IS NOT NULL')
            ->orderBy('distance_between', 'asc')
        ;
    }
}
