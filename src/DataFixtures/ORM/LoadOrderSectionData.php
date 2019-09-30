<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\OrderSection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadOrderSectionData extends AbstractFixture implements FixtureInterface
{
    const ORDER_SECTION = [
        'OS001' => 'Articles',
        'OS002' => 'Tribune',
        'OS003' => 'Lexique',
        'OS004' => 'Autre resources',
    ];

    public function load(ObjectManager $manager)
    {
        $position = 0;
        foreach (self::ORDER_SECTION as $key => $name) {
            $section = new OrderSection(++$position, $name);
            $manager->persist($section);
            $this->addReference(strtolower($key), $section);
        }

        $manager->flush();
    }
}
