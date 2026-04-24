<?php

namespace App\Models\Concerns;

use App\Enums\PaymentMethodEnum;
use App\Models\PaymentMethod;
use Illuminate\Support\Collection;

/**
 * Resolves the payment methods that should be offered for this payable.
 * Per-payable methods (Event/Training pivot via payable_payment_method) win when set;
 * otherwise the parent team's enabled methods are used as fallback.
 *
 * Hosts must declare:
 *   - enabledPaymentMethods(): MorphToMany — the per-payable relation
 *   - team relation
 */
trait HasResolvedPaymentMethods
{
    /**
     * @return Collection<int, PaymentMethod>
     */
    public function effectivePaymentMethods(): Collection
    {
        $perPayable = $this->enabledPaymentMethods()->get();

        if ($perPayable->isNotEmpty()) {
            return $perPayable;
        }

        return $this->team?->enabledPaymentMethods ?? collect();
    }

    /**
     * @return list<string>
     */
    public function effectivePaymentMethodKeys(): array
    {
        return $this->effectivePaymentMethods()
            ->pluck('method')
            ->map(fn ($m) => $m instanceof PaymentMethodEnum ? $m->value : (string) $m)
            ->values()
            ->all();
    }

    /**
     * Resolve instructions for a method with cascade:
     * 1. per-payable pivot (payable_payment_method.instructions)
     * 2. team pivot (payment_method_team.instructions)
     * 3. default PaymentMethod.instructions (seeded fallback)
     * 4. null — caller falls back to localized defaults
     */
    public function effectivePaymentMethodInstructions(string $methodKey, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $perPayable = $this->enabledPaymentMethods()
            ->where('method', $methodKey)
            ->first();

        $instructions = $perPayable?->pivot?->getTranslation('instructions', $locale, false);
        if (filled($instructions) && trim(strip_tags($instructions)) !== '') {
            return $instructions;
        }

        $teamMethod = $this->team?->enabledPaymentMethods
            ->firstWhere(
                fn ($m) => ($m->method instanceof PaymentMethodEnum ? $m->method->value : (string) $m->method) === $methodKey,
            );

        $instructions = $teamMethod?->pivot?->getTranslation('instructions', $locale, false);
        if (filled($instructions) && trim(strip_tags($instructions)) !== '') {
            return $instructions;
        }

        $default = $teamMethod ?? PaymentMethod::query()->where('method', $methodKey)->first();
        $instructions = $default?->getTranslation('instructions', $locale, false);
        if (filled($instructions) && trim(strip_tags($instructions)) !== '') {
            return $instructions;
        }

        return null;
    }
}
