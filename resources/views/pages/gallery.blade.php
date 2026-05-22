@extends('layouts.app')

@section('title', 'Gallery — Styledinee')
@section('meta_description', 'Browse the Styledinee gallery — bespoke tailoring, dry cleaning and alterations portfolio from Uyo, Nigeria.')

@section('content')

{{-- Page Header --}}
<section style="
    padding: 8rem 5vw 5rem;
    background: linear-gradient(180deg, var(--off-black) 0%, var(--black) 100%);
    border-bottom: 1px solid var(--border);
    text-align: center;
">
    <span class="section__label">Our Work</span>
    <h1 class="section__title" style="font-size: clamp(2.5rem, 5vw, 4rem);">The Gallery</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: rgba(250,250,248,0.55); max-width: 480px; margin: 0 auto; font-size: 1rem;">
        Every piece tells a story. Browse our portfolio of bespoke creations, bridal wear, corporate looks and more.
    </p>
</section>

{{-- Filter --}}
<section style="padding: 2rem 5vw; background: var(--off-black); border-bottom: 1px solid var(--border);">
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        @foreach (['All', 'Bespoke', 'Bridal', 'Corporate', 'Dry Cleaning', 'Alteration'] as $cat)
        <button
            class="gallery-filter {{ $loop->first ? 'gf-active' : '' }}"
            data-cat="{{ strtolower(str_replace(' ', '_', $cat)) === 'all' ? 'all' : strtolower(str_replace(' ', '_', $cat)) }}"
            style="
                padding: 0.5rem 1.25rem;
                font-family: 'Jost', sans-serif; font-size: 0.75rem; font-weight: 500;
                letter-spacing: 0.12em; text-transform: uppercase; cursor: pointer;
                border: 1px solid var(--border);
                background: {{ $loop->first ? 'var(--gold)' : 'transparent' }};
                color: {{ $loop->first ? 'var(--black)' : 'rgba(250,250,248,0.6)' }};
                transition: all 0.2s;
            "
        >{{ $cat }}</button>
        @endforeach
    </div>
</section>

{{-- Gallery Grid --}}
<section class="section section--dark">
    @if ($items->isEmpty())
    <div style="text-align: center; padding: 4rem 0; color: rgba(250,250,248,0.4);">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem;">Gallery coming soon.</p>
        <p style="font-size: 0.9rem; margin-top: 0.5rem;">We're loading up our portfolio — check back shortly.</p>
    </div>
    @else
    <div id="galleryGrid" style="
        columns: 3;
        column-gap: 1.5rem;
        @media(max-width:768px){ columns: 2; }
        @media(max-width:480px){ columns: 1; }
    ">
        @foreach ($items as $item)
        <div class="gallery-item" data-cat="{{ $item->category }}" style="
            break-inside: avoid;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: border-color 0.25s;
        " onclick="openLightbox('{{ Storage::url($item->image) }}', '{{ addslashes($item->title) }}')">
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                style="width: 100%; display: block; transition: transform 0.5s;">
            <div style="
                position: absolute; inset: 0;
                background: linear-gradient(0deg, rgba(13,13,13,0.85) 0%, transparent 50%);
                opacity: 0; transition: opacity 0.3s;
                display: flex; align-items: flex-end; padding: 1.25rem;
            " class="gallery-overlay">
                <div>
                    <div style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem;">{{ $item->title }}</div>
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
    @endif
</section>

{{-- Lightbox --}}
<div id="lightbox" style="
    display: none;
    position: fixed; inset: 0; z-index: 999;
    background: rgba(0,0,0,0.95);
    align-items: center; justify-content: center;
    padding: 2rem;
" onclick="closeLightbox()">
    <button onclick="closeLightbox()" style="
        position: absolute; top: 1.5rem; right: 1.5rem;
        background: none; border: 1px solid var(--border);
        color: var(--white); font-size: 1.2rem; width: 40px; height: 40px;
        cursor: pointer;
    ">&times;</button>
    <img id="lightboxImg" src="" alt="" style="max-height: 85vh; max-width: 90vw; object-fit: contain;">
    <div id="lightboxCaption" style="
        position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);
        font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: var(--white);
        text-align: center;
    "></div>
</div>

{{-- CTA --}}
<section style="
    background: var(--off-black); border-top: 1px solid var(--border);
    padding: 4rem 5vw; text-align: center;
">
    <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 0.75rem;">
        Want something like this made for you?
    </h2>
    <p style="color: rgba(250,250,248,0.5); margin-bottom: 2rem; font-size: 0.95rem;">
        Book a consultation and let our tailors bring your vision to life.
    </p>
    <a href="{{ url('/contact') }}" class="btn btn--gold">Start Your Order</a>
</section>

@endsection

@push('styles')
<style>
    .gallery-item:hover { border-color: var(--gold); }
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

    // Filter
    document.querySelectorAll('.gallery-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.gallery-filter').forEach(b => {
                b.style.background = 'transparent';
                b.style.color = 'rgba(250,250,248,0.6)';
            });
            btn.style.background = 'var(--gold)';
            btn.style.color = 'var(--black)';
            const cat = btn.dataset.cat;
            document.querySelectorAll('.gallery-item').forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.cat === cat) ? '' : 'none';
            });
        });
    });
</script>
@endpush
