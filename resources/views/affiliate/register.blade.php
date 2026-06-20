@extends('layouts.app')

@section('title', 'Become an Affiliate — Styledinee')

@section('content')

<section class="page-header">
    <span class="section__label">Partner With Us</span>
    <h1 class="section__title">Become an Affiliate</h1>
    <div class="divider" style="margin: 1.5rem auto;"></div>
    <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto; font-size: 1rem;">
        Earn a commission on every purchase made by the customers you refer to us.
    </p>
</section>

<section class="section section--off">
    <div style="max-width: 640px; margin: 0 auto;">
        <div class="card" style="padding: 2.5rem;">
            <form method="POST" action="{{ route('affiliate.register.submit') }}">
                @csrf

                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--gold); margin-bottom: 1rem;">Personal Information</div>

                <div class="grid-2" style="margin-bottom: 0;">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="username">Username / Referral Code *</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            required pattern="[a-zA-Z0-9_\-]+" title="Letters, numbers, underscores, hyphens only">
                        <p style="font-size: .75rem; color: var(--text-muted); margin-top: .35rem;">This becomes your unique referral code. Cannot be changed later.</p>
                        @error('username') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 0;">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 08012345678">
                        @error('phone') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="divider"></div>
                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--gold); margin-bottom: 1rem;">Bank Details (for commission payouts)</div>

                <div class="grid-2" style="margin-bottom: 0;">
                    <div class="form-group">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                        @error('bank_name') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <input type="text" id="account_number" name="account_number" value="{{ old('account_number') }}" maxlength="10">
                        @error('account_number') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="account_name">Account Name</label>
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name') }}">
                    @error('account_name') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                </div>

                <div class="divider"></div>

                <div class="form-group">
                    <label for="notes">Why do you want to be an affiliate? (optional)</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Tell us about yourself…">{{ old('notes') }}</textarea>
                    @error('notes') <span style="font-size: .75rem; color: #dc2626;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn--gold" style="width: 100%; justify-content: center;">Submit Application</button>
                <p style="font-size: .78rem; color: var(--text-muted); text-align: center; margin-top: .75rem;">Your application will be reviewed by our team. You will be contacted once approved.</p>
            </form>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .card:hover { transform: none; }
</style>
@endpush
