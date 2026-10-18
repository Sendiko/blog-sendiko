<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard shows the signed-in authors article counts and recent posts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $user->posts()->create([
        'title' => 'Published story',
        'slug' => 'published-story',
        'body' => 'Published content.',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $user->posts()->create([
        'title' => 'Draft story',
        'slug' => 'draft-story',
        'body' => 'Draft content.',
        'status' => 'draft',
    ]);
    $otherUser->posts()->create([
        'title' => 'Another authors story',
        'slug' => 'another-authors-story',
        'body' => 'Not this authors article.',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Total articles')
        ->assertSee('Published')
        ->assertSee('Drafts')
        ->assertSee('Published story')
        ->assertSee('Draft story')
        ->assertDontSee('Another authors story')
        ->assertSee('Recently updated');
});
