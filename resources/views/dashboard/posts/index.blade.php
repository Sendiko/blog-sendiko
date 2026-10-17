<x-layouts::app :title="__('Your articles')">
    <div class="mx-auto max-w-5xl space-y-8 px-6 py-10">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm text-zinc-500">Your writing, all in one place.</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">Articles</h1>
            </div>
            <a href="{{ route('dashboard.posts.create') }}" class="inline-flex items-center rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">
                Write an article <span class="ml-2" aria-hidden="true">+</span>
            </a>
        </div>

        @if (session('status'))
            <p role="status" class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800 dark:bg-green-950 dark:text-green-200">{{ session('status') }}</p>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            @forelse ($posts as $post)
                <article class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-100 px-5 py-5 last:border-b-0 dark:border-zinc-800">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate font-medium text-zinc-900 dark:text-white">{{ $post->title }}</h2>
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $post->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-zinc-500">
                            Updated {{ $post->updated_at->diffForHumans() }}
                            @if ($post->status === 'published')
                                · <a class="underline underline-offset-2 hover:text-zinc-900 dark:hover:text-white" href="{{ route('blog.show', $post->slug) }}" target="_blank">View article</a>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a class="text-sm font-medium text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white" href="{{ route('dashboard.posts.edit', $post) }}">Edit</a>
                        <form action="{{ route('dashboard.posts.destroy', $post) }}" method="post" onsubmit="return confirm('Delete this article? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:text-red-800 dark:text-red-400" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="px-6 py-16 text-center">
                    <p class="text-lg font-medium text-zinc-900 dark:text-white">Your first story starts here.</p>
                    <p class="mt-2 text-sm text-zinc-500">Write in Markdown, add a few photos, and share it when you’re ready.</p>
                    <a href="{{ route('dashboard.posts.create') }}" class="mt-5 inline-flex rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-600 dark:hover:bg-zinc-800">Create an article</a>
                </div>
            @endforelse
        </div>

        {{ $posts->links() }}
    </div>
</x-layouts::app>
