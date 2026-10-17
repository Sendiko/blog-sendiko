<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => 'Writing', 'titleSuffix' => 'Sendiko'])
        <meta name="description" content="Essays and notes on creative work, the internet, and the everyday.">
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
                <a class="header-cta" href="{{ route('home') }}">Home <span aria-hidden="true">↗</span></a>
            </div>
        </header>

        <main class="blog-index section-wrap">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">The journal</p>
                    <h1>All <span>dispatches</span></h1>
                </div>
            </div>

            @if ($posts->isEmpty())
                <div class="blog-empty">
                    <h2>The next story is still being written.</h2>
                    <p>Check back soon for a new dispatch.</p>
                </div>
            @else
                <div class="blog-index-grid">
                    @foreach ($posts as $post)
                        <article class="post-card">
                            <a class="post-card__image" href="{{ route('blog.show', $post->slug) }}">
                                @if ($post->cover_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->cover_image) }}" alt="{{ $post->title }}" loading="lazy">
                                @else
                                    <span class="post-card__placeholder" aria-hidden="true">s.</span>
                                @endif
                            </a>
                            <div class="post-meta">
                                <span>{{ $post->author->name }}</span><span>·</span>
                                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M d, Y') }}</time>
                            </div>
                            <h2><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
                            @if ($post->excerpt)
                                <p>{{ $post->excerpt }}</p>
                            @endif
                            <a class="text-link" href="{{ route('blog.show', $post->slug) }}">Read article <span aria-hidden="true">↗</span></a>
                        </article>
                    @endforeach
                </div>
                <div class="blog-pagination">{{ $posts->links() }}</div>
            @endif
        </main>

        <footer class="site-footer">
            <a class="wordmark" href="{{ route('home') }}">sendiko<span>.</span></a>
            <p>Made with curiosity &amp; a cup of tea.</p>
            <div class="footer-links">
                <a href="{{ route('home') }}">Home</a>
                @if (auth()->check())
                    <a href="{{ route('dashboard.posts.index') }}">Manage writing</a>
                @else
                    <a href="{{ route('login') }}">Writer login</a>
                @endif
                <a href="mailto:hello@sendiko.com">Email me ↗</a>
            </div>
            <span class="copyright">© {{ date('Y') }} Sendiko</span>
        </footer>
    </body>
</html>
