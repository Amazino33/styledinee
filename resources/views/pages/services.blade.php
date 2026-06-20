@extends('layouts.app')

@section('title', 'Services — Styledinee')
@section('meta_description', 'Bespoke tailoring, dry cleaning, alterations and pickup & delivery in Uyo, Nigeria. Premium quality, Naira-priced.')

@php
    $hasGallery = $galleryItems->count() > 0;
    $placeholders = [
        'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
        'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80',
        'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=800&q=80',
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80',
    ];
    $svcImgIndex = 0;
    $getSvcImage = function() use ($hasGallery, $galleryItems, $placeholders, &$svcImgIndex) {
        if ($hasGallery) {
            $item = $galleryItems[$svcImgIndex % $galleryItems->count()];
            $svcImgIndex++;
            return Storage::url($item->image);
        }
        return $placeholders[$svcImgIndex++ % count($placeholders)];
    };

    $slides = function($start = 0) use ($hasGallery, $galleryItems, $placeholders) {
        $urls = [];
        for ($s = 0; $s < 4; $s++) {
            if ($hasGallery) {
                $urls[] = Storage::url($galleryItems[($start + $s) % $galleryItems->count()]->image);
            } else {
                $urls[] = $placeholders[($start + $s) % count($placeholders)];
            }
        }
        return $urls;
    };
@endphp

@section('content')

{{-- Page Header --}}
<section class="page-header">
    <span class="section__label">What We Offer</span>
    <h1 class="section__title">Our Services</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: var(--text-muted); max-width: 520px; margin: 0 auto; font-size: 1rem;">
        From custom bespoke pieces to same-day dry cleaning — every service is delivered with the precision and care Styledinee is known for.
    </p>
</section>

{{-- Services Grid --}}
<section class="section section--off">
    @php
    $servicesList = [
        [
            'slug' => 'tailoring',
            'title' => 'Bespoke Tailoring',
            'price' => 'From ₦15,000',
            'desc' => 'We craft garments from scratch, tailored to your exact measurements and style vision. Whether it\'s a Nigerian traditional outfit, a power suit, or an evening dress — we bring it to life with premium fabrics and expert hands.',
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

    <div style="display: flex; flex-direction: column; gap: 3rem;">
        @foreach ($servicesList as $i => $svc)
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="svc-row {{ $i % 2 === 1 ? 'svc-row--reverse' : '' }}">
                <div style="padding: 2.5rem;">
                    <span class="section__label">{{ sprintf('%02d', $i + 1) }}</span>
                    <h2 style="font-size: clamp(1.6rem, 3vw, 2.2rem); margin-bottom: 0.5rem; color: var(--black);">{{ $svc['title'] }}</h2>
                    <div style="color: var(--gold); font-size: 0.95rem; margin-bottom: 1rem; font-weight: 600;">{{ $svc['price'] }}</div>
                    <div class="divider"></div>
                    <p style="color: var(--text-muted); line-height: 1.9; margin-bottom: 1.5rem;">{{ $svc['desc'] }}</p>
                    <ul style="list-style: none; margin-bottom: 2rem;">
                        @foreach ($svc['features'] as $f)
                        <li style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.6rem; font-size: 0.9rem; color: var(--text);">
                            <span style="color: var(--gold); font-size: 0.7rem;">✦</span> {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ url('/contact') }}" class="btn btn--outline">Book This Service</a>
                </div>
                @php $svcAnims = ['fade', 'zoom', 'slide', 'fade']; @endphp
                <div class="svc-img slideshow slideshow--{{ $svcAnims[$i % 4] }}">
                    @foreach ($slides($i * 4) as $url)
                    <div class="slideshow__slide" style="background-image: url('{{ $url }}');"></div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Pricing Note --}}
<section class="section section--white" style="text-align: center;">
    <span class="section__label">Pricing</span>
    <h2 class="section__title">Transparent Naira Pricing</h2>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: var(--text-muted); max-width: 560px; margin: 0 auto 2.5rem;">
        All prices are in Nigerian Naira (₦). Final pricing depends on fabric, complexity and urgency. Contact us for a precise quote.
    </p>
    <a href="{{ url('/contact') }}" class="btn btn--gold">Request a Quote</a>
</section>

@endsection

@push('styles')
<style>
    .card:hover { transform: none; }

    .svc-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: stretch;
    }

    .svc-row--reverse {
        direction: rtl;
    }

    .svc-row--reverse > * {
        direction: ltr;
    }

    .svc-img {
        background-size: cover;
        background-position: center;
        min-height: 320px;
    }

    @media (max-width: 768px) {
        .svc-row, .svc-row--reverse {
            grid-template-columns: 1fr;
            direction: ltr;
        }

        .svc-img {
            min-height: 250px;
            order: -1;
        }
    }
</style>
@endpush
