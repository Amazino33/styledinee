@extends('layouts.app')

@section('title', 'Styledinee — Premium Bespoke Tailoring')

@php
    $hasGallery = $galleryItems->count() > 0;

    $placeholders = [
        'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
        'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80',
        'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=800&q=80',
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80',
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&q=80',
        'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&q=80',
        'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=800&q=80',
        'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800&q=80',
        'https://images.unsplash.com/photo-1492707892479-7bc8d5a4ee93?w=800&q=80',
        'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&q=80',
        'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&q=80',
        'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800&q=80',
    ];
    shuffle($placeholders);

    // Helper: get image URL — uses gallery if available, falls back to placeholders
    $imgIndex = 0;
    $getImage = function() use ($hasGallery, $galleryItems, $placeholders, &$imgIndex) {
        if ($hasGallery) {
            $item = $galleryItems[$imgIndex % $galleryItems->count()];
            $imgIndex++;
            return Storage::url($item->image);
        }
        return $placeholders[$imgIndex++ % count($placeholders)];
    };
@endphp

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero__content">
        <span class="section__label">Est. Uyo, Nigeria</span>
        <h1 class="hero__title">
            Dressed<br>to Your<br><em>Exact Standard</em>
        </h1>
        <p class="hero__text">
            Bespoke tailoring, expert dry cleaning, precise alterations, and door-to-door pickup &amp; delivery — all under one roof in Uyo.
        </p>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ url('/services') }}" class="btn btn--gold">Explore Services</a>
            <a href="{{ url('/contact') }}" class="btn btn--outline">Book a Consultation</a>
        </div>
    </div>
    <div class="hero__gallery">
        <div class="hero__img" style="background-image: url('{{ $getImage() }}');"></div>
        <div class="hero__img" style="background-image: url('{{ $getImage() }}');"></div>
        <div class="hero__img" style="background-image: url('{{ $getImage() }}');"></div>
    </div>
</section>

{{-- Gallery Showcase --}}
<section class="section section--white">
    <div style="text-align: center; margin-bottom: 3rem;">
        <span class="section__label">Our Portfolio</span>
        <h2 class="section__title">Recent Work</h2>
        <div class="divider" style="margin: 1.5rem auto;"></div>
    </div>

    <div class="masonry">
        @for ($i = 0; $i < 12; $i++)
        <a href="{{ url('/gallery') }}" class="masonry__item">
            <img src="{{ $getImage() }}" alt="Fashion portfolio" loading="lazy">
            <div class="masonry__overlay">
                @if ($hasGallery)
                    @php $gi = $galleryItems[$i % $galleryItems->count()]; @endphp
                    <span class="masonry__title">{{ $gi->title }}</span>
                    @if($gi->category)
                    <span class="masonry__cat">{{ ucfirst(str_replace('_', ' ', $gi->category)) }}</span>
                    @endif
                @else
                    <span class="masonry__title">View Gallery</span>
                @endif
            </div>
        </a>
        @endfor
    </div>

    <div style="text-align: center; margin-top: 2.5rem;">
        <a href="{{ url('/gallery') }}" class="btn btn--outline">View Full Gallery</a>
    </div>
</section>

{{-- Services Overview --}}
<section class="section section--off">
    <span class="section__label">What We Do</span>
    <h2 class="section__title">Our Services</h2>
    <div class="divider"></div>

    <div class="grid-4">
        @php
        $services = [
            ['title' => 'Bespoke Tailoring', 'desc' => 'Custom-crafted garments tailored precisely to your measurements and style preferences.'],
            ['title' => 'Dry Cleaning',     'desc' => 'Professional dry cleaning for your finest fabrics, handled with the utmost care.'],
            ['title' => 'Alterations',      'desc' => 'Expert alterations to ensure every piece fits you perfectly.'],
            ['title' => 'Pickup & Delivery','desc' => 'Convenient door-to-door collection and delivery across Uyo and Nigeria.'],
        ];
        @endphp

        @foreach ($services as $s)
        <a href="{{ url('/services') }}" class="card svc-card" style="overflow: hidden; text-decoration: none;">
            <div class="svc-card__img" style="background-image: url('{{ $getImage() }}');"></div>
            <div style="padding: 1.25rem;">
                <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; color: var(--black);">{{ $s['title'] }}</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.7;">{{ $s['desc'] }}</p>
            </div>
        </a>
        @endforeach
    </div>
</section>

{{-- Fashion Strip --}}
<section class="strip">
    <div class="strip__track">
        @for ($i = 0; $i < 10; $i++)
        <div class="strip__img" style="background-image: url('{{ $getImage() }}');"></div>
        @endfor
        @for ($i = 0; $i < 10; $i++)
        <div class="strip__img" style="background-image: url('{{ $getImage() }}');"></div>
        @endfor
    </div>
</section>

{{-- Why Styledinee --}}
<section class="section section--white">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center;">
        <div>
            <span class="section__label">Why Choose Us</span>
            <h2 class="section__title">Precision Meets Elegance</h2>
            <div class="divider"></div>
            <p style="color: var(--text-muted); margin-bottom: 2rem; line-height: 1.9;">
                At Styledinee, every stitch tells a story. We combine traditional tailoring artistry with modern precision to deliver garments that speak to your identity.
            </p>
            @foreach (['Locally Made, Global Standard', 'Your Measurements, Our Craft', 'On-Time, Every Time', 'Premium Fabrics Only'] as $point)
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; flex-shrink: 0;"></span>
                <span style="font-size: 0.95rem; color: var(--text);">{{ $point }}</span>
            </div>
            @endforeach
            <a href="{{ url('/services') }}" class="btn btn--gold" style="margin-top: 1.5rem;">View All Services</a>
        </div>
        <div style="position: relative;">
            <div style="
                width: 100%; aspect-ratio: 4/5;
                background: url('{{ $getImage() }}') center/cover;
                border-radius: var(--radius);
            "></div>
            <div style="
                position: absolute; bottom: -1.5rem; right: -1.5rem;
                background: var(--gold);
                color: var(--white);
                padding: 1.5rem;
                border-radius: var(--radius);
                font-family: 'Cormorant Garamond', serif;
                font-size: 1.1rem;
                font-weight: 600;
            ">
                Since 2020<br><span style="font-size: 2rem; font-weight: 700; line-height: 1;">500+</span><br>
                <span style="font-size: 0.75rem; font-weight: 400; letter-spacing: 0.1em; text-transform: uppercase;">Happy Clients</span>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="section section--off">
    <div style="text-align: center; margin-bottom: 4rem;">
        <span class="section__label">Testimonials</span>
        <h2 class="section__title">What Our Clients Say</h2>
        <div class="divider" style="margin: 1.5rem auto;"></div>
    </div>

    <div class="grid-3">
        @php
        $testimonials = [
            ['name' => 'Emeka O.', 'role' => 'Corporate Executive', 'text' => 'My suits from Styledinee are impeccable. The attention to detail and quality of tailoring is world-class.'],
            ['name' => 'Ngozi A.', 'role' => 'Bride', 'text' => 'They made my bridal outfit look absolutely breathtaking. I cannot recommend Styledinee highly enough.'],
            ['name' => 'Chidi M.', 'role' => 'Fashion Enthusiast', 'text' => 'The dry cleaning service is top-tier. My agbada came back pristine — better than I sent it.'],
        ];
        @endphp

        @foreach ($testimonials as $t)
        <div class="card" style="padding: 2rem;">
            <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.9; margin-bottom: 1.5rem; font-style: italic;">
                "{{ $t['text'] }}"
            </p>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Cormorant Garamond', serif; font-weight: 600; color: var(--white);">
                    {{ substr($t['name'], 0, 1) }}
                </div>
                <div>
                    <div style="font-size: 0.9rem; font-weight: 500; color: var(--text);">{{ $t['name'] }}</div>
                    <div style="font-size: 0.78rem; color: var(--gold);">{{ $t['role'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA Banner --}}
<section style="
    background: var(--gold);
    padding: 5rem 5vw;
    text-align: center;
">
    <h2 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3rem); color: var(--white); margin-bottom: 1rem; font-weight: 500;">
        Ready to Look Your Best?
    </h2>
    <p style="color: rgba(255,255,255,0.8); margin-bottom: 2rem; font-size: 1rem;">
        Book a consultation today and let us create something exceptional for you.
    </p>
    <a href="{{ url('/contact') }}" class="btn btn--dark">
        Book a Consultation
    </a>
</section>

@endsection

@push('styles')
<style>
    /* ── Hero ── */
    .hero {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 85vh;
        background: var(--white);
        border-bottom: 1px solid var(--border);
    }

    .hero__content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 4rem 5vw;
    }

    .hero__title {
        font-size: clamp(2.8rem, 6vw, 5rem);
        font-weight: 300;
        line-height: 1.05;
        margin-bottom: 1.5rem;
        color: var(--black);
    }

    .hero__title em {
        font-style: italic;
        color: var(--gold);
    }

    .hero__text {
        font-size: 1.05rem;
        color: var(--text-muted);
        max-width: 440px;
        margin-bottom: 2.5rem;
        line-height: 1.8;
    }

    .hero__gallery {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 4px;
    }

    .hero__img {
        background-size: cover;
        background-position: center;
        min-height: 200px;
    }

    .hero__img:first-child {
        grid-row: 1 / 3;
    }

    /* ── Masonry Gallery ── */
    .masonry {
        columns: 4;
        column-gap: 1rem;
    }

    .masonry__item {
        display: block;
        break-inside: avoid;
        margin-bottom: 1rem;
        border-radius: var(--radius);
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }

    .masonry__item img {
        width: 100%;
        display: block;
        transition: transform 0.5s;
    }

    .masonry__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(0deg, rgba(0,0,0,0.65) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.25rem;
    }

    .masonry__item:hover img { transform: scale(1.04); }
    .masonry__item:hover .masonry__overlay { opacity: 1; }

    .masonry__title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.05rem;
        color: #fff;
    }

    .masonry__cat {
        font-size: 0.7rem;
        color: var(--gold);
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-top: 0.2rem;
    }

    /* ── Service Image Cards ── */
    .svc-card { text-decoration: none; }
    .svc-card:hover { transform: translateY(-4px); }

    .svc-card__img {
        width: 100%;
        aspect-ratio: 4/3;
        background-size: cover;
        background-position: center;
        transition: transform 0.5s;
    }

    .svc-card:hover .svc-card__img {
        transform: scale(1.04);
    }

    /* ── Scrolling Strip ── */
    .strip {
        overflow: hidden;
        background: var(--white);
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        padding: 4px 0;
    }

    .strip__track {
        display: flex;
        gap: 4px;
        animation: scroll-strip 40s linear infinite;
        width: max-content;
    }

    .strip__img {
        width: 220px;
        height: 280px;
        background-size: cover;
        background-position: center;
        flex-shrink: 0;
    }

    @keyframes scroll-strip {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    .strip:hover .strip__track {
        animation-play-state: paused;
    }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .masonry { columns: 3; }
    }

    @media (max-width: 768px) {
        .hero {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .hero__gallery {
            height: 320px;
        }

        .masonry { columns: 2; }

        section div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }

        .strip__img {
            width: 160px;
            height: 200px;
        }
    }
</style>
@endpush
