<?php

return [
    'goid' => env('GOPAY_GOID'),
    'client_id' => env('GOPAY_CLIENT_ID'),
    'client_secret' => env('GOPAY_CLIENT_SECRET'),
    'is_production' => env('GOPAY_PRODUCTION', false),
    'language' => env('GOPAY_LANGUAGE', 'SLOVAK'),
    'notification_url' => env('GOPAY_NOTIFICATION_URL'),
    'return_url' => env('GOPAY_RETURN_URL'),
];
