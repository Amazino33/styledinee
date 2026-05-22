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
            --gold:   #C9A84C;
            --gold-light: #DFC07A;
            --black:  #0D0D0D;
            --off-black: #1A1A1A;
            --white:  #FAFAF8;
            --gray:   #6B6B6B;
            --border: rgba(201,168,76,0.25);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Jost', sans-serif;
            background: var(--black);
            color: var(--white);
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
        .gold-border { border-color: var(--gold); }

        /* ── Nav ── */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5vw;
            height: 72px;
            background: rgba(13,13,13,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }

        .nav__logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            color: var(--white);
        }

        .nav__logo span { color: var(--gold); }

        .nav__links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            list-style: none;
        }

        .nav__links a {
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(250,250,248,0.75);
            transition: color 0.2s;
        }

        .nav__links a:hover,
        .nav__links a.active { color: var(--gold); }

        .nav__cta {
            padding: 0.5rem 1.4rem;
            border: 1px solid var(--gold);
            color: var(--gold) !important;
            font-size: 0.78rem !important;
            letter-spacing: 0.14em !important;
            text-transform: uppercase;
            transition: background 0.2s, color 0.2s !important;
        }

        .nav__cta:hover {
            background: var(--gold);
            color: var(--black) !important;
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
            width: 24px;
            height: 1.5px;
            background: var(--white);
            transition: all 0.3s;
        }

        /* ── Mobile Nav ── */
        @media (max-width: 768px) {
            .nav__links { display: none; }
            .nav__hamburger { display: flex; }

            .nav__links.open {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 72px; left: 0; right: 0;
                background: var(--off-black);
                padding: 2rem 5vw;
                gap: 1.5rem;
                border-bottom: 1px solid var(--border);
            }
        }

        /* ── Main ── */
        main { padding-top: 72px; }

        /* ── Section ── */
        .section {
            padding: 6rem 5vw;
        }

        .section--dark { background: var(--black); }
        .section--off { background: var(--off-black); }

        .section__label {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.75rem;
        }

        .section__title {
            font-size: clamp(2rem, 4vw, 3.2rem);
            margin-bottom: 1.25rem;
        }

        .section__subtitle {
            font-size: 1rem;
            color: rgba(250,250,248,0.6);
            max-width: 540px;
            margin-bottom: 3rem;
        }

        /* ── Gold Divider ── */
        .divider {
            width: 48px;
            height: 1px;
            background: var(--gold);
            margin: 1.5rem 0;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            font-family: 'Jost', sans-serif;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s;
            border: none;
        }

        .btn--gold {
            background: var(--gold);
            color: var(--black);
        }

        .btn--gold:hover {
            background: var(--gold-light);
        }

        .btn--outline {
            background: transparent;
            border: 1px solid var(--gold);
            color: var(--gold);
        }

        .btn--outline:hover {
            background: var(--gold);
            color: var(--black);
        }

        /* ── Cards ── */
        .card {
            background: var(--off-black);
            border: 1px solid var(--border);
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
            .section { padding: 4rem 5vw; }
        }

        /* ── Forms ── */
        .form-group { margin-bottom: 1.25rem; }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(250,250,248,0.5);
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--white);
            padding: 0.85rem 1rem;
            font-family: 'Jost', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--gold);
        }

        .form-group textarea { resize: vertical; min-height: 120px; }

        /* ── Footer ── */
        .footer {
            background: var(--off-black);
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
        }

        .footer__logo span { color: var(--gold); }

        .footer__tagline {
            font-size: 0.9rem;
            color: rgba(250,250,248,0.5);
            line-height: 1.8;
        }

        .footer__heading {
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1.25rem;
        }

        .footer__links { list-style: none; }

        .footer__links li { margin-bottom: 0.6rem; }

        .footer__links a {
            font-size: 0.88rem;
            color: rgba(250,250,248,0.5);
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
            color: rgba(250,250,248,0.35);
        }

        @media (max-width: 768px) {
            .footer__grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .footer__bottom { flex-direction: column; gap: 0.5rem; text-align: center; }
        }

        @media (max-width: 480px) {
            .footer__grid { grid-template-columns: 1fr; }
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
    </ul>

    <button class="nav__hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<main>
    @yield('content')
</main>

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
