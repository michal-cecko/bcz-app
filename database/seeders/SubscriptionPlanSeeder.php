<?php

namespace Database\Seeders;

use App\Enums\PlanTierEnum;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => ['sk' => 'Zadarmo', 'en' => 'Free', 'cs' => 'Zdarma'],
                'tier' => PlanTierEnum::FREE,
                'description' => [
                    'sk' => 'Základný plán pre začínajúce tímy.',
                    'en' => 'Basic plan for starting teams.',
                    'cs' => 'Základní plán pro začínající týmy.',
                ],
                'features' => [
                    'sk' => ['Neobmedzený počet členov', 'Základné funkcie', '100 MB úložisko'],
                    'en' => ['Unlimited members', 'Basic features', '100 MB storage'],
                    'cs' => ['Neomezený počet členů', 'Základní funkce', '100 MB úložiště'],
                ],
                'limits' => [
                    'storage_limit_mb' => 100,
                ],
                'sort_order' => 1,
                'prices' => [
                    ['currency_code' => 'EUR', 'price_monthly' => 0, 'price_yearly' => 0],
                    ['currency_code' => 'CZK', 'price_monthly' => 0, 'price_yearly' => 0],
                ],
            ],
            [
                'name' => ['sk' => 'Starter', 'en' => 'Starter', 'cs' => 'Starter'],
                'tier' => PlanTierEnum::STARTER,
                'description' => [
                    'sk' => 'Pre malé tímy s rozšírenými funkciami.',
                    'en' => 'For small teams with extended features.',
                    'cs' => 'Pro malé týmy s rozšířenými funkcemi.',
                ],
                'features' => [
                    'sk' => ['Až 50 členov', 'Až 5 tréningov', 'Až 2 súťaže ročne', 'Až 5 podujatí ročne', '1 GB úložisko'],
                    'en' => ['Up to 50 members', 'Up to 5 trainings', 'Up to 2 competitions/year', 'Up to 5 events/year', '1 GB storage'],
                    'cs' => ['Až 50 členů', 'Až 5 tréninků', 'Až 2 soutěže ročně', 'Až 5 událostí ročně', '1 GB úložiště'],
                ],
                'limits' => [
                    'max_members' => 50,
                    'max_trainings' => 5,
                    'max_competitions_yearly' => 2,
                    'max_events_yearly' => 5,
                    'storage_limit_mb' => 1024,
                ],
                'sort_order' => 2,
                'prices' => [
                    ['currency_code' => 'EUR', 'price_monthly' => 29.00, 'price_yearly' => 289.00],
                    ['currency_code' => 'CZK', 'price_monthly' => 729.00, 'price_yearly' => 7259.00],
                ],
            ],
            [
                'name' => ['sk' => 'Pro', 'en' => 'Pro', 'cs' => 'Pro'],
                'tier' => PlanTierEnum::PRO,
                'description' => [
                    'sk' => 'Pre stredné tímy s plnými funkciami.',
                    'en' => 'For medium teams with full features.',
                    'cs' => 'Pro střední týmy s plnými funkcemi.',
                ],
                'features' => [
                    'sk' => ['Až 200 členov', 'Neobmedzené tréningy', 'Až 10 súťaží ročne', 'Až 20 podujatí ročne', 'Vlastný branding', '5 GB úložisko'],
                    'en' => ['Up to 200 members', 'Unlimited trainings', 'Up to 10 competitions/year', 'Up to 20 events/year', 'Custom branding', '5 GB storage'],
                    'cs' => ['Až 200 členů', 'Neomezené tréninky', 'Až 10 soutěží ročně', 'Až 20 událostí ročně', 'Vlastní branding', '5 GB úložiště'],
                ],
                'limits' => [
                    'max_members' => 200,
                    'max_competitions_yearly' => 10,
                    'max_events_yearly' => 20,
                    'storage_limit_mb' => 5120,
                ],
                'sort_order' => 3,
                'prices' => [
                    ['currency_code' => 'EUR', 'price_monthly' => 79.00, 'price_yearly' => 789.00],
                    ['currency_code' => 'CZK', 'price_monthly' => 1990.00, 'price_yearly' => 19900.00],
                ],
            ],
            [
                'name' => ['sk' => 'Enterprise', 'en' => 'Enterprise', 'cs' => 'Enterprise'],
                'tier' => PlanTierEnum::ENTERPRISE,
                'description' => [
                    'sk' => 'Pre veľké organizácie bez limitov.',
                    'en' => 'For large organizations without limits.',
                    'cs' => 'Pro velké organizace bez limitů.',
                ],
                'features' => [
                    'sk' => ['Neobmedzený počet členov', 'Neobmedzené tréningy', 'Neobmedzené súťaže', 'Neobmedzené podujatia', 'Vlastný branding', 'Prioritná podpora', 'Neobmedzené úložisko'],
                    'en' => ['Unlimited members', 'Unlimited trainings', 'Unlimited competitions', 'Unlimited events', 'Custom branding', 'Priority support', 'Unlimited storage'],
                    'cs' => ['Neomezený počet členů', 'Neomezené tréninky', 'Neomezené soutěže', 'Neomezené události', 'Vlastní branding', 'Prioritní podpora', 'Neomezené úložiště'],
                ],
                'limits' => null,
                'sort_order' => 4,
                'prices' => [
                    ['currency_code' => 'EUR', 'price_monthly' => 199.00, 'price_yearly' => 1990.00],
                    ['currency_code' => 'CZK', 'price_monthly' => 4990.00, 'price_yearly' => 49900.00],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $prices = $planData['prices'];
            unset($planData['prices']);

            $plan = SubscriptionPlan::query()->updateOrCreate(
                ['tier' => $planData['tier']],
                $planData,
            );

            foreach ($prices as $priceData) {
                SubscriptionPlanPrice::query()->updateOrCreate(
                    [
                        'subscription_plan_id' => $plan->id,
                        'currency_code' => $priceData['currency_code'],
                    ],
                    $priceData,
                );
            }
        }
    }
}
