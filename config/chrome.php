<?php

return [
    'socket_file' => env('CHROME_SOCKET_FILE', storage_path('app/browser_socket')),
    'check_interval' => env('CHROME_CHECK_INTERVAL', 5),
];
