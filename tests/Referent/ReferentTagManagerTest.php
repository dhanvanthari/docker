<?php

namespace Tests\AppBundle\Referent;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\ApplicationRequest\ApplicationRequest;
use AppBundle\Entity\ReferentTag;
use AppBundle\Referent\ReferentTagManager;
use AppBundle\Repository\ReferentTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Controller\ControllerTestTrait;

/**
 * @group functional
 */
class ReferentTagManagerTest extends WebTestCase
{
    use ControllerTestTrait;

    /**
     * @var ReferentTagManager
     */
    private $referentTagManager;

    /**
     * @var ReferentTagRepository
     */
    private $referentTagRepository;

    protected function setUp()
    {
        $this->init();

        $this->referentTagManager = $this->getContainer()->get(ReferentTagManager::class);
        $this->referentTagRepository = $this->getRepository(ReferentTag::class);
    }

    protected function tearDown()
    {
        $this->referentTagManager = null;

        $this->kill();
    }

    /**
     * @param string[]|array $favoriteCities
     * @param string[]|array $expectedTagCodes
     *
     * @dataProvider provideApplicationRequestsReferentTags
     */
    public function testAssignApplicationRequestReferentTags(array $favoriteCities, array $expectedTagCodes): void
    {
        $applicationRequest = $this->createMock(ApplicationRequest::class);

        $applicationRequest->method('getFavoriteCities')->willReturn($favoriteCities);

        $applicationRequest
            ->expects($this->exactly(\count($expectedTagCodes)))
            ->method('addReferentTag')
            ->will($this->returnValueMap(array_map(function (string $expectedTagCode) {
                return [$this->referentTagRepository->findOneByCode($expectedTagCode)];
            }, $expectedTagCodes)))
        ;

        $this->referentTagManager->assignApplicationRequestReferentTags($applicationRequest);
    }

    /**
     * @dataProvider providesTestIsUpdateNeeded
     */
    public function testIsUpdateNeeded(bool $isUpdateNeeded, string $postalCode, array $referentCodes): void
    {
        $adherent = $this->createAdherent($postalCode, $referentCodes);

        $this->assertSame($isUpdateNeeded, $this->referentTagManager->isUpdateNeeded($adherent));
    }

    public function providesTestIsUpdateNeeded(): array
    {
        return [
            [false, '73100', ['73']],
            [true, '75001', ['75']],
            [false, '75001', ['75', '75001']],
            [true, '75001', ['75', '75001', 'France']],
        ];
    }

    public function provideApplicationRequestsReferentTags(): array
    {
        return [
            [['11069'], ['11']],
            [['75001'], ['75001', '75']],
            [['11069', '75001'], ['11', '75001', '75']],
            [['11069', '75001'], ['11', '75001', '75']],
            [['70295', '2A004'], ['70', '2A', '20']],
            [['70295', '75001', '2A004'], ['70', '75001', '75', '2A', '20']],
        ];
    }

    /**
     * @return Adherent|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createAdherent($postalCode, array $referentCodes): Adherent
    {
        $adherent = $this->createMock(Adherent::class);

        $tags = new ArrayCollection();
        foreach ($referentCodes as $code) {
            $tags->add(new ReferentTag(null, $code));
        }

        $adherent->method('getCountry')->willReturn('FR');
        $adherent->method('getReferentTags')->willReturn($tags);
        $adherent->method('getPostalCode')->willReturn($postalCode);

        return $adherent;
    }
}
