<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('the homepage and writing index show published articles', function () {
    $author = User::factory()->create();
    $author->posts()->create([
        'title' => 'A Thoughtful Article',
        'slug' => 'a-thoughtful-article',
        'excerpt' => 'A short introduction.',
        'body' => '## The story',
        'status' => 'published',
        'published_at' => now()->subMinute(),
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('A Thoughtful Article')
        ->assertSee(route('blog.show', 'a-thoughtful-article'));

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('A Thoughtful Article')
        ->assertSee('A short introduction.');
});

test('published markdown is rendered safely and drafts are not public', function () {
    $author = User::factory()->create();
    $published = $author->posts()->create([
        'title' => 'Safe Markdown',
        'slug' => 'safe-markdown',
        'body' => "## Hello\n\n<script>alert('xss')</script>\n\n[unsafe](javascript:alert(1))",
        'status' => 'published',
        'published_at' => now()->subMinute(),
    ]);
    $draft = $author->posts()->create([
        'title' => 'Private Draft',
        'slug' => 'private-draft',
        'body' => 'Not public yet.',
        'status' => 'draft',
    ]);

    $this->get(route('blog.show', $published->slug))
        ->assertOk()
        ->assertSee('<h2>Hello</h2>', false)
        ->assertSee('&lt;script&gt;', false)
        ->assertDontSee('<script>alert', false)
        ->assertDontSee('href="javascript:', false);

    $this->get(route('blog.show', $draft->slug))->assertNotFound();
    $this->get(route('blog.index'))->assertDontSee('Private Draft');
});

test('a verified author can create and publish an article with a cover photo', function () {
    Storage::fake('public');
    $author = User::factory()->create();
    $this->actingAs($author);

    $response = $this->post(route('dashboard.posts.store'), [
        'title' => 'A New Beginning',
        'body' => "# A new beginning\n\nHere we go.",
        'status' => 'published',
        'cover_image' => UploadedFile::fake()->image('cover.jpg'),
    ]);

    $post = Post::query()->where('slug', 'a-new-beginning')->firstOrFail();

    $response->assertRedirect(route('dashboard.posts.edit', $post));
    expect($post->user_id)->toBe($author->id)
        ->and($post->excerpt)->toBeNull()
        ->and($post->published_at)->not->toBeNull();
    Storage::disk('public')->assertExists($post->cover_image);
    $this->get(route('blog.show', $post->slug))->assertOk()->assertSee('A New Beginning');
});

test('verified authors can open the markdown editor', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.posts.create'))
        ->assertOk()
        ->assertSee('Markdown supported')
        ->assertSee('Preview')
        ->assertSee('min-h-[32rem]', false)
        ->assertSee('Upload photo')
        ->assertSee('data-article-form', false);
});

test('authors can preview safe rendered markdown', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('dashboard.posts.preview'), [
            'body' => "## Preview\n\n<script>alert('xss')</script>\n\n[unsafe](javascript:alert(1))",
        ])
        ->assertOk()
        ->assertJsonPath('html', "<h2>Preview</h2>\n&lt;script&gt;alert('xss')&lt;/script&gt;\n<p><a>unsafe</a></p>\n");
});

test('an author can upload an inline image for a markdown article', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $response = $this->postJson(route('dashboard.posts.images.store'), [
        'image' => UploadedFile::fake()->image('garden.png'),
    ]);

    $response->assertOk()->assertJsonPath('alt', 'garden');
    $path = str_replace('/storage/', '', $response->json('url'));
    Storage::disk('public')->assertExists($path);
});

test('authors cannot edit another authors articles and guests cannot manage articles', function () {
    $owner = User::factory()->create();
    $article = $owner->posts()->create([
        'title' => 'Private Draft',
        'slug' => 'private-draft',
        'body' => 'Draft content',
        'status' => 'draft',
    ]);

    $this->get(route('dashboard.posts.index'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.posts.edit', $article))
        ->assertNotFound();
});

test('an author can update and delete their article', function () {
    $author = User::factory()->create();
    $post = $author->posts()->create([
        'title' => 'Original Title',
        'slug' => 'original-title',
        'body' => 'Original body',
        'status' => 'draft',
    ]);
    $this->actingAs($author);

    $this->put(route('dashboard.posts.update', $post), [
        'title' => 'Updated Title',
        'excerpt' => '',
        'body' => 'Updated **body**',
        'status' => 'draft',
    ])->assertRedirect(route('dashboard.posts.edit', $post));

    expect($post->fresh()->title)->toBe('Updated Title')
        ->and($post->fresh()->slug)->toBe('updated-title');

    $this->delete(route('dashboard.posts.destroy', $post))
        ->assertRedirect(route('dashboard.posts.index'));
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});
