<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AutoIncrementResetter;
use AppBundle\Entity\IdeasWorkshop\Theme;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIdeaThemeData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        AutoIncrementResetter::resetAutoIncrement($manager, 'ideas_workshop_theme');

        $themeArmyDefense = new Theme(
            'Armées et défense',
            'default.png',
            true
        );
        $this->addReference('theme-army-defense', $themeArmyDefense);

        $themeTreasure = new Theme(
            'Trésorerie',
            'tresory.png',
            true,
            2
        );
        $this->addReference('theme-treasure', $themeTreasure);

        $themeEcology = new Theme(
            'Écologie',
            'ecology.png',
            true,
            3
        );
        $this->addReference('theme-ecology', $themeEcology);

        $themeNotPublished = new Theme(
            'Thème non publié',
            'not_published.png',
            false,
            4
        );
        $this->addReference('theme-not-published', $themeNotPublished);

        $manager->persist($themeArmyDefense);
        $manager->persist($themeTreasure);
        $manager->persist($themeEcology);
        $manager->persist($themeNotPublished);

        $manager->flush();
    }
}
