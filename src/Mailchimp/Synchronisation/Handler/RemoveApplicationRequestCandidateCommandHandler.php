<?php

namespace AppBundle\Mailchimp\Synchronisation\Handler;

use AppBundle\Mailchimp\Manager;
use AppBundle\Mailchimp\Synchronisation\Command\RemoveNewsletterMemberCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveApplicationRequestCandidateCommandHandler implements MessageHandlerInterface
{
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(RemoveNewsletterMemberCommand $command): void
    {
        $this->manager->deleteApplicationRequestCandidate($command->getEmail());
    }
}
