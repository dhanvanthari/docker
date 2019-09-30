<?php

namespace AppBundle\AdherentMessage\Listener;

use AppBundle\AdherentMessage\Command\CreateStaticSegmentCommand;
use AppBundle\AdherentMessage\StaticSegmentInterface;
use AppBundle\CitizenProject\CitizenProjectWasUpdatedEvent;
use AppBundle\Committee\CommitteeEvent;
use AppBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateStaticSegmentListener implements EventSubscriberInterface
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::COMMITTEE_UPDATED => 'onCommitteeUpdate',
            Events::CITIZEN_PROJECT_UPDATED => 'onCitizenProjectUpdate',
        ];
    }

    public function onCitizenProjectUpdate(CitizenProjectWasUpdatedEvent $event): void
    {
        if (($citizenProject = $event->getCitizenProject())->isApproved()) {
            $this->onUpdate($citizenProject);
        }
    }

    public function onCommitteeUpdate(CommitteeEvent $event): void
    {
        if (($committee = $event->getCommittee())->isApproved()) {
            $this->onUpdate($committee);
        }
    }

    private function onUpdate(StaticSegmentInterface $object): void
    {
        if (!$object->getMailchimpId()) {
            $this->bus->dispatch(new CreateStaticSegmentCommand($object->getUuid(), \get_class($object)));
        }
    }
}
