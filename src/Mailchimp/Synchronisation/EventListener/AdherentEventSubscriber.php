<?php

namespace AppBundle\Mailchimp\Synchronisation\EventListener;

use AppBundle\AdherentMessage\StaticSegmentInterface;
use AppBundle\CitizenProject\CitizenProjectFollowerChangeEvent;
use AppBundle\Committee\Event\CommitteeEventInterface;
use AppBundle\Committee\Event\FollowCommitteeEvent;
use AppBundle\Entity\Adherent;
use AppBundle\Events;
use AppBundle\Mailchimp\Synchronisation\Command\AddAdherentToStaticSegmentCommand;
use AppBundle\Mailchimp\Synchronisation\Command\AdherentChangeCommand;
use AppBundle\Mailchimp\Synchronisation\Command\AdherentDeleteCommand;
use AppBundle\Mailchimp\Synchronisation\Command\RemoveAdherentFromStaticSegmentCommand;
use AppBundle\Membership\UserEvent;
use AppBundle\Membership\UserEvents;
use AppBundle\Utils\ArrayUtils;
use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\SerializationContext;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AdherentEventSubscriber implements EventSubscriberInterface
{
    private $before = [];
    private $normalizer;
    private $bus;

    public function __construct(ArrayTransformerInterface $normalizer, MessageBusInterface $bus)
    {
        $this->normalizer = $normalizer;
        $this->bus = $bus;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Adherent creation
            UserEvents::USER_VALIDATED => 'onUserValidated',

            UserEvents::USER_BEFORE_UPDATE => 'onBeforeUpdate',
            UserEvents::USER_UPDATED => 'onAfterUpdate',
            UserEvents::USER_UPDATE_INTERESTS => 'onAfterUpdate',
            UserEvents::USER_UPDATE_SUBSCRIPTIONS => 'onAfterUpdate',
            UserEvents::USER_DELETED => 'onDelete',

            UserEvents::USER_UPDATE_COMMITTEE_PRIVILEGE => 'onCommitteePrivilegeChange',
            UserEvents::USER_UPDATE_CITIZEN_PROJECT_PRIVILEGE => 'onCitizenProjectPrivilegeChange',

            Events::CITIZEN_PROJECT_FOLLOWER_ADDED => 'onCitizenProjectMembershipCreation',
            Events::CITIZEN_PROJECT_FOLLOWER_REMOVED => 'onCitizenProjectMembershipDeletion',
        ];
    }

    public function onBeforeUpdate(UserEvent $event): void
    {
        $this->before = $this->transformToArray($event->getUser());
    }

    public function onCitizenProjectMembershipCreation(CitizenProjectFollowerChangeEvent $event): void
    {
        $this->dispatchAddAdherentToStaticSegmentCommand($event->getFollower(), $event->getCitizenProject());
    }

    public function onCitizenProjectMembershipDeletion(CitizenProjectFollowerChangeEvent $event): void
    {
        $this->dispatchRemoveAdherentFromStaticSegmentCommand($event->getFollower(), $event->getCitizenProject());
    }

    public function onAfterUpdate(UserEvent $event): void
    {
        $after = $this->transformToArray($adherent = $event->getUser());

        $changeFrom = ArrayUtils::arrayDiffRecursive($this->before, $after);
        $changeTo = ArrayUtils::arrayDiffRecursive($after, $this->before);

        if ($changeFrom || $changeTo) {
            $this->dispatchAdherentChangeCommand(
                $adherent->getUuid(),
                $changeFrom['emailAddress'] ?? $adherent->getEmailAddress(),
                isset($changeFrom['referentTagCodes']) ? (array) $changeFrom['referentTagCodes'] : []
            );
        }
    }

    public function onCitizenProjectPrivilegeChange(UserEvent $event): void
    {
        $this->dispatchAdherentChangeCommand($event->getUser()->getUuid(), $event->getUser()->getEmailAddress());
    }

    public function onCommitteePrivilegeChange(CommitteeEventInterface $event): void
    {
        $adherent = $event->getAdherent();

        $this->dispatchAdherentChangeCommand($adherent->getUuid(), $adherent->getEmailAddress());

        if (!$committee = $event->getCommittee()) {
            return;
        }

        if ($event instanceof FollowCommitteeEvent) {
            $this->dispatchAddAdherentToStaticSegmentCommand($adherent, $committee);
        } else {
            $this->dispatchRemoveAdherentFromStaticSegmentCommand($adherent, $committee);
        }
    }

    public function onDelete(UserEvent $event): void
    {
        $this->dispatch(new AdherentDeleteCommand($event->getUser()->getEmailAddress()));
    }

    public function onUserValidated(UserEvent $event): void
    {
        $adherent = $event->getUser();

        $this->dispatchAdherentChangeCommand($adherent->getUuid(), $adherent->getEmailAddress());
    }

    private function transformToArray(Adherent $adherent): array
    {
        return $this->normalizer->toArray(
            $adherent,
            SerializationContext::create()->setGroups(['adherent_change_diff'])
        );
    }

    private function dispatchAdherentChangeCommand(
        UuidInterface $uuid,
        string $identifier,
        array $removedTags = []
    ): void {
        $this->dispatch(new AdherentChangeCommand($uuid, $identifier, $removedTags));
    }

    private function dispatchAddAdherentToStaticSegmentCommand(Adherent $adherent, StaticSegmentInterface $object): void
    {
        $this->dispatch(new AddAdherentToStaticSegmentCommand(
            $adherent->getUuid(),
            $object->getUuid(),
            \get_class($object)
        ));
    }

    private function dispatchRemoveAdherentFromStaticSegmentCommand(
        Adherent $adherent,
        StaticSegmentInterface $object
    ): void {
        $this->dispatch(new RemoveAdherentFromStaticSegmentCommand(
            $adherent->getUuid(),
            $object->getUuid(),
            \get_class($object)
        ));
    }

    private function dispatch($command): void
    {
        $this->bus->dispatch($command);
    }
}
