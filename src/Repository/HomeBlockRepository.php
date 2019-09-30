<?php

namespace AppBundle\Repository;

use AppBundle\Entity\HomeBlock;
use Doctrine\ORM\EntityRepository;

class HomeBlockRepository extends EntityRepository
{
    /**
     * @return HomeBlock[]
     */
    public function findHomeBlocks()
    {
        return $this->createQueryBuilder('h')
            ->select('h', 'm')
            ->leftJoin('h.media', 'm')
            ->orderBy('h.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
