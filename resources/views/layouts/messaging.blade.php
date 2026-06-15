<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Messaging') — Styledinee</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
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
        body { font-family: 'Jost', sans-serif; background: var(--gray-100); color: var(--text); font-size: 15px; line-height: 1.6; min-height: 100vh; }
        h1,h2,h3,h4 { font-family: 'Cormorant Garamond', serif; font-weight: 500; line-height: 1.2; }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }

        /* ── Top Nav ── */
        .c-nav {
            position: sticky; top: 0; z-index: 100;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 5vw; height: 64px;
        }
        .c-nav__logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem; font-weight: 600; letter-spacing: 0.06em;
            color: var(--black);
        }
        .c-nav__logo span { color: var(--gold); }
        .c-nav__links { display: flex; align-items: center; gap: 2rem; list-style: none; }
        .c-nav__links a {
            font-size: 0.8rem; font-weight: 500; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--text-muted);
            padding-bottom: 2px; border-bottom: 2px solid transparent;
            transition: color .2s, border-color .2s;
        }
        .c-nav__links a:hover { color: var(--text); }
        .c-nav__links a.active { color: var(--black); border-bottom-color: var(--gold); }
        .c-nav__logout {
            font-size: 0.78rem; font-weight: 500; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--text-muted);
            background: none; border: none; cursor: pointer; padding: 0;
            transition: color .2s;
        }
        .c-nav__logout:hover { color: var(--black); }
        .c-nav__hamburger {
            display: none; flex-direction: column; gap: 5px;
            background: none; border: none; cursor: pointer; padding: 4px;
        }
        .c-nav__hamburger span { display: block; width: 22px; height: 1.5px; background: var(--black); transition: all .3s; }

        /* ── Mobile nav ── */
        @media (max-width: 700px) {
            .c-nav__links, .c-nav__logout, .c-nav__logout-form { display: none; }
            .c-nav__hamburger { display: flex; }
            .c-nav__links.open {
                display: flex; flex-direction: column; align-items: flex-start;
                position: fixed; top: 64px; left: 0; right: 0;
                background: var(--white); border-bottom: 1px solid var(--border);
                padding: 1.5rem 5vw 1.25rem; gap: 1.25rem; z-index: 99;
            }
            .c-nav__links.open + .c-nav__logout-form {
                display: block;
                position: fixed; top: 64px; left: 0; right: 0; z-index: 99;
                background: var(--white); border-bottom: 1px solid var(--border);
                padding: 0 5vw 1.25rem;
            }
        }

        /* ── Mobile content ── */
        @media (max-width: 640px) {
            .c-page { padding: 1.5rem 1rem 3rem; }
            .page-title { font-size: 1.6rem; }
            .card { padding: 1rem; }
            .tbl th, .tbl td { padding: .6rem .6rem; font-size: .82rem; }
            .referral-box { flex-direction: column; align-items: flex-start; }
            .referral-code { font-size: 1.3rem; }
            .section-head h2 { font-size: 1.2rem; }
            .order-row__date { display: none; }
            .stat-card__value { font-size: 1.6rem; }
        }

        /* ── Page wrapper ── */
        .c-page { max-width: 960px; margin: 0 auto; padding: 2rem 5vw 4rem; }

        /* ── Cards ── */
        .card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);
        }
        .card + .card { margin-top: 1rem; }

        /* ── Page title ── */
        .page-title { font-size: 2rem; font-weight: 500; margin-bottom: 0.25rem; }
        .page-subtitle { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.75rem; }

        /* ── Stat cards ── */
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        @media(max-width:640px) { .stats { grid-template-columns: 1fr; } }
        .stat-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 1.25rem 1.5rem; box-shadow: var(--shadow);
        }
        .stat-card__label { font-size: 0.72rem; font-weight: 500; letter-spacing: .12em; text-transform: uppercase; color: var(--text-muted); margin-bottom: .5rem; }
        .stat-card__value { font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 500; color: var(--black); }
        .stat-card__value.gold { color: var(--gold); }
        .stat-card__sub { font-size: 0.8rem; color: var(--text-muted); margin-top: .25rem; }

        /* ── Status badges ── */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 100px; font-size: 0.72rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
        }
        .badge::before { content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background:currentColor; opacity:.6; }
        .badge--pending    { background:#FFF8E7; color:#B45309; }
        .badge--confirmed  { background:#EFF6FF; color:#1D4ED8; }
        .badge--in_progress{ background:#F5F3FF; color:#7C3AED; }
        .badge--ready      { background:#F0FDF4; color:#166534; }
        .badge--handed_over{ background:#EDE9FE; color:#6D28D9; }
        .badge--dispatched { background:#E0F2FE; color:#075985; }
        .badge--delivered  { background:#F0FDF4; color:#14532D; }
        .badge--cancelled  { background:#FEF2F2; color:#991B1B; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .65rem 1.5rem; border-radius: 8px; font-family: 'Jost', sans-serif;
            font-size: .8rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
            cursor: pointer; border: none; transition: all .2s;
        }
        .btn--gold    { background: var(--gold); color: var(--white); }
        .btn--gold:hover { background: #b8943d; }
        .btn--outline { background: transparent; border: 1.5px solid var(--border); color: var(--text); }
        .btn--outline:hover { border-color: var(--black); }
        .btn--sm { padding: .45rem 1rem; font-size: .72rem; }
        .btn:disabled { opacity: .5; cursor: not-allowed; }

        /* ── Form inputs ── */
        .field { margin-bottom: 1.25rem; }
        .field label { display: block; font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted); margin-bottom: .5rem; }
        .field input {
            width: 100%; padding: .75rem 1rem; border: 1.5px solid var(--border);
            border-radius: 8px; font-family: 'Jost', sans-serif; font-size: .95rem;
            background: var(--white); color: var(--text); outline: none; transition: border-color .15s;
        }
        .field input:focus { border-color: var(--gold); }
        .field .hint { font-size: .75rem; color: var(--text-muted); margin-top: .35rem; }
        .field .err  { font-size: .75rem; color: #dc2626; margin-top: .35rem; }

        /* ── Divider ── */
        .divider { height: 1px; background: var(--border); margin: 1.5rem 0; }

        /* ── Section heading ── */
        .section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .section-head h2 { font-size: 1.4rem; }

        /* ── Table ── */
        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .tbl { width: 100%; border-collapse: collapse; font-size: .88rem; }
        .tbl th { font-size: .7rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted); padding: .6rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .tbl td { padding: .8rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .tbl tr:last-child td { border-bottom: none; }

        /* ── Timeline ── */
        .timeline { display: flex; align-items: center; gap: 0; margin: 1rem 0; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px; }
        .tl-step { display: flex; align-items: center; flex-shrink: 0; }
        .tl-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--border); border: 2px solid var(--border); flex-shrink:0; }
        .tl-dot.done  { background: var(--gold); border-color: var(--gold); }
        .tl-dot.active{ background: var(--white); border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,168,76,.2); }
        .tl-line { width: 32px; height: 1px; background: var(--border); flex-shrink: 0; }
        .tl-line.done { background: var(--gold); }
        .tl-label { font-size: .65rem; text-align: center; color: var(--text-muted); margin-top: 4px; white-space: nowrap; }
        .tl-wrap { display: flex; flex-direction: column; align-items: center; }

        /* ── Field component classes ── */
        .field__label { display: block; font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted); margin-bottom: .5rem; }
        .field__input {
            width: 100%; padding: .75rem 1rem; border: 1.5px solid var(--border);
            border-radius: 8px; font-family: 'Jost', sans-serif; font-size: .95rem;
            background: var(--white); color: var(--text); outline: none; transition: border-color .15s;
        }
        .field__input:focus { border-color: var(--gold); }
        .field__error { display: block; font-size: .75rem; color: #dc2626; margin-top: .35rem; }

        /* ── Btn primary ── */
        .btn--primary { background: var(--black); color: var(--white); }
        .btn--primary:hover { background: var(--gray-800); }
        .btn--loading { opacity: .6; cursor: not-allowed; }

        /* ── Vertical timeline (order status logs) ── */
        .timeline { display: flex; flex-direction: column; gap: 0; }
        .timeline__item { display: flex; gap: .9rem; }
        .timeline__item:not(:last-child) .timeline__dot::after {
            content: ''; display: block; width: 1px; background: var(--border);
            flex: 1; margin: 4px auto 0;
        }
        .timeline__dot { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; width: 14px; }
        .timeline__dot::before { content: ''; display: block; width: 10px; height: 10px; border-radius: 50%; background: var(--gold); border: 2px solid var(--gold); flex-shrink: 0; }
        .timeline__body { padding-bottom: 1rem; }
        .timeline__label { font-size: .88rem; font-weight: 600; color: var(--text); }
        .timeline__note  { font-size: .82rem; color: var(--text-muted); margin-top: .15rem; }
        .timeline__time  { font-size: .75rem; color: var(--text-muted); margin-top: .2rem; }

        /* ── Referral box ── */
        .referral-box {
            border: 1.5px dashed var(--gold); border-radius: var(--radius);
            padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .referral-code { font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; font-weight: 500; letter-spacing: .06em; }
        .copy-btn { font-size: .72rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gold); background: none; border: none; cursor: pointer; }
    </style>
</head>
<body>

<nav class="c-nav">
    <a href="{{ url('/') }}" class="c-nav__logo">STYLE<span>DINEE</span></a>

    <a href="/admin" class="c-nav__logout">Back to Admin</a>
</nav>

<main class="c-page">
    @yield('content')
</main>

@livewireScripts

@stack('scripts')
</body>
</html>
