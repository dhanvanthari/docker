<?php

namespace AppBundle\Statistics\Acquisition\Calculator;

use AppBundle\Entity\Event;
use AppBundle\Repository\EventRegistrationRepository;
use AppBundle\Statistics\Acquisition\Calculator\Category\AdhesionCategoryTrait;
use AppBundle\Statistics\Acquisition\StatisticsRequest;
use Doctrine\ORM\Query\Expr\Join;

abstract class AbstractEventSubscriptionCalculator extends AbstractCalculator
{
    use AdhesionCategoryTrait;

    private $repository;

    public function __construct(EventRegistrationRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function processing(StatisticsRequest $request, array $keys): array
    {
        $total = $this->getTotalInitial($request);

        return array_map(
            function (int $totalByMonth) use (&$total) {
                return $total += $totalByMonth;
            },
            $this->fillEmptyCase($this->getNewCounters($request), $keys)
        );
    }

    private function getTotalInitial(StatisticsRequest $request): int
    {
        return (int) $this->repository
            ->createQueryBuilder('event_registration')
            ->select('COUNT(1) AS total')
            ->innerJoin(Event::class, 'event', Join::WITH, 'event_registration.event = event')
            ->innerJoin('event.referentTags', 'tags')
            ->where('event_registration.createdAt < :date')
            ->andWhere(sprintf('event_registration.adherentUuid %s', $this->isAdherentOnly() ? 'IS NOT NULL' : 'IS NULL'))
            ->andWhere('tags.code IN (:tags)')
            ->setParameters([
                'date' => $request->getStartDateAsString(),
                'tags' => $request->getTags(),
            ])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    private function getNewCounters(StatisticsRequest $request): array
    {
        return $this->repository
            ->createQueryBuilder('event_registration')
            ->select('COUNT(1) AS total')
            ->addSelect('YEAR_MONTH(event_registration.createdAt) AS date')
            ->innerJoin(Event::class, 'event', Join::WITH, 'event_registration.event = event')
            ->innerJoin('event.referentTags', 'tags')
            ->where('event_registration.createdAt >= :start_date AND event_registration.createdAt <= :end_date')
            ->andWhere(sprintf('event_registration.adherentUuid %s', $this->isAdherentOnly() ? 'IS NOT NULL' : 'IS NULL'))
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

    abstract protected function isAdherentOnly(): bool;
}
