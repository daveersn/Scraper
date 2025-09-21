<?php

return [
    'chrome' => [
        // Absolute path to Chrome/Chromium binary if auto-detection fails.
        'binary' => env('SCRAPING_CHROME_BINARY'),

        // Headless flags and extra Chrome flags.
        'headless' => env('SCRAPING_CHROME_HEADLESS', true),

        // Page environment
        'user_agent' => env('SCRAPING_USER_AGENT'),
        'viewport' => [
            'width' => (int) env('SCRAPING_VIEWPORT_WIDTH', 1366),
            'height' => (int) env('SCRAPING_VIEWPORT_HEIGHT', 768),
        ],

        // Throttling between pages (milliseconds)
        'throttle_ms' => (int) env('SCRAPING_THROTTLE_MS', 500),
        'navigation_delay_ms' => (int) env('SCRAPING_NAV_DELAY_MS', 0),

        // Pagination safety cap (max pages per run)
        'pagination_max_pages' => (int) env('SCRAPING_MAX_PAGES', 10),
    ],
];
