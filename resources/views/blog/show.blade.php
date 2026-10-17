<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $post->title, 'titleSuffix' => 'Sendiko'])
        <meta name="description" content="{{ $post->excerpt ?: Str::limit(strip_tags($post->body), 160) }}">
    </head>
    <body class="blog-page">
        <header class="site-header">
            <div class="site-header__inner">
                <a class="wordmark" href="{{ route('home') }}">sendiko<span>.</span></a>
                <nav class="main-nav" aria-label="Main navigation">
                    <a class="main-nav__link main-nav__link--active" href="{{ route('blog.index') }}">Writing</a>
                    <a class="main-nav__link" href="{{ route('home') }}#about">About</a>
                    <a class="main-nav__link" href="{{ route('home') }}#newsletter">Newsletter</a>
                </nav>
                <a class="header-cta" href="{{ route('blog.index') }}">All writing <span aria-hidden="true">↗</span></a>
            </div>
        </header>

        <main class="article-page">
            <article>
                <header class="article-header">
                    <a class="article-back" href="{{ route('blog.index') }}">← Back to all writing</a>
                    <p class="eyebrow">A dispatch from Sendiko</p>
                    <h1>{{ $post->title }}</h1>
                    @if ($post->excerpt)
                        <p class="article-excerpt">{{ $post->excerpt }}</p>
                    @endif
                    <div class="article-meta">
                        <span>By {{ $post->author->name }}</span><span>·</span>
                        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('F j, Y') }}</time>
                    </div>
                </header>

                @if ($post->cover_image)
                    <img class="article-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->cover_image) }}" alt="{{ $post->title }}">
                @endif

                <div class="article-content">
                    {!! $post->renderedBody() !!}
                </div>
            </article>
            <div class="article-end">
                <span aria-hidden="true">✳</span>
                <p>Thanks for reading.</p>
                <a class="text-link" href="{{ route('blog.index') }}">More writing <span aria-hidden="true">↗</span></a>
            </div>
        </main>

        <footer class="site-footer">
            <a class="wordmark" href="{{ route('home') }}">sendiko<span>.</span></a>
            <p>Made with curiosity &amp; a cup of tea.</p>
            <div class="footer-links">
                <a href="{{ route('blog.index') }}">Writing</a>
                @if (auth()->check())
                    <a href="{{ route('dashboard.posts.index') }}">Manage writing</a>
                @else
                    <a href="{{ route('login') }}">Writer login</a>
                @endif
                <a href="mailto:rizky@sendiko.dev">Email me ↗</a>
            </div>
            <span class="copyright">© {{ date('Y') }} Sendiko</span>
        </footer>
    </body>
</html>
