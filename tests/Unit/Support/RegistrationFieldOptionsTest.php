<?php

namespace Tests\Unit\Support;

use App\Support\RegistrationFieldOptions;
use PHPUnit\Framework\TestCase;

class RegistrationFieldOptionsTest extends TestCase
{
    public function test_resolves_legacy_comma_separated_string(): void
    {
        $options = RegistrationFieldOptions::resolve(
            ['type' => 'select', 'options' => 'Apple, Banana, Cherry'],
            'sk',
        );

        $this->assertSame(['Apple' => 'Apple', 'Banana' => 'Banana', 'Cherry' => 'Cherry'], $options);
    }

    public function test_resolves_legacy_newline_separated_string(): void
    {
        $options = RegistrationFieldOptions::resolve(
            ['type' => 'select', 'options' => "Red\nGreen\nBlue"],
            'sk',
        );

        $this->assertSame(['Red' => 'Red', 'Green' => 'Green', 'Blue' => 'Blue'], $options);
    }

    public function test_resolves_legacy_array_of_strings(): void
    {
        $options = RegistrationFieldOptions::resolve(
            ['type' => 'select', 'options' => ['A', 'B']],
            'sk',
        );

        $this->assertSame(['A' => 'A', 'B' => 'B'], $options);
    }

    public function test_resolves_translated_array_for_current_locale(): void
    {
        $field = ['type' => 'select', 'options' => [
            ['value' => 'apple', 'label' => ['sk' => 'Jablko', 'en' => 'Apple', 'cs' => 'Jablko']],
            ['value' => 'pear', 'label' => ['sk' => 'Hruška', 'en' => 'Pear', 'cs' => 'Hruška']],
        ]];

        $this->assertSame(['apple' => 'Apple', 'pear' => 'Pear'], RegistrationFieldOptions::resolve($field, 'en'));
        $this->assertSame(['apple' => 'Jablko', 'pear' => 'Hruška'], RegistrationFieldOptions::resolve($field, 'sk'));
    }

    public function test_falls_back_to_sk_when_locale_label_missing(): void
    {
        $field = ['type' => 'select', 'options' => [
            ['value' => 'x', 'label' => ['sk' => 'iba SK']],
        ]];

        $this->assertSame(['x' => 'iba SK'], RegistrationFieldOptions::resolve($field, 'en'));
    }

    public function test_returns_empty_for_missing_options(): void
    {
        $this->assertSame([], RegistrationFieldOptions::resolve(['type' => 'select'], 'sk'));
        $this->assertSame([], RegistrationFieldOptions::resolve(['type' => 'select', 'options' => ''], 'sk'));
        $this->assertSame([], RegistrationFieldOptions::resolve(['type' => 'select', 'options' => null], 'sk'));
    }

    public function test_label_for_resolves_single_value(): void
    {
        $field = ['type' => 'select', 'options' => [
            ['value' => 'a', 'label' => ['sk' => 'Áno', 'en' => 'Yes']],
            ['value' => 'b', 'label' => ['sk' => 'Nie', 'en' => 'No']],
        ]];

        $this->assertSame('Nie', RegistrationFieldOptions::labelFor($field, 'b', 'sk'));
        $this->assertSame('Yes', RegistrationFieldOptions::labelFor($field, 'a', 'en'));
    }

    public function test_label_for_resolves_multi_value(): void
    {
        $field = ['type' => 'multi_select', 'options' => [
            ['value' => 'a', 'label' => ['sk' => 'Áno']],
            ['value' => 'b', 'label' => ['sk' => 'Nie']],
        ]];

        $this->assertSame('Áno, Nie', RegistrationFieldOptions::labelFor($field, ['a', 'b'], 'sk'));
    }

    public function test_label_for_falls_back_to_raw_value_when_unknown(): void
    {
        $this->assertSame('unknown', RegistrationFieldOptions::labelFor(
            ['type' => 'select', 'options' => [['value' => 'a', 'label' => ['sk' => 'A']]]],
            'unknown',
            'sk',
        ));
    }

    public function test_category_type_returns_empty_without_event(): void
    {
        $this->assertSame([], RegistrationFieldOptions::resolve(['type' => 'category'], 'sk'));
    }
}
