<?php

namespace AppBundle\Security\Voter\Committee;

use AppBundle\Committee\CommitteePermissions;
use AppBundle\Entity\Adherent;
use AppBundle\Entity\Committee;
use AppBundle\Repository\CommitteeMembershipRepository;
use AppBundle\Security\Voter\AbstractAdherentVoter;

class FollowerCommitteeVoter extends AbstractAdherentVoter
{
    private $repository;

    public function __construct(CommitteeMembershipRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof Committee
            && \in_array($attribute, CommitteePermissions::FOLLOWER, true)
        ;
    }

    /**
     * @param Committee $committee
     */
    protected function doVoteOnAttribute(string $attribute, Adherent $adherent, $committee): bool
    {
        if (!$committee->isApproved()) {
            return false;
        }

        $membership = $adherent->getMembershipFor($committee);

        if (CommitteePermissions::FOLLOW === $attribute) {
            return !$membership;
        }

        // A supervisor cannot unfollow its committee
        if (!$membership || $membership->isSupervisor()) {
            return false;
        }

        // Any basic follower of a committee can unfollow the committee at any point in time.
        // A host can only if another host is registered for that committee.
        return $membership->isFollower() || 1 < $this->repository->countHostMembers($committee);
    }
}
