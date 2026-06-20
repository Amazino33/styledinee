@extends('layouts.app')

@section('title', 'Gallery — Styledinee')
@section('meta_description', 'Browse the Styledinee gallery — bespoke tailoring, dry cleaning and alterations portfolio from Uyo, Nigeria.')

@php
    $galleryPlaceholders = [
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
    shuffle($galleryPlaceholders);
@endphp

@section('content')

{{-- Page Header --}}
<section class="page-header">
    <span class="section__label">Our Work</span>
    <h1 class="section__title">The Gallery</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto; font-size: 1rem;">
        Every piece tells a story. Browse our portfolio of bespoke creations, bridal wear, corporate looks and more.
    </p>
</section>

{{-- Filter --}}
@if ($items->isNotEmpty())
<section class="filter-bar">
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        @foreach (['All', 'Bespoke', 'Bridal', 'Corporate', 'Dry Cleaning', 'Alteration'] as $cat)
        <button
            class="filter-btn gallery-filter {{ $loop->first ? 'active' : '' }}"
            data-cat="{{ strtolower(str_replace(' ', '_', $cat)) === 'all' ? 'all' : strtolower(str_replace(' ', '_', $cat)) }}"
        >{{ $cat }}</button>
        @endforeach
    </div>
</section>
@endif

{{-- Gallery Grid --}}
<section class="section section--off">
    @if ($items->isNotEmpty())
    <div id="galleryGrid" style="columns: 3; column-gap: 1.5rem;">
        @foreach ($items as $item)
        <div class="gallery-item" data-cat="{{ $item->category }}" style="
            break-inside: avoid;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: border-color 0.25s, box-shadow 0.25s;
        " onclick="openLightbox('{{ Storage::url($item->image) }}', '{{ addslashes($item->title) }}')">
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                style="width: 100%; display: block; transition: transform 0.5s;">
            <div style="
                position: absolute; inset: 0;
                background: linear-gradient(0deg, rgba(0,0,0,0.7) 0%, transparent 50%);
                opacity: 0; transition: opacity 0.3s;
                display: flex; align-items: flex-end; padding: 1.25rem;
            " class="gallery-overlay">
                <div>
                    <div style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #fff;">{{ $item->title }}</div>
                    @if($item->category)
                    <div style="font-size: 0.72rem; color: var(--gold); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 0.25rem;">
                        {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Placeholder gallery --}}
    <div style="text-align: center; margin-bottom: 2rem;">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--black); margin-bottom: 0.5rem;">Gallery coming soon</p>
        <p style="font-size: 0.9rem; color: var(--text-muted);">We're loading up our portfolio — here's a preview of what's in store.</p>
    </div>
    <div id="galleryGrid" style="columns: 3; column-gap: 1.5rem;">
        @foreach ($galleryPlaceholders as $ph)
        <div class="gallery-item" style="
            break-inside: avoid;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            position: relative;
            transition: border-color 0.25s, box-shadow 0.25s;
        ">
            <img src="{{ $ph }}" alt="Fashion preview" loading="lazy"
                style="width: 100%; display: block; transition: transform 0.5s;">
            <div style="
                position: absolute; inset: 0;
                background: linear-gradient(0deg, rgba(0,0,0,0.6) 0%, transparent 50%);
                opacity: 0; transition: opacity 0.3s;
                display: flex; align-items: flex-end; padding: 1.25rem;
            " class="gallery-overlay">
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.05rem; color: #fff;">Coming Soon</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- Lightbox --}}
<div id="lightbox" style="
    display: none;
    position: fixed; inset: 0; z-index: 999;
    background: rgba(0,0,0,0.92);
    align-items: center; justify-content: center;
    padding: 2rem;
" onclick="closeLightbox()">
    <button onclick="closeLightbox()" style="
        position: absolute; top: 1.5rem; right: 1.5rem;
        background: none; border: 1px solid rgba(255,255,255,0.2);
        color: #fff; font-size: 1.2rem; width: 40px; height: 40px;
        border-radius: 8px; cursor: pointer;
    ">&times;</button>
    <img id="lightboxImg" src="" alt="" style="max-height: 85vh; max-width: 90vw; object-fit: contain; border-radius: var(--radius);">
    <div id="lightboxCaption" style="
        position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);
        font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #fff;
        text-align: center;
    "></div>
</div>

{{-- CTA --}}
<section style="
    background: var(--white); border-top: 1px solid var(--border);
    padding: 4rem 5vw; text-align: center;
">
    <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 0.75rem; color: var(--black);">
        Want something like this made for you?
    </h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem;">
        Book a consultation and let our tailors bring your vision to life.
    </p>
    <a href="{{ url('/contact') }}" class="btn btn--gold">Start Your Order</a>
</section>

@endsection

@push('styles')
<style>
    .gallery-item:hover { border-color: var(--gold); box-shadow: var(--shadow); }
    .gallery-item:hover img { transform: scale(1.04); }
    .gallery-item:hover .gallery-overlay { opacity: 1; }
    #lightbox.open { display: flex; }

    @media (max-width: 768px) {
        #galleryGrid { columns: 2 !important; }
    }
    @media (max-width: 480px) {
        #galleryGrid { columns: 1 !important; }
    }
</style>
@endpush

@push('scripts')
<script>
    function openLightbox(src, caption) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightboxCaption').textContent = caption;
        document.getElementById('lightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

    document.querySelectorAll('.gallery-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.gallery-filter').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.cat;
            document.querySelectorAll('.gallery-item').forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.cat === cat) ? '' : 'none';
            });
        });
    });
</script>
@endpush
