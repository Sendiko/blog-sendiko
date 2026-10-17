<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Notes on creative work, the internet, and finding a little more meaning in the everyday.">
        <title>Sendiko — Notes from the in-between</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="blog-page">
        <header class="site-header">
            <div class="site-header__inner">
                <a class="wordmark" href="#top" aria-label="Sendiko home">sendiko<span>.</span></a>
                <nav class="main-nav" aria-label="Main navigation">
                    <a class="main-nav__link main-nav__link--active" href="{{ route('blog.index') }}">Writing</a>
                    <a class="main-nav__link" href="#about">About</a>
                    <a class="main-nav__link" href="#newsletter">Newsletter</a>
                </nav>
                <a class="header-cta" href="#newsletter">Let’s connect <span aria-hidden="true">↗</span></a>
            </div>
        </header>

        <main id="top">
            <section class="intro-wrap">
                <div class="intro">
                    <div class="intro__copy" id="about">
                        <h1>Ideas for a<br>life <span>well noticed.</span></h1>
                        <p class="intro__description">I'm Sendiko — a curious human writing about creative work, the internet, and the small things that make a life.</p>
                        <a class="text-link" href="#latest">Come on in <span aria-hidden="true">↓</span></a>
                    </div>
                    <div class="intro__art" role="img" aria-label="Sunlight falling across a quiet, leafy landscape">
                        <div class="art-note art-note--top">FIELD NOTES <span>NO. 01</span></div>
                        <div class="art-note art-note--bottom">SOMEWHERE, SLOWING DOWN</div>
                        <span class="art-sun"></span>
                        <span class="art-hill art-hill--back"></span>
                        <span class="art-hill art-hill--front"></span>
                        <span class="art-stem art-stem--one"></span>
                        <span class="art-stem art-stem--two"></span>
                    </div>
                </div>
                <div class="intro-footer">
                    <span>Independent thoughts, occasionally sent.</span>
                    <span class="intro-footer__location"><span aria-hidden="true">✳</span> Based somewhere on Earth</span>
                </div>
            </section>

            <section class="latest section-wrap" id="latest">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">The journal</p>
                        <h2>Recent <span>dispatches</span></h2>
                    </div>
                    <a class="text-link section-heading__link" href="{{ route('blog.index') }}">All writing <span aria-hidden="true">↗</span></a>
                </div>

                @if ($posts->isNotEmpty())
                    @php($featuredPost = $posts->first())
                    <div class="featured-post">
                        <a class="featured-post__image" href="{{ route('blog.show', $featuredPost->slug) }}" aria-label="Read {{ $featuredPost->title }}">
                            @if ($featuredPost->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($featuredPost->cover_image) }}" alt="{{ $featuredPost->title }}" loading="lazy">
                            @else
                                <span class="post-card__placeholder" aria-hidden="true">s.</span>
                            @endif
                            <span class="image-index">LATEST DISPATCH</span>
                        </a>
                        <div class="featured-post__content">
                            <div class="post-meta"><span>BY {{ mb_strtoupper($featuredPost->author->name) }}</span><span>·</span><time datetime="{{ $featuredPost->published_at->toDateString() }}">{{ $featuredPost->published_at->format('M d, Y') }}</time></div>
                            <h3><a href="{{ route('blog.show', $featuredPost->slug) }}">{{ $featuredPost->title }}</a></h3>
                            <p>{{ $featuredPost->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($featuredPost->body), 180) }}</p>
                            <div class="post-byline">
                                <div class="avatar">{{ mb_substr($featuredPost->author->name, 0, 1) }}</div>
                                <span>By {{ $featuredPost->author->name }} <span class="byline-separator">·</span> A dispatch</span>
                                <a class="round-arrow" href="{{ route('blog.show', $featuredPost->slug) }}" aria-label="Read {{ $featuredPost->title }}">↗</a>
                            </div>
                        </div>
                    </div>

                    @if ($posts->count() > 1)
                        <div class="post-grid">
                            @foreach ($posts->skip(1) as $post)
                                <article class="post-card">
                                    <a class="post-card__image" href="{{ route('blog.show', $post->slug) }}">
                                        @if ($post->cover_image)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->cover_image) }}" alt="{{ $post->title }}" loading="lazy">
                                        @else
                                            <span class="post-card__placeholder" aria-hidden="true">s.</span>
                                        @endif
                                    </a>
                                    <div class="post-meta"><span>{{ mb_strtoupper($post->author->name) }}</span><span>·</span><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M d, Y') }}</time></div>
                                    <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                    @if ($post->excerpt)
                                        <p>{{ $post->excerpt }}</p>
                                    @endif
                                    <a class="read-time" href="{{ route('blog.show', $post->slug) }}">READ ARTICLE ↗</a>
                                </article>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="blog-empty">
                        <h2>The next story is still being written.</h2>
                        <p>Check back soon for a new dispatch.</p>
                    </div>
                @endif

                <div class="topics">
                    <span class="topics__label">A few things I write about</span>
                    <div class="topic-list">
                        <a href="#latest">Creative practice <span>08</span></a>
                        <a href="#latest">The internet <span>05</span></a>
                        <a href="#latest">Personal notes <span>12</span></a>
                        <a href="#latest">Things I’m learning <span>04</span></a>
                    </div>
                </div>
            </section>

            <section class="newsletter" id="newsletter">
                <div class="newsletter__inner">
                    <div>
                        <p class="eyebrow">A good thing for your inbox</p>
                        <h2>A note, now<br>and <span>then.</span></h2>
                    </div>
                    <div class="newsletter__form-wrap">
                        <p>Occasional letters about what I’m reading, making, and noticing. No noise, ever.</p>
                        <form class="newsletter-form" action="mailto:hello@sendiko.com" method="post" enctype="text/plain">
                            <label class="sr-only" for="email-address">Your email address</label>
                            <input id="email-address" type="email" name="email" placeholder="Your email address" required>
                            <button type="submit">Count me in <span aria-hidden="true">↗</span></button>
                        </form>
                        <span class="newsletter__fine-print">A little letter from me to you. Unsubscribe whenever.</span>
                    </div>
                </div>
                <span class="newsletter-star" aria-hidden="true">✳</span>
            </section>
        </main>

        <footer class="site-footer">
            <a class="wordmark" href="#top">sendiko<span>.</span></a>
            <p>Made with curiosity & a cup of tea.</p>
            <div class="footer-links">
                <a href="{{ route('blog.index') }}">Writing</a>
                <a href="#about">About</a>
                @if (auth()->check())
                    <a href="{{ route('dashboard.posts.index') }}">Manage writing</a>
                @else
                    <a href="{{ route('login') }}">Writer login</a>
                @endif
                <a href="mailto:hello@sendiko.com">Email me <span aria-hidden="true">↗</span></a>
            </div>
            <span class="copyright">© {{ date('Y') }} Sendiko</span>
        </footer>
    </body>
</html>
