<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\CitizenProject;
use Ramsey\Uuid\Uuid;

class CitizenProjectRequestCommitteeSupportMessage extends Message
{
    public static function create(
        CitizenProject $citizenProject,
        Adherent $committeeSupervisor,
        string $validationUrl
    ): self {
        $message = new self(
            Uuid::uuid4(),
            '263222',
            $committeeSupervisor->getEmailAddress(),
            $committeeSupervisor->getFullName(),
            'Un projet citoyen a besoin du soutien de votre comité !',
            [
                'citizen_project_name' => self::escape($citizenProject->getName()),
                'citizen_project_host_firstname' => self::escape($citizenProject->getCreator() ? $citizenProject->getCreator()->getFirstName() : ''),
                'citizen_project_host_lastname' => self::escape($citizenProject->getCreator() ? $citizenProject->getCreator()->getLastName() : ''),
                'validation_url' => self::escape($validationUrl),
            ],
            [
                'target_firstname' => self::escape($committeeSupervisor->getFirstName() ?? ''),
            ]
        );

        $message->setSenderEmail('projetscitoyens@en-marche.fr');

        return $message;
    }
}
