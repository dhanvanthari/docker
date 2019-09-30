<?php

namespace AppBundle\Statistics\Acquisition\Calculator;

use AppBundle\Entity\Event;
use AppBundle\Repository\EventRepository;
use AppBundle\Statistics\Acquisition\Calculator\Category\AdhesionCategoryTrait;
use AppBundle\Statistics\Acquisition\StatisticsRequest;

class EventCalculator extends AbstractCalculator
{
    use AdhesionCategoryTrait;

    private $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getLabel(): string
    {
        return 'Événements (nouveaux)';
    }

    protected function processing(StatisticsRequest $request, array $keys): array
    {
        return $this->repository
            ->createQueryBuilder('event')
            ->select('COUNT(1) AS total')
            ->addSelect('YEAR_MONTH(event.beginAt) AS date')
            ->innerJoin('event.referentTags', 'tags')
            ->where('event.beginAt >= :start_date AND event.beginAt <= :end_date')
            ->andWhere('event.status = :status')
            ->andWhere('tags.code IN (:tags)')
            ->setParameters([
                'start_date' => $request->getStartDateAsString(),
                'end_date' => $request->getEndDateAsString(),
                'status' => Event::STATUS_SCHEDULED,
                'tags' => $request->getTags(),
            ])
            ->groupBy('date')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
