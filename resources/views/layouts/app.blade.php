<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Styledinee') — Premium Bespoke Tailoring, Uyo</title>
    <meta name="description" content="@yield('meta_description', 'Styledinee — premium bespoke tailoring, dry cleaning, alterations and pickup & delivery in Uyo, Nigeria.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:        #C9A84C;
            --gold-light:  #F0E0B0;
            --black:       #0D0D0D;
            --white:       #FFFFFF;
            --off-white:   #FAFAF8;
            --gray-100:    #F5F5F5;
            --gray-200:    #E8E8E8;
            --gray-400:    #9CA3AF;
            --gray-600:    #6B7280;
            --gray-800:    #1F2937;
            --text:        #111111;
            --text-muted:  #6B7280;
            --border:      #E5E7EB;
            --radius:      10px;
            --shadow:      0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Jost', sans-serif;
            background: var(--gray-100);
            color: var(--text);
            font-weight: 400;
            line-height: 1.7;
            font-size: 16px;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 500;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }

        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }

        /* ── Gold Accent ── */
        .gold { color: var(--gold); }

        /* ── Nav ── */
        .nav {
            position: sticky;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5vw;
            height: 64px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .nav__logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            color: var(--black);
        }

        .nav__logo span { color: var(--gold); }

        .nav__links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .nav__links a {
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding-bottom: 2px;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-color 0.2s;
        }

        .nav__links a:hover { color: var(--text); }
        .nav__links a.active { color: var(--black); border-bottom-color: var(--gold); }

        .nav__cta {
            color: var(--gold) !important;
            font-weight: 600 !important;
            border-bottom: none !important;
        }

        .nav__cta:hover {
            color: var(--black) !important;
        }

        .nav__account {
            padding: 0.5rem 1.3rem;
            background: var(--black);
            color: var(--white) !important;
            border: none !important;
            border-bottom: none !important;
            border-radius: 8px;
            font-size: 0.78rem !important;
            font-weight: 600;
            letter-spacing: 0.1em !important;
            text-transform: uppercase;
            transition: background 0.2s;
            padding-bottom: 0.5rem !important;
        }

        .nav__account:hover {
            background: var(--gray-800);
            color: var(--white) !important;
        }

        .nav__hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 4px;
        }

        .nav__hamburger span {
            display: block;
            width: 22px;
            height: 1.5px;
            background: var(--black);
            transition: all 0.3s;
        }

        /* ── Mobile Nav ── */
        @media (max-width: 768px) {
            .nav__links { display: none; }
            .nav__hamburger { display: flex; }

            .nav__links.open {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                position: fixed;
                top: 64px; left: 0; right: 0;
                background: var(--white);
                padding: 1.5rem 5vw 1.25rem;
                gap: 1.25rem;
                border-bottom: 1px solid var(--border);
                z-index: 99;
            }
        }

        /* ── Main ── */
        main { min-height: calc(100vh - 64px); }

        /* ── Section ── */
        .section {
            padding: 5rem 5vw;
        }

        .section--white { background: var(--white); }
        .section--off { background: var(--gray-100); }

        .section__label {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.75rem;
        }

        .section__title {
            font-size: clamp(2rem, 4vw, 3.2rem);
            color: var(--black);
            margin-bottom: 1.25rem;
        }

        .section__subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            max-width: 540px;
            margin-bottom: 3rem;
        }

        /* ── Gold Divider ── */
        .divider {
            width: 48px;
            height: 2px;
            background: var(--gold);
            margin: 1.5rem 0;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            border-radius: 8px;
            font-family: 'Jost', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn--gold {
            background: var(--gold);
            color: var(--white);
        }

        .btn--gold:hover {
            background: #b8943d;
        }

        .btn--outline {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text);
        }

        .btn--outline:hover {
            border-color: var(--black);
        }

        .btn--dark {
            background: var(--black);
            color: var(--white);
        }

        .btn--dark:hover {
            background: var(--gray-800);
        }

        /* ── Cards ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: border-color 0.25s, transform 0.25s;
        }

        .card:hover {
            border-color: var(--gold);
            transform: translateY(-4px);
        }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }

        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .section { padding: 3.5rem 5vw; }
        }

        /* ── Forms ── */
        .form-group { margin-bottom: 1.25rem; }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 0.75rem 1rem;
            font-family: 'Jost', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--gold);
        }

        .form-group textarea { resize: vertical; min-height: 120px; }

        /* ── Footer ── */
        .footer {
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 4rem 5vw 2rem;
        }

        .footer__grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer__logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            margin-bottom: 1rem;
            color: var(--black);
        }

        .footer__logo span { color: var(--gold); }

        .footer__tagline {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .footer__heading {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1.25rem;
        }

        .footer__links { list-style: none; }

        .footer__links li { margin-bottom: 0.6rem; }

        .footer__links a {
            font-size: 0.88rem;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .footer__links a:hover { color: var(--gold); }

        .footer__bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            font-size: 0.78rem;
            color: var(--gray-400);
        }

        @media (max-width: 768px) {
            .footer__grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .footer__bottom { flex-direction: column; gap: 0.5rem; text-align: center; }
        }

        @media (max-width: 480px) {
            .footer__grid { grid-template-columns: 1fr; }
        }

        /* ── Page header ── */
        .page-header {
            padding: 3rem 5vw;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .page-header .section__title {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
        }

        /* ── Filter bar ── */
        .filter-bar {
            padding: 1.5rem 5vw;
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .filter-btn {
            padding: 0.5rem 1.25rem;
            font-family: 'Jost', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            border: 1.5px solid var(--border);
            border-radius: 100px;
            background: transparent;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--gold);
            color: var(--white);
            border-color: var(--gold);
        }

        /* ── Footer Gallery Strip ── */
        .footer-strip {
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 2rem 5vw 0;
        }

        .footer-strip__label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .footer-strip__grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 4px;
        }

        .footer-strip__item {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
            display: block;
        }

        .footer-strip__item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s;
        }

        .footer-strip__overlay {
            position: absolute;
            inset: 0;
            background: rgba(201,168,76,0.0);
            transition: background 0.3s;
        }

        .footer-strip__item:hover img { transform: scale(1.08); }
        .footer-strip__item:hover .footer-strip__overlay { background: rgba(201,168,76,0.2); }

        @media (max-width: 768px) {
            .footer-strip__grid { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 480px) {
            .footer-strip__grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="nav">
    <a href="{{ url('/') }}" class="nav__logo">STYLED<span>INEE</span></a>

    <ul class="nav__links" id="navLinks">
        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
        <li><a href="{{ url('/services') }}" class="{{ request()->is('services') ? 'active' : '' }}">Services</a></li>
        <li><a href="{{ url('/shop') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Shop</a></li>
        <li><a href="{{ url('/gallery') }}" class="{{ request()->is('gallery') ? 'active' : '' }}">Gallery</a></li>
        <li><a href="{{ url('/contact') }}" class="nav__cta">Contact Us</a></li>
        <li><a href="{{ url('/account/login') }}" class="nav__account">My Account</a></li>
    </ul>

    <button class="nav__hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<main>
    @yield('content')
</main>

{{-- Footer Gallery Strip --}}
@php
    $stripPlaceholders = [
        'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=400&q=80',
        'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=400&q=80',
        'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=400&q=80',
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=400&q=80',
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=400&q=80',
        'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=400&q=80',
        'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&q=80',
        'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&q=80',
    ];
    shuffle($stripPlaceholders);
    $hasFooterGallery = isset($footerGallery) && $footerGallery->count() > 0;
@endphp
<div class="footer-strip">
    <div class="footer-strip__label">
        <span class="section__label" style="margin: 0;">Follow Our Work</span>
        <a href="{{ url('/gallery') }}" style="font-size: 0.78rem; color: var(--gold); font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase;">View Gallery →</a>
    </div>
    <div class="footer-strip__grid">
        @for ($fi = 0; $fi < 8; $fi++)
            @if ($hasFooterGallery)
                @php $fItem = $footerGallery[$fi % $footerGallery->count()]; @endphp
                <a href="{{ url('/gallery') }}" class="footer-strip__item">
                    <img src="{{ Storage::url($fItem->image) }}" alt="{{ $fItem->title }}" loading="lazy">
                    <div class="footer-strip__overlay"></div>
                </a>
            @else
                <a href="{{ url('/gallery') }}" class="footer-strip__item">
                    <img src="{{ $stripPlaceholders[$fi] }}" alt="Fashion portfolio" loading="lazy">
                    <div class="footer-strip__overlay"></div>
                </a>
            @endif
        @endfor
    </div>
</div>

<footer class="footer">
    <div class="footer__grid">
        <div>
            <div class="footer__logo">STYLED<span>INEE</span></div>
            <p class="footer__tagline">Premium bespoke tailoring, dry cleaning,<br>alterations and delivery — crafted with precision in Uyo, Nigeria.</p>
        </div>
        <div>
            <div class="footer__heading">Services</div>
            <ul class="footer__links">
                <li><a href="{{ url('/services') }}">Bespoke Tailoring</a></li>
                <li><a href="{{ url('/services') }}">Dry Cleaning</a></li>
                <li><a href="{{ url('/services') }}">Alterations</a></li>
                <li><a href="{{ url('/services') }}">Pickup & Delivery</a></li>
            </ul>
        </div>
        <div>
            <div class="footer__heading">Quick Links</div>
            <ul class="footer__links">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/shop') }}">Shop</a></li>
                <li><a href="{{ url('/gallery') }}">Gallery</a></li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
            </ul>
        </div>
        <div>
            <div class="footer__heading">Contact</div>
            <ul class="footer__links">
                <li><a href="tel:+2340000000000">+234 000 000 0000</a></li>
                <li><a href="mailto:hello@styledinee.com">hello@styledinee.com</a></li>
                <li>Uyo, Akwa Ibom State,<br>Nigeria</li>
            </ul>
        </div>
    </div>
    <div class="footer__bottom">
        <span>&copy; {{ date('Y') }} Styledinee. All rights reserved.</span>
        <span>Crafted with care in Uyo, Nigeria</span>
    </div>
</footer>

<script>
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    hamburger.addEventListener('click', () => navLinks.classList.toggle('open'));
</script>

@stack('scripts')
</body>
</html>
