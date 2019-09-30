<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Adherent;
use Ramsey\Uuid\Uuid;

final class ApprovedIdeaCommentMessage extends Message
{
    public static function create(Adherent $adherent, string $ideaName, string $ideaLink): self
    {
        $message = new self(
            Uuid::uuid4(),
            '645030',
            $adherent->getEmailAddress(),
            $adherent->getFullName(),
            'Votre contribution à une proposition a été approuvée par son auteur !',
            [
                'first_name' => $adherent->getFirstName(),
                'idea_name' => $ideaName,
                'idea_link' => $ideaLink,
            ]
        );

        $message->setSenderEmail('atelier-des-idees@en-marche.fr');
        $message->setSenderName('La République En Marche !');

        return $message;
    }
}
