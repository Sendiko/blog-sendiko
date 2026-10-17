<x-layouts::app :title="$post->exists ? __('Edit article') : __('Write an article')">
    <div class="mx-auto max-w-5xl space-y-7 px-6 py-10">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ route('dashboard.posts.index') }}" class="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white">← All articles</a>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $post->exists ? 'Edit article' : 'Write an article' }}</h1>
            </div>
            @if ($post->exists && $post->status === 'published')
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-sm font-medium text-zinc-600 underline underline-offset-4 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">View published article ↗</a>
            @endif
        </div>

        @if (session('status'))
            <p role="status" class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800 dark:bg-green-950 dark:text-green-200">{{ session('status') }}</p>
        @endif

        <form
            action="{{ $post->exists ? route('dashboard.posts.update', $post) : route('dashboard.posts.store') }}"
            method="post"
            enctype="multipart/form-data"
            class="space-y-7"
            data-article-form
            data-image-upload-url="{{ route('dashboard.posts.images.store') }}"
        >
            @csrf
            @if ($post->exists)
                @method('PUT')
            @endif

            <div class="space-y-2">
                <label for="title" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Title</label>
                <input id="title" name="title" value="{{ old('title', $post->title) }}" required maxlength="180" placeholder="Give your story a title" class="w-full rounded-lg border border-zinc-300 bg-white px-4 py-3 text-lg text-zinc-900 outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:ring-zinc-700">
                @error('title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label for="excerpt" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Short description <span class="font-normal text-zinc-500">(optional)</span></label>
                <textarea id="excerpt" name="excerpt" rows="2" maxlength="500" placeholder="A sentence or two to introduce the article" class="w-full rounded-lg border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:ring-zinc-700">{{ old('excerpt', $post->excerpt) }}</textarea>
                @error('excerpt') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-[1fr_220px]">
                <div class="space-y-2">
                    <label for="cover_image" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Cover photo <span class="font-normal text-zinc-500">(optional, up to 5 MB)</span></label>
                    @if ($post->cover_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->cover_image) }}" alt="Current cover photo" class="mb-3 h-36 w-full rounded-lg object-cover sm:max-w-sm">
                    @endif
                    <input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-zinc-600 file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-800 hover:file:bg-zinc-200 dark:text-zinc-300 dark:file:bg-zinc-800 dark:file:text-zinc-200">
                    @error('cover_image') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label for="status" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Status</label>
                    <select id="status" name="status" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                        <option value="draft" @selected(old('status', $post->status ?: 'draft') === 'draft')>Draft — only you can see it</option>
                        <option value="published" @selected(old('status', $post->status ?: 'draft') === 'published')>Published — visible to everyone</option>
                    </select>
                    @error('status') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <label for="body" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Article <span class="font-normal text-zinc-500">— Markdown supported</span></label>
                    <div>
                        <input id="inline-image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="sr-only" data-inline-image>
                        <button type="button" data-image-upload class="rounded-md border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Upload photo</button>
                    </div>
                </div>
                <p class="text-xs text-zinc-500">Use the upload button to insert an image into your article. Maximum image size: 5 MB.</p>
                <textarea id="body" name="body" rows="24" required maxlength="50000" placeholder="# Your story starts here&#10;&#10;Write in Markdown: **bold**, *italic*, [links](https://example.com), and more." class="w-full resize-y rounded-lg border border-zinc-300 bg-white px-4 py-4 font-mono text-sm leading-7 text-zinc-900 outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:ring-zinc-700">{{ old('body', $post->body) }}</textarea>
                <p data-upload-status role="status" aria-live="polite" class="min-h-5 text-xs text-zinc-500"></p>
                @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <details class="rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <summary class="cursor-pointer font-medium text-zinc-700 dark:text-zinc-200">Markdown quick reference</summary>
                <div class="mt-3 grid gap-x-8 gap-y-2 text-xs text-zinc-500 sm:grid-cols-2">
                    <p><code># Heading</code> — section heading</p>
                    <p><code>**bold**</code> or <code>*italic*</code> — emphasis</p>
                    <p><code>[link](https://...)</code> — link</p>
                    <p><code>![alt text](image-url)</code> — image</p>
                    <p><code>&gt; quote</code> — block quote</p>
                    <p><code>- item</code> — bulleted list</p>
                </div>
            </details>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <a href="{{ route('dashboard.posts.index') }}" class="text-sm text-zinc-600 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">Cancel</a>
                <button type="submit" class="rounded-lg bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                    {{ $post->exists ? 'Save article' : 'Create article' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::app>
