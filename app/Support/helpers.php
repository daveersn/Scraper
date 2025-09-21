<?php

if (! function_exists('parse_domain')) {
    function parse_domain(string $url): string
    {
        return parse_url($url, PHP_URL_HOST);
    }
}
