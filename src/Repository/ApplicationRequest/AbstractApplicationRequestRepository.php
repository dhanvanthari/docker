<?php

namespace AppBundle\Repository\ApplicationRequest;

use AppBundle\ApplicationRequest\Filter\ListFilter;
use AppBundle\Entity\Adherent;
use AppBundle\Entity\ApplicationRequest\ApplicationRequest;
use AppBundle\Entity\ApplicationRequest\RunningMateRequest;
use AppBundle\Entity\ApplicationRequest\VolunteerRequest;
use AppBundle\Entity\ReferentTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractApplicationRequestRepository extends ServiceEntityRepository
{
    /**
     * @var ReferentTag[]
     *
     * @return VolunteerRequest[]|RunningMateRequest[]
     */
    public function findForReferentTags(array $referentTags, ListFilter $filter = null): array
    {
        return $this->createListQueryBuilder('r', $filter)
            ->innerJoin('r.referentTags', 'refTag')
            ->andWhere('refTag IN (:tags)')
            ->setParameter('tags', $referentTags)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return VolunteerRequest|RunningMateRequest|null
     */
    public function findOneByUuid(string $uuid): ?ApplicationRequest
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    /**
     * @return VolunteerRequest[]|RunningMateRequest[]
     */
    public function findAllForInseeCodes(array $inseeCodes, ListFilter $filter = null): array
    {
        $this->addFavoriteCitiesCondition($inseeCodes, $qb = $this->createListQueryBuilder('r', $filter));

        return $qb->getQuery()->getResult();
    }

    public function countForInseeCodes(array $inseeCodes): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(1)')
            ->where('r.displayed = true')
        ;

        $this->addFavoriteCitiesCondition($inseeCodes, $qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countTakenFor(array $inseeCodes): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(1)')
            ->where('r.displayed = true')
            ->andWhere('r.takenForCity IN (:cities)')
            ->setParameter('cities', $inseeCodes)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return VolunteerRequest[]|RunningMateRequest[]
     */
    public function findAllTakenFor(string $inseeCode, ListFilter $filter = null): array
    {
        return $this->createListQueryBuilder('r', $filter)
            ->andWhere('r.takenForCity = :insee_code')
            ->setParameter('insee_code', $inseeCode)
            ->getQuery()
            ->getResult()
        ;
    }

    public function updateAdherentRelation(string $email, ?Adherent $adherent): void
    {
        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'candidate')
            ->where('candidate.emailAddress = :email')
            ->set('candidate.adherent', ':adherent')
            ->setParameter('email', $email)
            ->setParameter('adherent', $adherent)
            ->getQuery()
            ->execute()
        ;
    }

    public function hideDuplicates(ApplicationRequest $request): void
    {
        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'candidate')
            ->where('candidate.id != :id')
            ->andWhere('candidate.emailAddress = :email')
            ->andWhere('candidate.displayed = true')
            ->set('candidate.displayed', 0)
            ->setParameter('id', $request->getId())
            ->setParameter('email', $request->getEmailAddress())
            ->getQuery()
            ->execute()
        ;
    }

    private function createListQueryBuilder(string $alias, ListFilter $filter = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias)
            ->addSelect('tag')
            ->where("$alias.displayed = true")
            ->leftJoin("$alias.tags", 'tag')
            ->orderBy("$alias.createdAt", 'DESC')
        ;

        if ($filter) {
            $this->applyListFilter($qb, $filter);
        }

        return $qb;
    }

    private function addFavoriteCitiesCondition(array $inseeCodes, QueryBuilder $qb): void
    {
        $orExpression = new Orx();

        foreach ($inseeCodes as $key => $code) {
            $orExpression->add("FIND_IN_SET(:codes_$key, r.favoriteCities) > 0");
            $qb->setParameter("codes_$key", $code);
        }

        $qb->andWhere($orExpression);
    }

    private function applyListFilter(QueryBuilder $qb, ListFilter $filter): void
    {
        $alias = $qb->getRootAliases()[0];

        if ($filter->getFirstName()) {
            $qb
                ->andWhere("${alias}.firstName = :first_name")
                ->setParameter('first_name', $filter->getFirstName())
            ;
        }

        if ($filter->getLastName()) {
            $qb
                ->andWhere("${alias}.lastName = :last_name")
                ->setParameter('last_name', $filter->getLastName())
            ;
        }

        if ($filter->getGender()) {
            $qb
                ->andWhere("${alias}.gender = :gender")
                ->setParameter('gender', $filter->getGender())
            ;
        }

        if (null !== $filter->isAdherent()) {
            if ($filter->isAdherent()) {
                $qb->andWhere("${alias}.adherent IS NOT NULL");
            } else {
                $qb->andWhere("${alias}.adherent IS NULL");
            }
        }

        if ($filter->getIsInMyTeam()) {
            // `No` value, free candidate
            if (2 === $filter->getIsInMyTeam()) {
                $qb->andWhere("${alias}.takenForCity IS NULL");
            } else {
                // `Yes` or `Taken for another city` values
                $sign = 1 === $filter->getIsInMyTeam() ? 'IN' : 'NOT IN';
                $qb
                    ->andWhere("${alias}.takenForCity ${sign}(:insee_codes)")
                    ->setParameter('insee_codes', $filter->getInseeCodes())
                ;
            }
        }

        if ($filter->getTag()) {
            $qb
                ->leftJoin("${alias}.tags", 'tag_for_search')
                ->andWhere('tag_for_search = :tag')
                ->setParameter('tag', $filter->getTag())
            ;
        }

        if ($filter->getTheme()) {
            $qb
                ->innerJoin("${alias}.favoriteThemes", 'theme')
                ->andWhere('theme = :theme')
                ->setParameter('theme', $filter->getTheme())
            ;
        }
    }
}
