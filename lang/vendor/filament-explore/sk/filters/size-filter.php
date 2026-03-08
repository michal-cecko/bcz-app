<?php

return [
    'label' => 'Veľkosť',
    'form-schema-components' => [
        'min_size' => [
            'label' => 'Minimálna veľkosť',
            'suffix' => 'jednotky',
        ],
        'max_size' => [
            'label' => 'Maximálna veľkosť',
            'suffix' => 'jednotky',
        ],
        'unit' => [
            'label' => 'Jednotka veľkosti',
            'options' => [
                'bytes' => 'Bajty',
                'kb' => 'KB',
                'mb' => 'MB',
                'gb' => 'GB',
            ],
        ],
    ],
];
