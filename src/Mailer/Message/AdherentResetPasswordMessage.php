<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Adherent;
use Ramsey\Uuid\Uuid;

final class AdherentResetPasswordMessage extends Message
{
    public static function createFromAdherent(Adherent $adherent, string $resetPasswordLink): self
    {
        return new self(
            Uuid::uuid4(),
            '292292',
            $adherent->getEmailAddress(),
            $adherent->getFullName(),
            'Réinitialisation de votre mot de passe',
            [
                'first_name' => self::escape($adherent->getFirstName()),
                'reset_link' => $resetPasswordLink,
            ]
        );
    }
}
