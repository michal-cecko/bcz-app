<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\Training;
use PHPUnit\Framework\TestCase;

class TrainingBankAccountOverrideTest extends TestCase
{
    public function test_returns_team_iban_when_override_is_null(): void
    {
        $training = new Training;
        $training->setRelation('team', $this->makeTeam('SK1111111111111111111111', 'Team Name'));

        $this->assertSame('SK1111111111111111111111', $training->effectiveBankAccountIban());
        $this->assertSame('Team Name', $training->effectiveBankAccountName());
    }

    public function test_returns_override_when_set(): void
    {
        $training = new Training;
        $training->bank_account_iban = 'SK9999999999999999999999';
        $training->bank_account_name = 'Override Name';
        $training->setRelation('team', $this->makeTeam('SK1111111111111111111111', 'Team Name'));

        $this->assertSame('SK9999999999999999999999', $training->effectiveBankAccountIban());
        $this->assertSame('Override Name', $training->effectiveBankAccountName());
    }

    public function test_falls_back_to_team_iban_when_override_is_empty_string(): void
    {
        $training = new Training;
        $training->bank_account_iban = '';
        $training->bank_account_name = '';
        $training->setRelation('team', $this->makeTeam('SK1111111111111111111111', 'Team Name'));

        $this->assertSame('SK1111111111111111111111', $training->effectiveBankAccountIban());
        $this->assertSame('Team Name', $training->effectiveBankAccountName());
    }

    public function test_returns_null_when_no_override_and_no_team(): void
    {
        $training = new Training;
        $training->setRelation('team', null);

        $this->assertNull($training->effectiveBankAccountIban());
        $this->assertNull($training->effectiveBankAccountName());
    }

    private function makeTeam(?string $iban, ?string $name): Team
    {
        $team = new Team;
        $team->bank_account_iban = $iban;
        $team->bank_account_name = $name;

        return $team;
    }
}
