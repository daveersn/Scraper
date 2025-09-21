<?php

use App\Support\UrlNormalizer;

test('Normalizes equivalent urls and hashes', function () {
    $a = 'HTTPS://Example.com:443/path/to/page/?b=2&a=1&utm_source=news#frag';
    $b = 'https://example.com/path/to/page?a=1&b=2&fbclid=abc';

    $normalizedA = UrlNormalizer::normalize($a);
    $normalizedB = UrlNormalizer::normalize($b);

    $this->assertSame('https://example.com/path/to/page?a=1&b=2', $normalizedA);
    $this->assertSame($normalizedA, $normalizedB);
    $this->assertSame(UrlNormalizer::hash($normalizedA), UrlNormalizer::hash($normalizedB));
});

test('Removes trackers and default ports and trailing slashes', function () {
    $url = 'http://Sub.Example.COM:80/path///?utm_campaign=x&ref=site&x=1&mc_cid=zz&y=';
    $normalized = UrlNormalizer::normalize($url);

    $this->assertSame('http://sub.example.com/path?x=1', $normalized);
});
