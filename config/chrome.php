<?php

return [
    'socket_file' => env('CHROME_SOCKET_FILE', storage_path('app/browser_socket')),
    'headless' => env('CHROME_HEADLESS', true),
];
