<?php

declare(strict_types=1);

return [
    'generator' => [
        'namespace' => 'App\\Mason',
        'views_path' => 'mason',
    ],
    'preview' => [
        'layout' => 'mason.preview-layout',
    ],
    'entry' => [
        'layout' => 'mason.entry-layout',
    ],
    'routes' => [
        'middleware' => ['web', 'auth'],
    ],
];
