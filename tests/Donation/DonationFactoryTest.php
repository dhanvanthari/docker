<?php

namespace Tests\AppBundle\Donation;

use AppBundle\Address\GeoCoder;
use AppBundle\Address\PostAddressFactory;
use AppBundle\Donation\DonationFactory;
use AppBundle\Donation\DonationRequest;
use AppBundle\Donation\DonationRequestUtils;
use AppBundle\Membership\MembershipRegistrationProcess;
use Cocur\Slugify\Slugify;
use libphonenumber\PhoneNumber;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DonationFactoryTest extends TestCase
{
    private const DONATION_REQUEST_UUID = 'cfd3c04f-cce0-405d-865f-f5f3a2c1792e';

    public function testCreateDonationFromDonationRequest(): void
    {
        $uuid = Uuid::fromString(self::DONATION_REQUEST_UUID);
        $phone = new PhoneNumber();
        $phone->setCountryCode('FR');
        $phone->setNationalNumber('0123456789');

        $request = new DonationRequest($uuid, '3.3.3.3');
        $request->firstName = 'Damien';
        $request->lastName = 'DUPONT';
        $request->gender = 'male';
        $request->setAmount(70.0);
        $request->setEmailAddress('m.dupont@example.fr');
        $request->setCountry('FR');
        $request->setPostalCode('69000');
        $request->setCityName('Lyon');
        $request->setAddress('2, Rue de la République');
        $request->setDuration(0);

        $factory = $this->createFactory();
        $donation = $factory->createFromDonationRequest($request);

        $this->assertSame('m.dupont@example.fr', $donation->getEmailAddress());
        $this->assertSame('male', $donation->getGender());
        $this->assertSame('Damien', $donation->getFirstName());
        $this->assertSame('DUPONT', $donation->getLastName());
        $this->assertSame('FR', $donation->getCountry());
        $this->assertSame('2, Rue de la République', $donation->getAddress());
        $this->assertSame('Lyon', $donation->getCityName());
        $this->assertSame('69000', $donation->getPostalCode());
        $this->assertSame(7000, $donation->getAmount());
        $this->assertSame(70.0, $donation->getAmountInEuros());
        $this->assertSame(0, $donation->getDuration());
        $this->assertSame('3.3.3.3', $donation->getClientIp());
        $this->assertSame(self::DONATION_REQUEST_UUID, $donation->getUuid()->toString());
    }

    private function createFactory()
    {
        return new DonationFactory(
            new PostAddressFactory(),
            new DonationRequestUtils(
                $this->createMock(ServiceLocator::class),
                Slugify::create(),
                $this->createMock(MembershipRegistrationProcess::class),
                $this->createMock(GeoCoder::class)
            )
        );
    }
}
