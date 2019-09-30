<?php

namespace AppBundle\Deputy\Subscriber;

use AppBundle\Entity\District;
use AppBundle\Membership\AdherentEvent;
use AppBundle\Membership\AdherentEvents;
use AppBundle\Repository\DistrictRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BindAdherentDistrictSubscriber implements EventSubscriberInterface
{
    /**
     * @var DistrictRepository
     */
    private $districtRepository;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->districtRepository = $em->getRepository(District::class);
        $this->em = $em;
    }

    public function updateReferentTagWithDistrict(AdherentEvent $event): void
    {
        $adherent = $event->getAdherent();

        if ($adherent->isGeocoded()) {
            $districts = $this->districtRepository->findDistrictsByCoordinates(
                $adherent->getLatitude(),
                $adherent->getLongitude()
            );
            if (!empty($districts)) {
                foreach ($districts as $district) {
                    if (!\in_array($adherent->getCountry(), $district->getCountries())) {
                        continue;
                    }

                    if (!$adherent->getReferentTags()->contains($district->getReferentTag())) {
                        $adherent->addReferentTag($district->getReferentTag());
                    }
                }
                $this->em->flush();
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AdherentEvents::REGISTRATION_COMPLETED => ['updateReferentTagWithDistrict', -257],
            AdherentEvents::PROFILE_UPDATED => ['updateReferentTagWithDistrict', -257],
        ];
    }
}
