<?php

namespace AppBundle\AdherentMessage;

use AppBundle\Entity\AdherentMessage\AdherentMessageInterface;
use AppBundle\Entity\AdherentMessage\CommitteeAdherentMessage;

class CommitteeAdherentMessageDataObject extends AdherentMessageDataObject
{
    /**
     * @var bool
     */
    private $sendToTimeline = false;

    public function isSendToTimeline(): bool
    {
        return $this->sendToTimeline;
    }

    public function setSendToTimeline(bool $sendToTimeline): void
    {
        $this->sendToTimeline = $sendToTimeline;
    }

    public static function createFromEntity(AdherentMessageInterface $message): AdherentMessageDataObject
    {
        /** @var self $dataObject */
        $dataObject = parent::createFromEntity($message);

        if ($message instanceof CommitteeAdherentMessage) {
            $dataObject->sendToTimeline = $message->isSendToTimeline();
        }

        return $dataObject;
    }
}
