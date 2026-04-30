<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\EventOrganization;
use App\Models\Team;
use PHPUnit\Framework\TestCase;

class EventOrganizationBankAccountOverrideTest extends TestCase
{
    public function test_returns_team_iban_via_event_when_override_null(): void
    {
        $org = $this->makeOrgWithTeam(null, null, 'SK1111111111111111111111', 'Team Name');

        $this->assertSame('SK1111111111111111111111', $org->effectiveBankAccountIban());
        $this->assertSame('Team Name', $org->effectiveBankAccountName());
    }

    public function test_returns_override_when_set(): void
    {
        $org = $this->makeOrgWithTeam('SK9999999999999999999999', 'Override', 'SK1111111111111111111111', 'Team Name');

        $this->assertSame('SK9999999999999999999999', $org->effectiveBankAccountIban());
        $this->assertSame('Override', $org->effectiveBankAccountName());
    }

    public function test_falls_back_to_team_when_override_is_empty_string(): void
    {
        $org = $this->makeOrgWithTeam('', '', 'SK1111111111111111111111', 'Team Name');

        $this->assertSame('SK1111111111111111111111', $org->effectiveBankAccountIban());
        $this->assertSame('Team Name', $org->effectiveBankAccountName());
    }

    public function test_returns_null_when_no_override_no_team(): void
    {
        $org = new EventOrganization;
        $event = new Event;
        $event->setRelation('team', null);
        $org->setRelation('event', $event);

        $this->assertNull($org->effectiveBankAccountIban());
        $this->assertNull($org->effectiveBankAccountName());
    }

    public function test_event_resolver_falls_back_through_organization(): void
    {
        $event = new Event;
        $org = new EventOrganization;
        $org->bank_account_iban = 'SK9999999999999999999999';
        $org->bank_account_name = 'Override';
        $event->setRelation('organization', $org);
        $event->setRelation('team', $this->makeTeam('SK1111111111111111111111', 'Team'));
        $org->setRelation('event', $event);

        $this->assertSame('SK9999999999999999999999', $event->effectiveBankAccountIban());
        $this->assertSame('Override', $event->effectiveBankAccountName());
    }

    public function test_event_resolver_uses_team_when_no_organization(): void
    {
        $event = new Event;
        $event->setRelation('organization', null);
        $event->setRelation('team', $this->makeTeam('SK1111111111111111111111', 'Team'));

        $this->assertSame('SK1111111111111111111111', $event->effectiveBankAccountIban());
        $this->assertSame('Team', $event->effectiveBankAccountName());
    }

    private function makeOrgWithTeam(?string $orgIban, ?string $orgName, ?string $teamIban, ?string $teamName): EventOrganization
    {
        $org = new EventOrganization;
        $org->bank_account_iban = $orgIban;
        $org->bank_account_name = $orgName;

        $event = new Event;
        $event->setRelation('team', $this->makeTeam($teamIban, $teamName));
        $event->setRelation('organization', $org);

        $org->setRelation('event', $event);

        return $org;
    }

    private function makeTeam(?string $iban, ?string $name): Team
    {
        $team = new Team;
        $team->bank_account_iban = $iban;
        $team->bank_account_name = $name;

        return $team;
    }
}
