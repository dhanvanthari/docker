<?php

namespace AppBundle\ApplicationRequest;

use AppBundle\Entity\ApplicationRequest\ApplicationRequest;
use Symfony\Component\EventDispatcher\Event;

class ApplicationRequestEvent extends Event
{
    private $applicationRequest;

    public function __construct(ApplicationRequest $applicationRequest)
    {
        $this->applicationRequest = $applicationRequest;
    }

    public function getApplicationRequest(): ApplicationRequest
    {
        return $this->applicationRequest;
    }
}
