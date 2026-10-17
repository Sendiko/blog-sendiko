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
                    <a class="main-nav__link main-nav__link--active" href="#latest">Writing</a>
                    <a class="main-nav__link" href="#about">About</a>
                    <a class="main-nav__link" href="#newsletter">Newsletter</a>
                </nav>
                <a class="header-cta" href="#newsletter">Let’s connect <span aria-hidden="true">↗</span></a>
            </div>
        </header>

        <main id="top">
            <section class="intro-wrap">
                <div class="intro">
                    <div class="intro__copy">
                        <p class="eyebrow"><span class="status-dot"></span> A little corner of the internet</p>
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
                    <a class="text-link section-heading__link" href="#latest">All writing <span aria-hidden="true">↗</span></a>
                </div>

                <div class="featured-post">
                    <a class="featured-post__image" href="#newsletter" aria-label="Read The case for making things slowly">
                        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=85" alt="Warm, sunlit creative studio with a work table" loading="lazy">
                        <span class="image-index">01 / THE LONG READ</span>
                    </a>
                    <div class="featured-post__content">
                        <div class="post-meta"><span>CREATIVE PRACTICE</span><span>·</span><time datetime="2025-04-18">APR 18, 2025</time></div>
                        <h3><a href="#newsletter">The case for making things slowly</a></h3>
                        <p>Somewhere along the way, faster became the default. A small argument for taking the long route, following the thread, and letting good work take the time it takes.</p>
                        <div class="post-byline">
                            <div class="avatar">S</div>
                            <span>By Sendiko <span class="byline-separator">·</span> 8 min read</span>
                            <a class="round-arrow" href="#newsletter" aria-label="Read The case for making things slowly">↗</a>
                        </div>
                    </div>
                </div>

                <div class="post-grid">
                    <article class="post-card">
                        <a class="post-card__image post-card__image--blue" href="#newsletter" aria-label="Read A quieter corner of the internet">
                            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=80" alt="Calm, plant-filled workspace by a window" loading="lazy">
                        </a>
                        <div class="post-meta"><span>THE INTERNET</span><span>·</span><time datetime="2025-04-02">APR 02, 2025</time></div>
                        <h3><a href="#newsletter">A quieter corner of the internet</a></h3>
                        <p>On personal websites, open tabs, and finding your way back to the good stuff.</p>
                        <span class="read-time">5 MIN READ</span>
                    </article>
                    <article class="post-card">
                        <a class="post-card__image post-card__image--rose" href="#newsletter" aria-label="Read Things I learned from starting over">
                            <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80" alt="Notebook and pen on a sunlit desk" loading="lazy">
                        </a>
                        <div class="post-meta"><span>PERSONAL NOTES</span><span>·</span><time datetime="2025-03-21">MAR 21, 2025</time></div>
                        <h3><a href="#newsletter">Things I learned from starting over</a></h3>
                        <p>A field guide to being new at something, written by someone still figuring it out.</p>
                        <span class="read-time">6 MIN READ</span>
                    </article>
                    <aside class="about-card" id="about">
                        <div class="about-card__mark" aria-hidden="true">s.</div>
                        <p class="eyebrow">A quick hello</p>
                        <h3>Good to have<br>you <span>here.</span></h3>
                        <p>This is my little home on the web. I write to think out loud, share what I’m learning, and pay closer attention.</p>
                        <a class="text-link" href="#newsletter">A bit more about me <span aria-hidden="true">↗</span></a>
                    </aside>
                </div>

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
                <a href="#latest">Writing</a>
                <a href="#about">About</a>
                <a href="mailto:hello@sendiko.com">Email me <span aria-hidden="true">↗</span></a>
            </div>
            <span class="copyright">© {{ date('Y') }} Sendiko</span>
        </footer>
    </body>
</html>
