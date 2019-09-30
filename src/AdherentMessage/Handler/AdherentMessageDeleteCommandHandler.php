<?php

namespace AppBundle\AdherentMessage\Handler;

use AppBundle\AdherentMessage\Command\AdherentMessageDeleteCommand;
use AppBundle\Mailchimp\Manager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdherentMessageDeleteCommandHandler implements MessageHandlerInterface
{
    private $mailchimpManager;

    public function __construct(Manager $mailchimpManager)
    {
        $this->mailchimpManager = $mailchimpManager;
    }

    public function __invoke(AdherentMessageDeleteCommand $command): void
    {
        $this->mailchimpManager->deleteCampaign($command->getCampaignId());
    }
}
