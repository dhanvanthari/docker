<?php

namespace AppBundle\Mailchimp\Webhook\Handler;

use AppBundle\Mailchimp\Webhook\EventTypeEnum;
use AppBundle\Membership\AdherentChangeEmailHandler;

class AdherentEmailUpdateHandler extends AbstractAdherentHandler
{
    private $adherentChangeEmailHandler;

    public function __construct(AdherentChangeEmailHandler $adherentChangeEmailHandler)
    {
        $this->adherentChangeEmailHandler = $adherentChangeEmailHandler;
    }

    public function handle(array $data): void
    {
        if ($adherent = $this->getAdherent($data['old_email'])) {
            $this->adherentChangeEmailHandler->handleRequest($adherent, $data['new_email']);
        }
    }

    public function support(string $type, string $listId): bool
    {
        return EventTypeEnum::UPDATE_EMAIL === $type && parent::support($type, $listId);
    }
}
