<?php

namespace Tests\AppBundle\Security\Voter\Committee;

use AppBundle\Committee\CommitteePermissions;
use AppBundle\Entity\Adherent;
use AppBundle\Entity\Committee;
use AppBundle\Security\Voter\AbstractAdherentVoter;
use AppBundle\Security\Voter\Committee\SuperviseCommitteeVoter;
use Tests\AppBundle\Security\Voter\AbstractAdherentVoterTest;

class SuperviseCommitteeVoterTest extends AbstractAdherentVoterTest
{
    public function provideAnonymousCases(): iterable
    {
        yield [false, true, CommitteePermissions::SUPERVISE, $this->createMock(Committee::class)];
    }

    protected function getVoter(): AbstractAdherentVoter
    {
        return new SuperviseCommitteeVoter();
    }

    public function testAdherentIsNotGranted()
    {
        $committee = $this->createMock(Committee::class);
        $adherent = $this->getAdherentMock(false, $committee);

        $this->assertGrantedForAdherent(false, true, $adherent, CommitteePermissions::SUPERVISE, $committee);
    }

    public function testSupervizerIsGranted()
    {
        $committee = $this->createMock(Committee::class);
        $adherent = $this->getAdherentMock(true, $committee);

        $this->assertGrantedForAdherent(true, true, $adherent, CommitteePermissions::SUPERVISE, $committee);
    }

    /**
     * @return Adherent|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getAdherentMock(bool $isSupervisor, Committee $committee): Adherent
    {
        $adherent = $this->createAdherentMock();

        $adherent->expects($this->once())
            ->method('isSupervisorOf')
            ->with($committee)
            ->willReturn($isSupervisor)
        ;

        return $adherent;
    }
}
