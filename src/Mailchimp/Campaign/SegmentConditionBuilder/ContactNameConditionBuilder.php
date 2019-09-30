<?php

namespace AppBundle\Mailchimp\Campaign\SegmentConditionBuilder;

use AppBundle\AdherentMessage\Filter\AdherentMessageFilterInterface;
use AppBundle\Entity\AdherentMessage\Filter\MunicipalChiefFilter;
use AppBundle\Entity\AdherentMessage\Filter\ReferentUserFilter;
use AppBundle\Entity\AdherentMessage\MailchimpCampaign;
use AppBundle\Mailchimp\Synchronisation\Request\MemberRequest;

class ContactNameConditionBuilder implements SegmentConditionBuilderInterface
{
    public function support(AdherentMessageFilterInterface $filter): bool
    {
        return
            $filter instanceof ReferentUserFilter
            || ($filter instanceof MunicipalChiefFilter && !$filter->getContactNewsletter())
        ;
    }

    public function build(MailchimpCampaign $campaign): array
    {
        /** @var MunicipalChiefFilter|ReferentUserFilter $filter */
        $filter = $campaign->getMessage()->getFilter();

        $conditions = [];

        if ($filter->getGender()) {
            $conditions[] = [
                'condition_type' => 'TextMerge',
                'op' => 'is',
                'field' => MemberRequest::MERGE_FIELD_GENDER,
                'value' => $filter->getGender(),
            ];
        }

        if ($filter->getFirstName()) {
            $conditions[] = [
                'condition_type' => 'TextMerge',
                'op' => 'is',
                'field' => MemberRequest::MERGE_FIELD_FIRST_NAME,
                'value' => $filter->getFirstName(),
            ];
        }

        if ($filter->getLastName()) {
            $conditions[] = [
                'condition_type' => 'TextMerge',
                'op' => 'is',
                'field' => MemberRequest::MERGE_FIELD_LAST_NAME,
                'value' => $filter->getLastName(),
            ];
        }

        return $conditions;
    }
}
