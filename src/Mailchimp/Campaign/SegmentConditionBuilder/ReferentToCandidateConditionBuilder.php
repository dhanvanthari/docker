<?php

namespace AppBundle\Mailchimp\Campaign\SegmentConditionBuilder;

use AppBundle\AdherentMessage\Filter\AdherentMessageFilterInterface;
use AppBundle\Entity\AdherentMessage\Filter\ReferentUserFilter;
use AppBundle\Entity\AdherentMessage\MailchimpCampaign;
use AppBundle\Mailchimp\Exception\InvalidFilterException;
use AppBundle\Mailchimp\Synchronisation\ApplicationRequestTagLabelEnum;
use AppBundle\Mailchimp\Synchronisation\Request\MemberRequest;

class ReferentToCandidateConditionBuilder extends AbstractConditionBuilder
{
    public function support(AdherentMessageFilterInterface $filter): bool
    {
        return $filter instanceof ReferentUserFilter
            && ($filter->getContactOnlyVolunteers() || $filter->getContactOnlyRunningMates())
        ;
    }

    public function build(MailchimpCampaign $campaign): array
    {
        if (!$campaign->getLabel()) {
            throw new InvalidFilterException(
                $campaign->getMessage(),
                '[ReferentMessage] Referent message does not have a valid tag code'
            );
        }

        $conditions[] = [
            'condition_type' => 'TextMerge',
            'op' => 'contains',
            'field' => MemberRequest::MERGE_FIELD_REFERENT_TAGS,
            'value' => $campaign->getLabel(),
        ];

        /** @var ReferentUserFilter $filter */
        $filter = $campaign->getMessage()->getFilter();

        if ($filter->getContactOnlyRunningMates() ^ $filter->getContactOnlyVolunteers()) {
            $conditions[] = $this->buildStaticSegmentCondition(
                $this->mailchimpObjectIdMapping->getApplicationRequestTagIds()[
                $filter->getContactOnlyRunningMates()
                    ? ApplicationRequestTagLabelEnum::RUNNING_MATE
                    : ApplicationRequestTagLabelEnum::VOLUNTEER
                ]
            );
        }

        return $conditions;
    }
}
