<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AdherentTag;
use AppBundle\Entity\AdherentTagEnum;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdherentTagData extends AbstractFixture implements FixtureInterface
{
    public const ADHERENT_TAG = [
        'AT001' => AdherentTagEnum::ELECTED,
        'AT002' => AdherentTagEnum::VERY_ACTIVE,
        'AT003' => AdherentTagEnum::ACTIVE,
        'AT004' => AdherentTagEnum::LOW_ACTIVE,
        'AT005' => AdherentTagEnum::MEDIATION,
        'AT006' => AdherentTagEnum::SUBSTITUTE,
        'AT007' => AdherentTagEnum::LAREM,
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::ADHERENT_TAG as $code => $name) {
            $adherentTag = new AdherentTag($name);
            $manager->persist($adherentTag);
            $this->addReference('adherent_tag_'.strtolower($code), $adherentTag);
        }

        $manager->flush();
    }
}
