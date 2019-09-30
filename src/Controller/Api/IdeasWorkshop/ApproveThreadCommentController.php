<?php

namespace AppBundle\Controller\Api\IdeasWorkshop;

use AppBundle\Entity\IdeasWorkshop\ThreadComment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApproveThreadCommentController
{
    public function approve(Request $request): ThreadComment
    {
        /** @var ThreadComment $object */
        $object = $request->attributes->get('data');

        if ($object->isApproved()) {
            throw new BadRequestHttpException('The comment is already approved');
        }

        $object->setApproved(true);

        return $object;
    }

    public function disapprove(Request $request): ThreadComment
    {
        /** @var ThreadComment $object */
        $object = $request->attributes->get('data');

        if (!$object->isApproved()) {
            throw new BadRequestHttpException('The comment is already disapproved');
        }

        $object->setApproved(false);

        return $object;
    }
}
