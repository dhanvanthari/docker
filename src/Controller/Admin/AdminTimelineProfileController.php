<?php

namespace AppBundle\Controller\Admin;

use A2lix\I18nDoctrineBundle\Annotation\I18nDoctrine;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class AdminTimelineProfileController extends Controller
{
    /**
     * @I18nDoctrine
     */
    public function listAction()
    {
        return parent::listAction();
    }
}
