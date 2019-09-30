<?php

namespace AppBundle\Deputy;

use AppBundle\Entity\DeputyManagedUsersMessage;
use AppBundle\Producer\DeputyMessageDispatcherProducerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DeputyMessageNotifier
{
    private $manager;
    private $producer;

    public function __construct(ObjectManager $manager, DeputyMessageDispatcherProducerInterface $producer)
    {
        $this->manager = $manager;
        $this->producer = $producer;
    }

    public function sendMessage(DeputyMessage $message): void
    {
        $this->manager->persist(DeputyManagedUsersMessage::createFromMessage($message));
        $this->manager->flush();

        $this->producer->scheduleDispatch($message);
    }
}
