<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\EventOrganization;
use App\Models\EventRegistration;
use App\Models\RegistrationFee;
use Tests\TestCase;

class EventRegistrationFeeTest extends TestCase
{
    public function test_falls_back_to_organization_price_without_fee(): void
    {
        $registration = new EventRegistration;
        $event = new Event;
        $org = new EventOrganization;
        $org->price_amount = '20.00';
        $org->price_currency = 'EUR';
        $event->setRelation('organization', $org);
        $registration->setRelation('event', $event);
        $registration->setRelation('registrationFee', null);

        $this->assertSame(20.0, $registration->getTotalPriceAmount());
        $this->assertSame('EUR', $registration->getPriceCurrency());
    }

    public function test_uses_fee_amount_when_set(): void
    {
        $registration = new EventRegistration;
        $event = new Event;
        $org = new EventOrganization;
        $org->price_amount = '20.00';
        $org->price_currency = 'EUR';
        $event->setRelation('organization', $org);
        $registration->setRelation('event', $event);

        $fee = new RegistrationFee;
        $fee->amount = '0.00';
        $fee->currency = 'EUR';
        $registration->setRelation('registrationFee', $fee);

        $this->assertSame(0.0, $registration->getTotalPriceAmount());
        $this->assertSame('EUR', $registration->getPriceCurrency());
    }

    public function test_fee_with_different_currency_overrides_organization(): void
    {
        $registration = new EventRegistration;
        $event = new Event;
        $org = new EventOrganization;
        $org->price_amount = '20.00';
        $org->price_currency = 'EUR';
        $event->setRelation('organization', $org);
        $registration->setRelation('event', $event);

        $fee = new RegistrationFee;
        $fee->amount = '500.00';
        $fee->currency = 'CZK';
        $registration->setRelation('registrationFee', $fee);

        $this->assertSame(500.0, $registration->getTotalPriceAmount());
        $this->assertSame('CZK', $registration->getPriceCurrency());
    }

    public function test_description_is_translatable(): void
    {
        $fee = new RegistrationFee;
        $fee->setTranslation('description', 'sk', 'Podporujeme šport u mladých.');
        $fee->setTranslation('description', 'en', 'We support youth sports.');

        $this->assertSame('Podporujeme šport u mladých.', $fee->getTranslation('description', 'sk', false));
        $this->assertSame('We support youth sports.', $fee->getTranslation('description', 'en', false));
        $this->assertSame('', $fee->getTranslation('description', 'cs', false));
    }
}
