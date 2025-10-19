<?php

return [
    'chrome' => [
        // Page environment
        'user_agent' => env('SCRAPING_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36'),
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
