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
}
