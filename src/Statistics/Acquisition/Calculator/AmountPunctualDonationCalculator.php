<?php

namespace AppBundle\Statistics\Acquisition\Calculator;

use AppBundle\Donation\PayboxPaymentSubscription;
use AppBundle\Entity\Donation;

class AmountPunctualDonationCalculator extends AbstractAmountDonationCalculator
{
    public function getLabel(): string
    {
        return 'Montant dons ponctuels (total)';
    }

    protected function getDonationStatus(): string
    {
        return Donation::STATUS_FINISHED;
    }

    protected function getDonationDuration(): int
    {
        return PayboxPaymentSubscription::NONE;
    }
}
