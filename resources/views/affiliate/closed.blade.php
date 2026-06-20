@extends('layouts.app')

@section('title', 'Affiliate Registration Closed — Styledinee')

@section('content')

<section class="section section--off" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div class="card" style="padding: 3rem; max-width: 480px; width: 100%; text-align: center;">
        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--black);">Registration Closed</h1>
        <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.7;">
            Affiliate registrations are not currently being accepted. Please check back later.
        </p>
        <a href="{{ url('/') }}" class="btn btn--outline" style="margin-top: 1.5rem;">← Back to website</a>
    </div>
</section>

@endsection

@push('styles')
<style>
    .card:hover { transform: none; }
</style>
@endpush
