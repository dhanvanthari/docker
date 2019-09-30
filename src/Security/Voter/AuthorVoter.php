<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\AuthoredInterface;

class AuthorVoter extends AbstractAdherentVoter
{
    public const PERMISSION = 'IS_AUTHOR_OF';

    protected function doVoteOnAttribute(string $attribute, Adherent $adherent, $subject): bool
    {
        /** @var AuthoredInterface $subject */
        return $subject->getAuthor()->equals($adherent);
    }

    protected function supports($attribute, $subject)
    {
        return self::PERMISSION === $attribute && $subject instanceof AuthoredInterface;
    }
}
