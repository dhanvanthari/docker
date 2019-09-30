<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Projection\ReferentManagedUser;
use AppBundle\Entity\Projection\ReferentManagedUserFactory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadReferentManagedUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {
        $referentManagedUserFactory = $this->getReferentManagedUserFactory();

        $managedUser1 = $referentManagedUserFactory->createFromArray([
            'status' => ReferentManagedUser::STATUS_READY,
            'type' => ReferentManagedUser::TYPE_ADHERENT,
            'original_id' => $this->getReference('adherent-1')->getId(),
            'email' => $this->getReference('adherent-1')->getEmailAddress(),
            'postal_code' => $this->getReference('adherent-1')->getPostalCode(),
            'city' => $this->getReference('adherent-1')->getCityName(),
            'country' => $this->getReference('adherent-1')->getCountry(),
            'first_name' => $this->getReference('adherent-1')->getFirstName(),
            'last_name' => $this->getReference('adherent-1')->getLastName(),
            'birthday' => $this->getReference('adherent-1')->getBirthdate(),
            'is_committee_member' => 0,
            'is_committee_host' => 0,
            'is_committee_supervisor' => 0,
            'is_mail_subscriber' => 0,
            'subscribedTags' => 'ch',
            'created_at' => '2017-06-01 09:22:45',
            'gender' => 'male',
        ]);

        $managedUser2 = $referentManagedUserFactory->createFromArray([
            'status' => ReferentManagedUser::STATUS_READY,
            'type' => ReferentManagedUser::TYPE_ADHERENT,
            'original_id' => $this->getReference('adherent-13')->getId(),
            'email' => $this->getReference('adherent-13')->getEmailAddress(),
            'postal_code' => $this->getReference('adherent-13')->getPostalCode(),
            'city' => $this->getReference('adherent-13')->getCityName(),
            'country' => $this->getReference('adherent-13')->getCountry(),
            'first_name' => $this->getReference('adherent-13')->getFirstName(),
            'last_name' => $this->getReference('adherent-13')->getLastName(),
            'birthday' => $this->getReference('adherent-13')->getBirthdate(),
            'committees' => 'En Marche - Suisse',
            'is_committee_member' => 1,
            'is_committee_host' => 0,
            'is_committee_supervisor' => 0,
            'is_mail_subscriber' => 1,
            'subscribedTags' => 'ch',
            'created_at' => '2017-06-02 15:34:12',
            'gender' => 'male',
        ]);

        $managedUser3 = $referentManagedUserFactory->createFromArray([
            'status' => ReferentManagedUser::STATUS_READY,
            'type' => ReferentManagedUser::TYPE_ADHERENT,
            'original_id' => $this->getReference('adherent-5')->getId(),
            'email' => $this->getReference('adherent-5')->getEmailAddress(),
            'postal_code' => $this->getReference('adherent-5')->getPostalCode(),
            'city' => $this->getReference('adherent-5')->getCityName(),
            'country' => $this->getReference('adherent-5')->getCountry(),
            'first_name' => $this->getReference('adherent-5')->getFirstName(),
            'last_name' => $this->getReference('adherent-5')->getLastName(),
            'birthday' => $this->getReference('adherent-5')->getBirthdate(),
            'committees' => 'En Marche Paris 8',
            'is_committee_member' => 0,
            'is_committee_host' => 1,
            'is_committee_supervisor' => 0,
            'is_mail_subscriber' => 1,
            'subscribedTags' => '92',
            'created_at' => '2017-06-02 15:34:12',
            'gender' => 'female',
            'citizenProjects' => [
                '59-en-marche-projet-citoyen' => 'En marche - Projet Citoyen',
            ],
        ]);

        $managedUser4 = $referentManagedUserFactory->createFromArray([
            'status' => ReferentManagedUser::STATUS_READY,
            'type' => ReferentManagedUser::TYPE_ADHERENT,
            'original_id' => $this->getReference('adherent-7')->getId(),
            'email' => $this->getReference('adherent-7')->getEmailAddress(),
            'postal_code' => $this->getReference('adherent-7')->getPostalCode(),
            'committee_postal_code' => '91',
            'city' => $this->getReference('adherent-7')->getCityName(),
            'country' => $this->getReference('adherent-7')->getCountry(),
            'first_name' => $this->getReference('adherent-7')->getFirstName(),
            'last_name' => $this->getReference('adherent-7')->getLastName(),
            'birthday' => $this->getReference('adherent-7')->getBirthdate(),
            'committees' => 'En Marche - Comité de Évry',
            'is_committee_member' => 0,
            'is_committee_host' => 0,
            'is_committee_supervisor' => 1,
            'is_mail_subscriber' => 1,
            'subscribedTags' => '77',
            'created_at' => '2017-08-12 16:12:13',
            'gender' => 'male',
            'supervisor_tags' => [
                '77',
            ],
            'citizenProjects' => [
                '59-en-marche-projet-citoyen' => 'En marche - Projet Citoyen',
            ],
            'citizenProjectsOrganizer' => [
                '59-en-marche-projet-citoyen' => 'En marche - Projet Citoyen',
            ],
        ]);

        $manager->persist($managedUser1);
        $manager->persist($managedUser2);
        $manager->persist($managedUser3);
        $manager->persist($managedUser4);

        $manager->flush();
    }

    private function getReferentManagedUserFactory(): ReferentManagedUserFactory
    {
        return new ReferentManagedUserFactory();
    }

    public function getDependencies()
    {
        return [
            LoadAdherentData::class,
        ];
    }
}
