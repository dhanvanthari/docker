<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Mooc\Quiz;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMoocQuizData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $quiz = new Quiz(
            'Le test de votre vie',
            '<p>une description</p>',
            'Bonsoir, voici un tweet de partage d\'un MOOC #enmarche',
            'Bonsoir, voici un partage avec Facebook',
            'Bonsoir, voici un email de partage !',
            'Voici le contenu de l\'email de partage. Merci.',
            'https://developerplatform.typeform.com/to/Xc7NMh'
        );

        $manager->persist($quiz);
        $manager->flush();

        $this->setReference('mooc-quiz-1', $quiz);
    }
}
