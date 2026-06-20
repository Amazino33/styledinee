@extends('layouts.app')

@section('title', 'Contact — Styledinee')
@section('meta_description', 'Get in touch with Styledinee in Uyo, Nigeria. Book a tailoring consultation, request a pickup, or send an enquiry.')

@php
    $hasGallery = $galleryItems->count() > 0;
    $contactPlaceholders = [
        'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
        'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80',
        'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=800&q=80',
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80',
    ];
    shuffle($contactPlaceholders);
    $contactSlides = [];
    for ($cs = 0; $cs < 4; $cs++) {
        $contactSlides[] = $hasGallery
            ? Storage::url($galleryItems[$cs % $galleryItems->count()]->image)
            : $contactPlaceholders[$cs];
    }
@endphp

@section('content')

{{-- Page Header --}}
<section class="page-header">
    <span class="section__label">Get In Touch</span>
    <h1 class="section__title">Contact Us</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto; font-size: 1rem;">
        Have a question, ready to book, or need a custom quote? Reach out — we respond within 24 hours.
    </p>
</section>

{{-- Contact Content --}}
<section class="section section--off">
    <div class="contact-grid">

        {{-- Info Column --}}
        <div>
            {{-- Fashion Image --}}
            <div class="slideshow slideshow--fade" style="
                width: 100%; aspect-ratio: 4/3;
                border-radius: var(--radius);
                margin-bottom: 2rem;
                border: 1px solid var(--border);
            ">
                @foreach ($contactSlides as $cUrl)
                <div class="slideshow__slide" style="background-image: url('{{ $cUrl }}'); border-radius: var(--radius);"></div>
                @endforeach
            </div>

            <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--black);">Let's Create Something Together</h2>
            <div class="divider"></div>
            <p style="color: var(--text-muted); margin-bottom: 2rem; line-height: 1.9; font-size: 0.95rem;">
                Whether you need a bespoke suit crafted from scratch, urgent dry cleaning, an alteration on a prized garment, or a reliable pickup and delivery service — we're here.
            </p>

            @foreach ([
                ['label' => 'Location',    'value' => 'Uyo, Akwa Ibom State, Nigeria',    'icon' => '⊙'],
                ['label' => 'Phone',       'value' => '+234 000 000 0000',                  'icon' => '☏'],
                ['label' => 'Email',       'value' => 'hello@styledinee.com',               'icon' => '✉'],
                ['label' => 'Hours',       'value' => 'Mon–Sat: 8am – 7pm',                 'icon' => '◷'],
            ] as $info)
            <div style="display: flex; gap: 1.25rem; margin-bottom: 1.5rem; align-items: flex-start;">
                <div style="
                    width: 40px; height: 40px; flex-shrink: 0;
                    border: 1.5px solid var(--border);
                    border-radius: 8px;
                    display: flex; align-items: center; justify-content: center;
                    color: var(--gold); font-size: 1rem;
                    background: var(--white);
                ">{{ $info['icon'] }}</div>
                <div>
                    <div style="font-size: 0.72rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--gold); font-weight: 600; margin-bottom: 0.2rem;">{{ $info['label'] }}</div>
                    <div style="font-size: 0.92rem; color: var(--text);">{{ $info['value'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Form Column --}}
        <div class="card" style="padding: 2rem; align-self: start;">
            @if (session('success'))
            <div style="
                background: rgba(201,168,76,0.1);
                border: 1px solid var(--gold);
                border-radius: 8px;
                padding: 1rem 1.25rem;
                margin-bottom: 1.5rem;
                font-size: 0.9rem;
                color: var(--gold);
            ">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ url('/contact') }}" method="POST">
                @csrf

                <div class="grid-2" style="margin-bottom: 0;">
                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" required
                            value="{{ old('name') }}"
                            placeholder="Emeka Okonkwo">
                        @error('name')<span style="color: #dc2626; font-size: 0.78rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required
                            value="{{ old('email') }}"
                            placeholder="you@example.com">
                        @error('email')<span style="color: #dc2626; font-size: 0.78rem;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 0;">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                            value="{{ old('phone') }}"
                            placeholder="+234 800 000 0000">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required
                            value="{{ old('subject', request('product') ? 'Product Enquiry: ' . request('product') : '') }}"
                            placeholder="Bespoke Suit Enquiry">
                        @error('subject')<span style="color: #dc2626; font-size: 0.78rem;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Your Message *</label>
                    <textarea id="message" name="message" required placeholder="Tell us about your needs — measurements, fabric preferences, timeline...">{{ old('message') }}</textarea>
                    @error('message')<span style="color: #dc2626; font-size: 0.78rem;">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn btn--gold" style="width: 100%; justify-content: center;">
                    Send Enquiry
                </button>
            </form>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .card:hover { transform: none; }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 4rem;
        align-items: start;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
