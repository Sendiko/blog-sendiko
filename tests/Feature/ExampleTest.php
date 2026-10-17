<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Ideas for a')
        ->assertSee('Recent')
        ->assertSee('A note, now');
});
