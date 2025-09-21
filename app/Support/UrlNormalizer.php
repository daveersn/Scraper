<?php

namespace App\Support;

final class UrlNormalizer
{
    public static function normalize(string $url): string
    {
        $parts = parse_url($url) ?: [];
        $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : 'http';
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';
        $port = $parts['port'] ?? null;
        $path = $parts['path'] ?? '/';
        $query = $parts['query'] ?? '';

        if (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443)) {
            $port = null;
        }

        // remove fragment by ignoring it

        $path = '/'.ltrim($path, '/');
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        parse_str($query, $params);
        $params = self::filterQuery($params);
        ksort($params);
        $query = http_build_query($params, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);

        $authority = $host.($port ? ':'.$port : '');

        return $scheme.'://'.$authority.$path.($query ? '?'.$query : '');
    }

    private static function filterQuery(array $params): array
    {
        $blacklistPrefixes = ['utm_', 'ad_', 'mc_'];
        $blacklistExact = ['gclid', 'fbclid', 'ref', 'referrer', 'src', 'campaign'];

        return array_filter($params, function ($value, $key) use ($blacklistPrefixes, $blacklistExact) {
            if (in_array(strtolower((string) $key), $blacklistExact, true)) {
                return false;
            }
            foreach ($blacklistPrefixes as $p) {
                if (str_starts_with(strtolower((string) $key), $p)) {
                    return false;
                }
            }

            return $value !== '' && $value !== null;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public static function hash(string $normalizedUrl): string
    {
        return sha1($normalizedUrl);
    }
}
