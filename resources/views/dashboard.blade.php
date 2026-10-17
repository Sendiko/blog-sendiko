<x-layouts::app :title="__('Dashboard')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 px-6 py-10">
        <div>
            <p class="text-sm text-zinc-500">Your writing, all in one place.</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">Welcome back, {{ auth()->user()->name }}.</h1>
        </div>
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
    </div>
</x-layouts::app>
