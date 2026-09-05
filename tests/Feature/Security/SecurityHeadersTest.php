<?php

test('responses include basic security headers', function () {
    actingAsAdmin();

    $response = $this->get(route('dashboard'));

    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Content-Security-Policy', "frame-ancestors 'none'");
});

test('security headers are present even for guests', function () {
    $response = $this->get(route('login'));

    $response->assertHeader('X-Frame-Options', 'DENY');
});
