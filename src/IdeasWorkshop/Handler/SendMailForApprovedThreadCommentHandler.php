<?php

namespace AppBundle\IdeasWorkshop\Handler;

use AppBundle\Entity\IdeasWorkshop\BaseComment;
use AppBundle\Entity\IdeasWorkshop\Thread;
use AppBundle\Entity\IdeasWorkshop\ThreadComment;
use AppBundle\IdeasWorkshop\Command\SendMailForApprovedThreadCommentCommand;
use AppBundle\Mailer\MailerService;
use AppBundle\Mailer\Message\ApprovedIdeaCommentMessage;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendMailForApprovedThreadCommentHandler implements MessageHandlerInterface
{
    private $mailer;
    private $urlGenerator;

    public function __construct(MailerService $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(SendMailForApprovedThreadCommentCommand $command): void
    {
        $comment = $command->getComment();

        $this->mailer->sendMessage(ApprovedIdeaCommentMessage::create(
            $comment->getAuthor(),
            $this->getIdeaName($comment),
            $this->urlGenerator->generate(
                'react_app_ideas_workshop_proposition',
                ['id' => $this->getIdeaUuid($comment)],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        ));
    }

    private function getIdeaName(BaseComment $comment): string
    {
        if ($comment instanceof Thread) {
            return $comment->getIdea()->getName();
        }

        if ($comment instanceof ThreadComment) {
            return $comment->getThread()->getIdea()->getName();
        }

        return '';
    }

    private function getIdeaUuid(BaseComment $comment): ?UuidInterface
    {
        if ($comment instanceof Thread) {
            return $comment->getIdea()->getUuid();
        }

        if ($comment instanceof ThreadComment) {
            return $comment->getThread()->getIdea()->getUuid();
        }

        return null;
    }
}
