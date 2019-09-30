<?php

namespace AppBundle\Mailchimp\Campaign\Listener;

use AppBundle\AdherentMessage\AdherentMessageTypeEnum;
use AppBundle\Mailchimp\Events;
use AppBundle\Mailchimp\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateCampaignSubjectSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::CAMPAIGN_PRE_EDIT => 'preEdit',
        ];
    }

    public function preEdit(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $message = $event->getMessage();

        switch ($message->getType()) {
            case AdherentMessageTypeEnum::DEPUTY:
                $prefix = 'Député';
                break;
            case AdherentMessageTypeEnum::COMMITTEE:
                $prefix = 'Comité';
                break;
            case AdherentMessageTypeEnum::REFERENT:
                $prefix = 'Référent';
                break;
            case AdherentMessageTypeEnum::CITIZEN_PROJECT:
                $prefix = 'Projet citoyen';
                break;
            case AdherentMessageTypeEnum::MUNICIPAL_CHIEF:
                $prefix = 'Municipales 2020';
                break;
            default:
                $prefix = '';
        }

        if ($prefix) {
            $request->setSubject(sprintf('[%s] %s', $prefix, $message->getSubject()));
        }
    }
}
