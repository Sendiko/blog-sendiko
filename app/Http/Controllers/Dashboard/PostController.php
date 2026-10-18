<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard.posts.index', [
            'posts' => $request->user()->posts()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.posts.edit', ['post' => new Post]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $image = $request->file('cover_image');
        $coverImage = $image ? $this->storeImage($image, 'blog-covers') : null;
        $status = $validated['status'];

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title']),
            'excerpt' => ($validated['excerpt'] ?? null) ?: null,
            'body' => $validated['body'],
            'cover_image' => $coverImage,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);

        return redirect()
            ->route('dashboard.posts.edit', $post)
            ->with('status', 'Article created.');
    }

    public function edit(Request $request, Post $post): View
    {
        $this->authorizePost($request, $post);

        return view('dashboard.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($request, $post);
        $validated = $request->validate($this->rules());
        $oldCover = $post->cover_image;
        $newCover = $request->file('cover_image')
            ? $this->storeImage($request->file('cover_image'), 'blog-covers')
            : $oldCover;
        $status = $validated['status'];
        $slug = $post->title === $validated['title']
            ? $post->slug
            : $this->uniqueSlug($validated['title'], $post);

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => ($validated['excerpt'] ?? null) ?: null,
            'body' => $validated['body'],
            'cover_image' => $newCover,
            'status' => $status,
            'published_at' => $status === 'published' ? ($post->published_at ?? now()) : null,
        ]);

        if ($newCover !== $oldCover && $oldCover) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()
            ->route('dashboard.posts.edit', $post)
            ->with('status', 'Article saved.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($request, $post);

        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return redirect()
            ->route('dashboard.posts.index')
            ->with('status', 'Article deleted.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);
        $path = $this->storeImage($validated['image'], 'blog-images');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'alt' => pathinfo($validated['image']->getClientOriginalName(), PATHINFO_FILENAME),
        ]);
    }

    public function previewMarkdown(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:50000'],
        ]);

        return response()->json([
            'html' => Post::renderMarkdown($validated['body'] ?? ''),
        ]);
    }

    /**
     * @return array<string, array<int, string|Unique>>
     */
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:50000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }

    private function authorizePost(Request $request, Post $post): void
    {
        abort_unless($post->user_id === $request->user()->id, 404);
    }

    private function uniqueSlug(string $title, ?Post $ignore = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $suffix = 2;

        while (Post::query()
            ->where('slug', $slug)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function storeImage(UploadedFile $image, string $directory): string
    {
        $path = $image->storePublicly($directory, 'public');

        if (! $path) {
            throw new RuntimeException('The image could not be saved to the public storage disk.');
        }

        return $path;
    }
}
