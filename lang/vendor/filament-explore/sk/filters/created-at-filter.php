<?php

return [
    'label' => 'Dátum',
    'form-schema-components' => [
        'preset' => [
            'label' => 'Predvoľba',
            'placeholder' => 'Vyberte predvoľbu',
            'options' => [
                'last_7_days' => 'Posledných 7 dní',
                'last_30_days' => 'Posledných 30 dní',
                'last_90_days' => 'Posledné 3 mesiace',
                'last_365_days' => 'Posledných 12 mesiacov',
                'month_to_date' => 'Tento mesiac',
                'year_to_date' => 'Tento rok',
                'all_time' => 'Celé obdobie',
            ],
        ],
        'starts_at' => [
            'label' => 'Od',
        ],
        'ends_at' => [
            'label' => 'Do',
        ],
    ],
    'indicators' => [
        'last_7_days' => 'Posledných 7 dní',
        'last_30_days' => 'Posledných 30 dní',
        'last_90_days' => 'Posledné 3 mesiace',
        'last_365_days' => 'Posledných 12 mesiacov',
        'month_to_date' => 'Tento mesiac',
        'year_to_date' => 'Tento rok',
        'all_time' => 'Celé obdobie',
        'from_date' => 'Od :date',
        'until_date' => 'Do :date',
    ],
];
