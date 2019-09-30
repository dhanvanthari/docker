<?php

namespace Tests\AppBundle\Security;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\PostAddress;
use AppBundle\Membership\ActivityPositions;
use AppBundle\Security\AdherentLoginTimestampRecorder;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AdherentLoginTimestampRecorderTest extends TestCase
{
    public function testRecordLastLoginTimestamp()
    {
        $manager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $manager->expects($this->once())->method('flush');

        $adherent = $this->createAdherent();

        $request = Request::create('POST', '/connexion');
        $token = new PostAuthenticationGuardToken($adherent, 'main', $adherent->getRoles());

        $recorder = new AdherentLoginTimestampRecorder($manager);

        $this->assertNull($adherent->getLastLoggedAt());

        $recorder->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token));

        $this->assertInstanceOf(\DateTime::class, $adherent->getLastLoggedAt());
    }

    private function createAdherent()
    {
        return Adherent::create(
            Adherent::createUuid('john.smith@example.org'),
            'john.smith@example.org',
            'super-password',
            'male',
            'John',
            'Smith',
            new \DateTime('1990-12-12'),
            ActivityPositions::RETIRED,
            PostAddress::createForeignAddress('CH', '8002', 'Zürich', 'Brandschenkestrasse')
        );
    }
}
