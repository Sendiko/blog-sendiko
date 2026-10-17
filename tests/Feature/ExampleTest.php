<?php

use Illuminate\Support\Facades\Route;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Ideas for a')
        ->assertSee('/storage/hero_image.jpg')
        ->assertSee('/storage/favicon.jpg')
        ->assertSee('Recent')
        ->assertSee('A note, now');
});

test('vite assets use the forwarded https scheme behind the reverse proxy', function () {
    Route::get('/__proxy-scheme-check', fn () => response()->json([
        'secure' => request()->isSecure(),
        'asset' => asset('/build/assets/test.css'),
    ]));

    $response = $this
        ->withServerVariables([
            'HTTPS' => 'off',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])
        ->get('/__proxy-scheme-check');

    $response->assertOk()
        ->assertJsonPath('secure', true)
        ->assertJsonPath('asset', 'https://localhost:8000/build/assets/test.css');
});
