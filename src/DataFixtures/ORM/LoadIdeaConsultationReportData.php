<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\IdeasWorkshop\ConsultationReport;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIdeaConsultationReportData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $consultationReportGreenEnergy = new ConsultationReport(
            'Rapport sur les énergies renouvables',
            'https://storage.googleapis.com/en-marche-prod/documents/adherents/1-charte-et-manifeste/charte_des_valeurs.pdf',
            1
        );
        $this->addReference('consultation-report-green-energy', $consultationReportGreenEnergy);

        $consultationReportHousingPolicy = new ConsultationReport(
            'Rapport sur la politique du logement',
            'https://storage.googleapis.com/en-marche-prod/documents/adherents/1-charte-et-manifeste/regles_de_fonctionnement_LaREM.pdf',
            2
        );
        $this->addReference('consultation-report-housing-policy', $consultationReportHousingPolicy);

        $manager->persist($consultationReportGreenEnergy);
        $manager->persist($consultationReportHousingPolicy);

        $manager->flush();
    }
}
