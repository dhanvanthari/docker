<?php

namespace AppBundle\Statistics\Acquisition\Calculator;

use AppBundle\Repository\UnregistrationRepository;
use AppBundle\Statistics\Acquisition\Calculator\Category\AdhesionCategoryTrait;
use AppBundle\Statistics\Acquisition\StatisticsRequest;

class UnsubscribeAdherentsCalculator extends AbstractCalculator
{
    use AdhesionCategoryTrait;

    private $repository;

    public function __construct(UnregistrationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getLabel(): string
    {
        return 'Desadhésions (nouveaux)';
    }

    protected function processing(StatisticsRequest $request, array $keys): array
    {
        return $this->repository
            ->createQueryBuilder('adherent')
            ->select('COUNT(1) AS total')
            ->addSelect('YEAR_MONTH(adherent.unregisteredAt) AS date')
            ->innerJoin('adherent.referentTags', 'tags')
            ->where('adherent.unregisteredAt >= :start_date AND adherent.unregisteredAt <= :end_date')
            ->andWhere('tags.code IN (:tags)')
            ->setParameters([
                'start_date' => $request->getStartDateAsString(),
                'end_date' => $request->getEndDateAsString(),
                'tags' => $request->getTags(),
            ])
            ->groupBy('date')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
