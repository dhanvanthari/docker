<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\CitizenProject;
use Ramsey\Uuid\Uuid;

class CitizenProjectCreationNotificationMessage extends Message
{
    public static function create(Adherent $adherent, CitizenProject $citizenProject, Adherent $creator): self
    {
        $message = new self(
            Uuid::uuid4(),
            '263111',
            $adherent->getEmailAddress(),
            $adherent->getFullName(),
            'Un projet citoyen se lance près de chez vous !',
            [
                'target_firstname' => self::escape($adherent->getFirstName()),
                'citizen_project_name' => self::escape($citizenProject->getName()),
                'citizen_project_host_firstname' => self::escape($creator->getFirstName()),
                'citizen_project_host_lastname' => self::escape($creator->getLastName()),
                'citizen_project_slug' => self::escape($citizenProject->getSlug()),
            ]
        );

        $message->setSenderEmail('projetscitoyens@en-marche.fr');

        return $message;
    }
}
