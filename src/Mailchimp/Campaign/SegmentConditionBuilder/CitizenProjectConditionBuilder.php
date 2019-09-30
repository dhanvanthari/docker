<?php

namespace AppBundle\Mailchimp\Campaign\SegmentConditionBuilder;

use AppBundle\AdherentMessage\Filter\AdherentMessageFilterInterface;
use AppBundle\Entity\AdherentMessage\Filter\CitizenProjectFilter;
use AppBundle\Mailchimp\Exception\InvalidFilterException;
use AppBundle\Mailchimp\Exception\StaticSegmentIdMissingException;

class CitizenProjectConditionBuilder extends AbstractStaticSegmentConditionBuilder
{
    public function support(AdherentMessageFilterInterface $filter): bool
    {
        return $filter instanceof CitizenProjectFilter;
    }

    /**
     * @var CitizenProjectFilter
     */
    protected function getSegmentId(AdherentMessageFilterInterface $filter): int
    {
        if (!$citizenProject = $filter->getCitizenProject()) {
            throw new InvalidFilterException($filter->getMessage(), '[AdherentMessage] Citizen project should not be empty');
        }

        if (!$citizenProject->getMailchimpId()) {
            throw new StaticSegmentIdMissingException(
                sprintf('[AdherentMessage] Citizen project "%s" does not have mailchimp ID', $citizenProject->getUuidAsString())
            );
        }

        return $citizenProject->getMailchimpId();
    }
}
