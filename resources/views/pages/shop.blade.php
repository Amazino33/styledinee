@extends('layouts.app')

@section('title', 'Shop — Styledinee')
@section('meta_description', 'Shop premium fabrics, accessories and ready-made pieces from Styledinee, Uyo Nigeria.')

@section('content')

{{-- Page Header --}}
<section style="
    padding: 8rem 5vw 5rem;
    background: linear-gradient(180deg, var(--off-black) 0%, var(--black) 100%);
    border-bottom: 1px solid var(--border);
    text-align: center;
">
    <span class="section__label">Curated Selection</span>
    <h1 class="section__title" style="font-size: clamp(2.5rem, 5vw, 4rem);">The Shop</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: rgba(250,250,248,0.55); max-width: 480px; margin: 0 auto; font-size: 1rem;">
        Premium fabrics, accessories and ready-made pieces — handpicked to complement the Styledinee standard.
    </p>
</section>

{{-- Filter Bar --}}
<section style="padding: 2rem 5vw; background: var(--off-black); border-bottom: 1px solid var(--border);">
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;" id="filterBar">
        @foreach (['All', 'Fabric', 'Accessory', 'Ready-Made'] as $cat)
        <button
            class="filter-btn {{ $loop->first ? 'active' : '' }}"
            data-cat="{{ strtolower(str_replace('-', '_', $cat)) === 'all' ? 'all' : strtolower(str_replace('-', '_', $cat)) }}"
            style="
                padding: 0.5rem 1.25rem;
                font-family: 'Jost', sans-serif;
                font-size: 0.75rem;
                font-weight: 500;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                cursor: pointer;
                border: 1px solid var(--border);
                background: {{ $loop->first ? 'var(--gold)' : 'transparent' }};
                color: {{ $loop->first ? 'var(--black)' : 'rgba(250,250,248,0.6)' }};
                transition: all 0.2s;
            "
        >{{ $cat }}</button>
        @endforeach
    </div>
</section>

{{-- Products Grid --}}
<section class="section section--dark">
    @if ($products->isEmpty())
    <div style="text-align: center; padding: 4rem 0; color: rgba(250,250,248,0.4);">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem;">No products available yet.</p>
        <p style="font-size: 0.9rem; margin-top: 0.5rem;">Check back soon — new arrivals are on the way.</p>
    </div>
    @else
    <div class="grid-4" id="productsGrid">
        @foreach ($products as $product)
        <div class="card product-card" data-cat="{{ $product->category }}" style="overflow: hidden;">
            <div style="
                aspect-ratio: 3/4;
                background: var(--off-black);
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
                    background: var(--gold); color: var(--black);
                    padding: 0.25rem 0.6rem;
                    font-size: 0.7rem; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase;
                ">{{ ucfirst(str_replace('_', ' ', $product->category ?? '')) }}</div>
            </div>
            <div style="padding: 1.25rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem;">{{ $product->name }}</h3>
                <p style="font-size: 0.85rem; color: rgba(250,250,248,0.5); margin-bottom: 1rem; line-height: 1.6;">
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
    background: var(--off-black);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 4rem 5vw;
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    gap: 2rem;
">
    <div>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 0.5rem;">Don't see what you're looking for?</h2>
        <p style="color: rgba(250,250,248,0.5); font-size: 0.95rem;">We source premium fabrics on request. Tell us what you need.</p>
    </div>
    <a href="{{ url('/contact') }}" class="btn btn--gold">Make a Special Request</a>
</section>

@endsection

@push('scripts')
<script>
    const btns = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.product-card');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => {
                b.style.background = 'transparent';
                b.style.color = 'rgba(250,250,248,0.6)';
                b.style.borderColor = 'rgba(201,168,76,0.25)';
            });
            btn.style.background = 'var(--gold)';
            btn.style.color = 'var(--black)';

            const cat = btn.dataset.cat;
            cards.forEach(card => {
                card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
            });
        });
    });
</script>
@endpush
