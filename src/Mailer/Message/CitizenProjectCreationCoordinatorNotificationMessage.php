<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\CitizenProject;
use Ramsey\Uuid\Uuid;

class CitizenProjectCreationCoordinatorNotificationMessage extends Message
{
    public static function create(
        Adherent $coordinator,
        CitizenProject $citizenProject,
        Adherent $creator,
        string $coordinatorSpaceUrl
    ): self {
        $message = new self(
            Uuid::uuid4(),
            '302629',
            $coordinator->getEmailAddress(),
            $coordinator->getFullName(),
            '[Projet citoyen] Un nouveau projet citoyen attend votre validation !',
            [
                'target_firstname' => self::escape($coordinator->getFirstName()),
                'citizen_project_name' => self::escape($citizenProject->getName()),
                'citizen_project_host_firstname' => self::escape($creator->getFirstName()),
                'citizen_project_host_lastname' => self::escape($creator->getLastName()),
                'coordinator_space_url' => $coordinatorSpaceUrl,
            ]
        );

        $message->setSenderEmail('projetscitoyens@en-marche.fr');

        return $message;
    }
}
