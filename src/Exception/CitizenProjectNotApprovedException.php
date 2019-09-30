<?php

namespace AppBundle\Exception;

use AppBundle\Entity\CitizenProject;
use Throwable;

class CitizenProjectNotApprovedException extends CitizenProjectException
{
    public function __construct(CitizenProject $citizenProject, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($citizenProject, $message, $code, $previous);
    }
}
