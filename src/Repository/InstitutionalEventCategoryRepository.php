<?php

namespace AppBundle\Repository;

use AppBundle\Entity\InstitutionalEventCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class InstitutionalEventCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InstitutionalEventCategory::class);
    }
}
