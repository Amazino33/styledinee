@extends('layouts.app')

@section('title', 'Shop — Styledinee')
@section('meta_description', 'Shop premium fabrics, accessories and ready-made pieces from Styledinee, Uyo Nigeria.')

@section('content')

{{-- Page Header --}}
<section class="page-header">
    <span class="section__label">Curated Selection</span>
    <h1 class="section__title">The Shop</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto; font-size: 1rem;">
        Premium fabrics, accessories and ready-made pieces — handpicked to complement the Styledinee standard.
    </p>
</section>

{{-- Filter Bar --}}
<section class="filter-bar">
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;" id="filterBar">
        @foreach (['All', 'Fabric', 'Accessory', 'Ready-Made'] as $cat)
        <button
            class="filter-btn {{ $loop->first ? 'active' : '' }}"
            data-cat="{{ strtolower(str_replace('-', '_', $cat)) === 'all' ? 'all' : strtolower(str_replace('-', '_', $cat)) }}"
        >{{ $cat }}</button>
        @endforeach
    </div>
</section>

{{-- Products Grid --}}
<section class="section section--off">
    @if ($products->isEmpty())
    <div style="text-align: center; padding: 4rem 0; color: var(--text-muted);">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--black);">No products available yet.</p>
        <p style="font-size: 0.9rem; margin-top: 0.5rem;">Check back soon — new arrivals are on the way.</p>
    </div>
    @else
    <div class="grid-4" id="productsGrid">
        @foreach ($products as $product)
        <div class="card product-card" data-cat="{{ $product->category }}" style="overflow: hidden;">
            <div style="
                aspect-ratio: 3/4;
                background: var(--gray-100);
                overflow: hidden;
                position: relative;
            ">
                @if ($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                    style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:rgba(201,168,76,0.2); font-size:3rem;">✦</div>
                @endif
                <div style="
                    position: absolute; top: 0.75rem; left: 0.75rem;
                    background: var(--gold); color: var(--white);
                    padding: 0.25rem 0.6rem; border-radius: 100px;
                    font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;
                ">{{ ucfirst(str_replace('_', ' ', $product->category ?? '')) }}</div>
            </div>
            <div style="padding: 1.25rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: var(--black);">{{ $product->name }}</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.6;">
                    {{ Str::limit($product->description, 80) }}
                </p>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--gold);">
                        ₦{{ number_format($product->price, 0) }}
                    </span>
                    <a href="{{ url('/contact') }}?product={{ $product->name }}" class="btn btn--outline" style="padding: 0.4rem 1rem; font-size: 0.72rem;">Enquire</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- Custom Order Banner --}}
<section style="
    background: var(--white);
    border-top: 1px solid var(--border);
    padding: 4rem 5vw;
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    gap: 2rem;
">
    <div>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--black);">Don't see what you're looking for?</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">We source premium fabrics on request. Tell us what you need.</p>
    </div>
    <a href="{{ url('/contact') }}" class="btn btn--gold">Make a Special Request</a>
</section>

@endsection

@push('styles')
<style>
    .product-card:hover img { transform: scale(1.04); }
    @media (max-width: 768px) {
        section[style*="grid-template-columns: 1fr auto"] {
            grid-template-columns: 1fr !important;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const btns = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.product-card');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cat = btn.dataset.cat;
            cards.forEach(card => {
                card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
            });
        });
    });
</script>
@endpush
