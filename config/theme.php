<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Panel color defaults
    |--------------------------------------------------------------------------
    |
    | Fallback colors per panel (admin / website / customer) used until the
    | admin overrides them from Settings -> Theme. Overrides are stored in
    | the `settings` table as "theme.{panel}.{key}" and read/cached by
    | App\Services\ThemeService.
    |
    */
    'panels' => ['admin', 'website', 'customer'],

    'defaults' => [
        'admin' => [
            'primary' => '#2563eb',
            'primary_content' => '#ffffff',
            'secondary' => '#0f172a',
            'secondary_content' => '#ffffff',
            'accent' => '#f59e0b',
            'accent_content' => '#1f2937',
        ],
        'website' => [
            'primary' => '#0d9488',
            'primary_content' => '#ffffff',
            'secondary' => '#1e3a8a',
            'secondary_content' => '#ffffff',
            'accent' => '#f59e0b',
            'accent_content' => '#1f2937',
        ],
        'customer' => [
            'primary' => '#0d9488',
            'primary_content' => '#ffffff',
            'secondary' => '#1e3a8a',
            'secondary_content' => '#ffffff',
            'accent' => '#f59e0b',
            'accent_content' => '#1f2937',
        ],
    ],
];
