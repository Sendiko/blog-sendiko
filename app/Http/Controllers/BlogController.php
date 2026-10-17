<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;

class BlogController extends Controller
{
    public function home(): View
    {
        return view('welcome', [
            'posts' => Post::published()->latest('published_at')->take(3)->get(),
        ]);
    }

    public function index(): View
    {
        return view('blog.index', [
            'posts' => Post::published()
                ->with('author')
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('blog.show', compact('post'));
    }
}
