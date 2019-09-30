<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Redirection;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

class LoadRedirectionData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createRedirection(
            '/dynamic-redirection-301/',
            '/evenements',
            Response::HTTP_MOVED_PERMANENTLY
        ));

        $manager->persist($this->createRedirection(
            '/dynamic-redirection-302',
            '/comites',
            Response::HTTP_FOUND
        ));

        $manager->persist($this->createRedirection(
            '/dynamic-redirection',
            '/articles',
            Response::HTTP_MOVED_PERMANENTLY
        ));

        $manager->flush();
    }

    private function createRedirection(string $from, string $to, int $type): Redirection
    {
        $redirection = new Redirection();
        $redirection->setFrom($from);
        $redirection->setTo($to);
        $redirection->setType($type);

        return $redirection;
    }
}
