<?php

namespace AppBundle\Statistics\Acquisition\Calculator;

use AppBundle\Donation\PayboxPaymentSubscription;
use AppBundle\Entity\Donation;

class MonthlyAdherentDonationCalculator extends AbstractDonationCountCalculator
{
    public function getLabel(): string
    {
        return 'Dons mensuels par des adhérents (total)';
    }

    protected function getDonationStatus(): string
    {
        return Donation::STATUS_SUBSCRIPTION_IN_PROGRESS;
    }

    protected function getDonationDuration(): int
    {
        return PayboxPaymentSubscription::UNLIMITED;
    }

    protected function isAdherentOnly(): bool
    {
        return true;
    }
}
