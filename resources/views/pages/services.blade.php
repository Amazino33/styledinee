@extends('layouts.app')

@section('title', 'Services — Styledinee')
@section('meta_description', 'Bespoke tailoring, dry cleaning, alterations and pickup & delivery in Uyo, Nigeria. Premium quality, Naira-priced.')

@section('content')

{{-- Page Header --}}
<section style="
    padding: 8rem 5vw 5rem;
    background: linear-gradient(180deg, var(--off-black) 0%, var(--black) 100%);
    border-bottom: 1px solid var(--border);
    text-align: center;
">
    <span class="section__label">What We Offer</span>
    <h1 class="section__title" style="font-size: clamp(2.5rem, 5vw, 4rem);">Our Services</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: rgba(250,250,248,0.55); max-width: 520px; margin: 0 auto; font-size: 1rem;">
        From custom bespoke pieces to same-day dry cleaning — every service is delivered with the precision and care Styledinee is known for.
    </p>
</section>

{{-- Services Grid --}}
<section class="section section--dark">
    @php
    $servicesList = [
        [
            'slug' => 'tailoring',
            'title' => 'Bespoke Tailoring',
            'price' => 'From ₦15,000',
            'desc' => 'We craft garments from scratch, tailored to your exact measurements and style vision. Whether it's a Nigerian traditional outfit, a power suit, or an evening dress — we bring it to life with premium fabrics and expert hands.',
            'features' => ['Personal measurements & style consultation', 'Choice of premium fabrics', 'Multiple fitting sessions', 'Agbada, Kaftan, Suits, Gowns & more'],
        ],
        [
            'slug' => 'dry_cleaning',
            'title' => 'Dry Cleaning',
            'price' => 'From ₦2,500',
            'desc' => 'Professional dry cleaning for your finest garments — suits, gowns, traditional wear, and delicate fabrics handled with specialist care and returned spotless.',
            'features' => ['Same-day & express cleaning available', 'Delicate fabric specialists', 'Stain removal treatment', 'Pressed & packaged for delivery'],
        ],
        [
            'slug' => 'alteration',
            'title' => 'Alterations',
            'price' => 'From ₦2,000',
            'desc' => 'A great outfit is one that fits you perfectly. Our tailors handle all types of alterations — hemming, taking in or letting out, resizing, and more — restoring the perfect fit to any garment.',
            'features' => ['Quick turnaround time', 'All garment types accepted', 'Invisible repair & resizing', 'Zip, button & lining repairs'],
        ],
        [
            'slug' => 'delivery',
            'title' => 'Pickup & Delivery',
            'price' => 'From ₦1,500',
            'desc' => 'We come to you. Book a pickup and our rider will collect your garments, take them to the studio, and return them to your door — cleaned, tailored, or altered as requested.',
            'features' => ['Coverage across Uyo & Akwa Ibom', 'Nationwide delivery available', 'Real-time order tracking', 'Safe & insured handling'],
        ],
    ];
    @endphp

    <div style="display: flex; flex-direction: column; gap: 5rem;">
        @foreach ($servicesList as $i => $svc)
        <div style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            {{ $i % 2 === 1 ? 'direction: rtl;' : '' }}
        ">
            <div style="{{ $i % 2 === 1 ? 'direction: ltr;' : '' }}">
                <span class="section__label">{{ sprintf('%02d', $i + 1) }}</span>
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.5rem); margin-bottom: 0.5rem;">{{ $svc['title'] }}</h2>
                <div style="color: var(--gold); font-size: 0.95rem; margin-bottom: 1rem; font-weight: 500;">{{ $svc['price'] }}</div>
                <div class="divider"></div>
                <p style="color: rgba(250,250,248,0.6); line-height: 1.9; margin-bottom: 1.5rem;">{{ $svc['desc'] }}</p>
                <ul style="list-style: none; margin-bottom: 2rem;">
                    @foreach ($svc['features'] as $f)
                    <li style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.6rem; font-size: 0.9rem; color: rgba(250,250,248,0.75);">
                        <span style="color: var(--gold); font-size: 0.7rem;">✦</span> {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ url('/contact') }}" class="btn btn--outline">Book This Service</a>
            </div>
            <div style="{{ $i % 2 === 1 ? 'direction: ltr;' : '' }}">
                <div style="
                    width: 100%; aspect-ratio: 4/3;
                    background: var(--off-black);
                    border: 1px solid var(--border);
                    display: flex; align-items: center; justify-content: center;
                    font-family: 'Cormorant Garamond', serif;
                    font-size: 5rem; color: rgba(201,168,76,0.15);
                ">{{ $svc['slug'] === 'tailoring' ? '✦' : ($svc['slug'] === 'dry_cleaning' ? '◈' : ($svc['slug'] === 'alteration' ? '⌖' : '⟳')) }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Pricing Note --}}
<section class="section section--off" style="text-align: center;">
    <span class="section__label">Pricing</span>
    <h2 class="section__title">Transparent Naira Pricing</h2>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: rgba(250,250,248,0.55); max-width: 560px; margin: 0 auto 2.5rem;">
        All prices are in Nigerian Naira (₦). Final pricing depends on fabric, complexity and urgency. Contact us for a precise quote.
    </p>
    <a href="{{ url('/contact') }}" class="btn btn--gold">Request a Quote</a>
</section>

@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
            direction: ltr !important;
        }
    }
</style>
@endpush
