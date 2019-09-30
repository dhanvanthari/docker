<?php

namespace AppBundle\Collection;

use AppBundle\Entity\EventRegistration;
use Doctrine\Common\Collections\ArrayCollection;

class EventRegistrationCollection extends ArrayCollection
{
    public function getPastRegistrations(): self
    {
        return $this->filter(function (EventRegistration $registration) {
            return $registration->isEventFinished();
        });
    }

    public function getUpcomingRegistrations(): self
    {
        return $this->filter(function (EventRegistration $registration) {
            return !$registration->isEventFinished();
        });
    }
}
