<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_filter([
        env('FRONTEND_URL', 'https://chipper-moonbeam-100da6.netlify.app'),
        'https://chipper-moonbeam-100da6.netlify.app',
        'http://localhost:5173',
        'http://localhost:4173',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:4173',
    ]),
    'allowed_origins_patterns' => [
        '^http://localhost(:[0-9]+)?$',
        '^http://127\.0\.0\.1(:[0-9]+)?$',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
