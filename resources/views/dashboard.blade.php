<x-layouts::app :title="__('Dashboard')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-8 px-6 py-10">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm text-zinc-500">Your writing, all in one place.</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">Welcome back, {{ auth()->user()->name }}.</h1>
            </div>
            <a href="{{ route('dashboard.posts.create') }}" class="inline-flex items-center rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">
                Write an article <span class="ml-2" aria-hidden="true">+</span>
            </a>
        </div>

        <section aria-label="Article statistics" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500">Total articles</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $totalPosts }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500">Published</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $publishedPosts }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500">Drafts</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $draftPosts }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500">Article views</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ number_format($totalViews) }}</p>
            </div>
        </section>

        <div class="rounded-2xl border border-zinc-200 bg-white p-8 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500">Your blog</p>
            <h2 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">Got a thought worth sharing?</h2>
            <p class="mt-2 max-w-xl text-sm leading-6 text-zinc-500">Write an article in Markdown, add photos, and publish it whenever you’re ready. Drafts stay private until you publish.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('dashboard.posts.create') }}" class="rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Write an article</a>
                <a href="{{ route('dashboard.posts.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Manage articles</a>
                <a href="{{ route('blog.index') }}" target="_blank" class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">View your blog ↗</a>
            </div>
        </div>

        <section aria-labelledby="recent-articles-heading" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="recent-articles-heading" class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-white">Recently updated</h2>
                    <p class="mt-1 text-sm text-zinc-500">Your latest article activity.</p>
                </div>
                <a href="{{ route('dashboard.posts.index') }}" class="text-sm font-medium text-zinc-700 underline underline-offset-4 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">Manage all articles</a>
            </div>

            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                @forelse ($recentPosts as $post)
                    <article class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4 last:border-b-0 dark:border-zinc-800">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="truncate font-medium text-zinc-900 dark:text-white">{{ $post->title }}</h3>
                                <span class="rounded-full px-2 py-0.5 text-xs {{ $post->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ ucfirst($post->status) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-zinc-500">Updated {{ $post->updated_at->diffForHumans() }} · {{ number_format($post->views) }} {{ \Illuminate\Support\Str::plural('view', $post->views) }}</p>
                        </div>
                        <a href="{{ route('dashboard.posts.edit', $post) }}" class="text-sm font-medium text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">Edit</a>
                    </article>
                @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-sm text-zinc-500">No articles yet. Your first story starts here.</p>
                        <a href="{{ route('dashboard.posts.create') }}" class="mt-3 inline-flex text-sm font-medium text-zinc-900 underline underline-offset-4 dark:text-white">Write your first article</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
