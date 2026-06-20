@extends('layouts.app')

@section('title', 'Application Submitted — Styledinee')

@section('content')

<section class="section section--off" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div class="card" style="padding: 3rem; max-width: 480px; width: 100%; text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--black);">Application Submitted!</h1>
        <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.7;">
            Thank you for applying to become a Styledinee affiliate. Our team will review your application and get back to you shortly.
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
