<?php

namespace AppBundle\Repository\Jecoute;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\Jecoute\LocalSurvey;
use AppBundle\Repository\ReferentTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LocalSurveyRepository extends ServiceEntityRepository
{
    use ReferentTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LocalSurvey::class);
    }

    /**
     * @return LocalSurvey[]
     */
    public function findAllByAdherent(Adherent $adherent): array
    {
        return $this
            ->createSurveysForAdherentQueryBuilder($adherent)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByTags(array $tags): array
    {
        $qb = $this
            ->createQueryBuilder('survey')
            ->addSelect('questions')
            ->innerJoin('survey.questions', 'questions')
        ;

        return $qb
            ->andWhere($this->createOrExpressionForSurveyTags($qb, $tags))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Adherent|UserInterface $adherent
     */
    public function createSurveysForAdherentQueryBuilder(Adherent $adherent): QueryBuilder
    {
        $qb = $this
            ->createQueryBuilder('survey')
            ->addSelect('questions')
            ->innerJoin('survey.questions', 'questions')
        ;

        return $qb
            ->where($this->createOrExpressionForSurveyTags($qb, $adherent->getReferentTagCodes()))
            ->andWhere('survey.published = true')
        ;
    }

    public function createOrExpressionForSurveyTags(QueryBuilder $qb, array $tags): Orx
    {
        $expression = new Orx();

        foreach ($tags as $key => $tag) {
            $expression->add("FIND_IN_SET(:tags_$key, survey.tags) > 0");
            $qb->setParameter("tags_$key", $tag);
        }

        return $expression;
    }

    public function findAllByAuthor(Adherent $adherent): array
    {
        return $this
            ->createQueryBuilder('survey')
            ->addSelect('questions')
            ->innerJoin('survey.questions', 'questions')
            ->where('survey.author = :author')
            ->setParameter('author', $adherent)
            ->getQuery()
            ->getResult()
        ;
    }
}
