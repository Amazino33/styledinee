@extends('layouts.app')

@section('title', 'Styledinee — Premium Bespoke Tailoring')

@section('content')

{{-- Hero --}}
<section style="
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: var(--black);
    overflow: hidden;
">
    <div style="
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(201,168,76,0.06) 0%, transparent 60%);
        pointer-events: none;
    "></div>
    <div style="
        position: absolute;
        right: 0; top: 0; bottom: 0;
        width: 48%;
        background: url('https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=1200&q=80') center/cover no-repeat;
        opacity: 0.35;
    "></div>

    <div style="position: relative; padding: 0 5vw; max-width: 680px;">
        <span class="section__label">Est. Uyo, Nigeria</span>
        <h1 style="font-size: clamp(3rem, 7vw, 5.5rem); font-weight: 300; line-height: 1.05; margin-bottom: 1.5rem;">
            Dressed<br>to Your<br><em style="font-style: italic; color: var(--gold);">Exact Standard</em>
        </h1>
        <p style="font-size: 1.05rem; color: rgba(250,250,248,0.65); max-width: 440px; margin-bottom: 2.5rem; line-height: 1.8;">
            Bespoke tailoring, expert dry cleaning, precise alterations, and door-to-door pickup &amp; delivery — all under one roof in Uyo.
        </p>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ url('/services') }}" class="btn btn--gold">Explore Services</a>
            <a href="{{ url('/contact') }}" class="btn btn--outline">Book a Consultation</a>
        </div>
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
            ['icon' => '✦', 'title' => 'Bespoke Tailoring', 'desc' => 'Custom-crafted garments tailored precisely to your measurements and style preferences.'],
            ['icon' => '◈', 'title' => 'Dry Cleaning',     'desc' => 'Professional dry cleaning for your finest fabrics, handled with the utmost care.'],
            ['icon' => '⌖', 'title' => 'Alterations',      'desc' => 'Expert alterations to ensure every piece fits you perfectly.'],
            ['icon' => '⟳', 'title' => 'Pickup & Delivery','desc' => 'Convenient door-to-door collection and delivery across Uyo and Nigeria.'],
        ];
        @endphp

        @foreach ($services as $s)
        <div class="card" style="padding: 2rem;">
            <div style="font-size: 1.8rem; color: var(--gold); margin-bottom: 1rem;">{{ $s['icon'] }}</div>
            <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem;">{{ $s['title'] }}</h3>
            <p style="font-size: 0.9rem; color: rgba(250,250,248,0.55); line-height: 1.8;">{{ $s['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- Why Styledinee --}}
<section class="section section--dark">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center;">
        <div>
            <span class="section__label">Why Choose Us</span>
            <h2 class="section__title">Precision Meets Elegance</h2>
            <div class="divider"></div>
            <p style="color: rgba(250,250,248,0.6); margin-bottom: 2rem; line-height: 1.9;">
                At Styledinee, every stitch tells a story. We combine traditional tailoring artistry with modern precision to deliver garments that speak to your identity.
            </p>
            @foreach (['Locally Made, Global Standard', 'Your Measurements, Our Craft', 'On-Time, Every Time', 'Premium Fabrics Only'] as $point)
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; flex-shrink: 0;"></span>
                <span style="font-size: 0.95rem; color: rgba(250,250,248,0.75);">{{ $point }}</span>
            </div>
            @endforeach
            <a href="{{ url('/services') }}" class="btn btn--gold" style="margin-top: 1.5rem;">View All Services</a>
        </div>
        <div style="position: relative;">
            <div style="
                width: 100%; aspect-ratio: 4/5;
                background: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80') center/cover;
                filter: grayscale(20%);
            "></div>
            <div style="
                position: absolute; bottom: -1.5rem; right: -1.5rem;
                background: var(--gold);
                color: var(--black);
                padding: 1.5rem;
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
            <p style="font-size: 0.95rem; color: rgba(250,250,248,0.65); line-height: 1.9; margin-bottom: 1.5rem; font-style: italic;">
                "{{ $t['text'] }}"
            </p>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Cormorant Garamond', serif; font-weight: 600; color: var(--black);">
                    {{ substr($t['name'], 0, 1) }}
                </div>
                <div>
                    <div style="font-size: 0.9rem; font-weight: 500;">{{ $t['name'] }}</div>
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
    <h2 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3rem); color: var(--black); margin-bottom: 1rem; font-weight: 500;">
        Ready to Look Your Best?
    </h2>
    <p style="color: rgba(13,13,13,0.7); margin-bottom: 2rem; font-size: 1rem;">
        Book a consultation today and let us create something exceptional for you.
    </p>
    <a href="{{ url('/contact') }}" class="btn" style="background: var(--black); color: var(--white);">
        Book a Consultation
    </a>
</section>

@endsection
