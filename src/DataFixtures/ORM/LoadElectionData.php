<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Election;
use AppBundle\Entity\ElectionRound;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadElectionData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $presidentialElections = $this->createElection(
            'Élections Présidentielles 2017',
            <<<INTRODUCTION
<h1 class="text--larger">
    Chaque vote compte.
</h1>
<h2 class="text--medium b__nudge--top l__hide--on-mobile b__nudge--bottom-small">
    Les élections présidentielles ont lieu les 24 avril et 7 mai 2017 en France (15 et 29 avril pour les Français de l'étranger du continent Américain et 16 et 30 avril pour les autres Français de l'étranger).
</h2>
<div class="text--body">
    Si vous ne votez pas en France métropolitaine, <a href="https://www.diplomatie.gouv.fr/fr/services-aux-citoyens/droit-de-vote-et-elections-a-l-etranger/elections-europeennes-2019-mode-d-emploi-pour-les-francais-residant-a-l-62666" class="link--white">renseignez-vous sur les dates</a>.
</div>
INTRODUCTION
        );
        $this->createRound(
            $presidentialElections,
            '1er tour des éléctions présidentielles 2017',
            'Dimanche 24 avril 2017 en France (15 avril pour les Français de l\'étranger du continent Américain et 16 avril pour les autres Français de l\'étranger)',
            '24-04-2017'
        );
        $this->createRound(
            $presidentialElections,
            '2e tour des éléctions présidentielles 2017',
            'Dimanche 7 mai 2017 en France (29 avril pour les Français de l\'étranger du continent Américain et 30 avril pour les autres Français de l\'étranger)',
            '07-05-2017'
        );

        $legislativeElections = $this->createElection(
            'Élections Législatives 2017',
            <<<INTRODUCTION
<h1 class="text--larger">
    Chaque vote compte.
</h1>
<h2 class="text--medium b__nudge--top l__hide--on-mobile b__nudge--bottom-small">
    Les élections législatives ont lieu les 11 et 18 juin 2017 en France (3 et 17 juin pour les Français de l'étranger du continent Américain et 4 et 18 juin pour les autres Français de l'étranger).
</h2>
<div class="text--body">
    Si vous ne votez pas en France métropolitaine, <a href="https://www.diplomatie.gouv.fr/fr/services-aux-citoyens/droit-de-vote-et-elections-a-l-etranger/elections-europeennes-2019-mode-d-emploi-pour-les-francais-residant-a-l-62666" class="link--white">renseignez-vous sur les dates</a>.
</div>
INTRODUCTION
        );
        $this->createRound(
            $legislativeElections,
            '1er tour des éléctions législatives 2017',
            'Dimanche 11 juin 2017 en France (3 juin pour les Français de l\'étranger du continent Américain et 4 juin pour les autres Français de l\'étranger).',
            '11-06-2017'
        );
        $this->createRound(
            $legislativeElections,
            '2e tour des éléctions législatives 2017',
            'Dimanche 18 juin 2017 en France (17 juin pour les Français de l\'étranger du continent Américain et 18 juin pour les autres Français de l\'étranger).',
            '18-06-2017'
        );

        $partialLegislativeElections = $this->createElection(
            'Élection législative partielle pour la 1ère circonscription du Val-d\'Oise',
            <<<INTRODUCTION
<h1 class="text--larger">
    Chaque vote compte.
</h1>
<h2 class="text--medium b__nudge--top l__hide--on-mobile b__nudge--bottom-small">
    L'élection législative partielle pour la 1ère circonscription du Val-d'Oise aura lieu les 28 janvier et 4 février 2018.
</h2>
<div class="text--body">
    Si vous ne votez pas en France métropolitaine, <a href="https://www.diplomatie.gouv.fr/fr/services-aux-citoyens/droit-de-vote-et-elections-a-l-etranger/elections-europeennes-2019-mode-d-emploi-pour-les-francais-residant-a-l-62666" class="link--white">renseignez-vous sur les dates</a>.
</div>
INTRODUCTION
        );
        // We need this election to always be in the future for tests to pass
        // less than 3 days to trigger a reminder
        $nextTime = new \DateTime('+2 days');
        $this->createRound(
            $partialLegislativeElections,
            '1er tour des éléctions législatives partielles pour la 1ère circonscription du Val-d\'Oise 2018',
            'Dimanche 28 janvier 2018',
            $nextTime->format('d-m-Y')
        );
        $this->createRound(
            $partialLegislativeElections,
            '2e tour des éléctions législatives partielles pour la 1ère circonscription du Val-d\'Oise 2018',
            'Dimanche 4 février 2018',
            $nextTime->modify('+1 week')->format('d-m-Y')
        );

        $manager->persist($presidentialElections);
        $manager->persist($legislativeElections);
        $manager->persist($partialLegislativeElections);
        $manager->flush();
    }

    private function createElection(string $name, string $introduction): Election
    {
        $election = new Election();
        $election->setName($name);
        $election->setIntroduction($introduction);

        return $election;
    }

    private function createRound(Election $election, string $label, string $description, string $date): void
    {
        $round = new ElectionRound();
        $round->setLabel($label);
        $round->setDescription($description);
        $round->setDate(date_create_from_format('d-m-Y', $date));

        $election->addRound($round);
    }
}
