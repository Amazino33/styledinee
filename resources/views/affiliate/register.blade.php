<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become an Affiliate — Styledinee</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f9fafb; color: #111827; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 40px; max-width: 560px; width: 100%; }
        .logo { font-size: 22px; font-weight: 700; color: #C9A84C; margin-bottom: 8px; letter-spacing: -0.5px; }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .subtitle { font-size: 14px; color: #6b7280; margin-bottom: 32px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        input, textarea {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px;
            font-size: 14px; outline: none; transition: border-color .15s; background: #f9fafb;
        }
        input:focus, textarea:focus { border-color: #C9A84C; background: #fff; }
        .hint { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .error { font-size: 12px; color: #dc2626; margin-top: 4px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width: 500px) { .grid-2 { grid-template-columns: 1fr; } }
        .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #9ca3af; margin: 24px 0 16px; }
        .divider { height: 1px; background: #f3f4f6; margin: 20px 0; }
        .btn { display: block; width: 100%; padding: 12px; background: #C9A84C; color: #fff; font-weight: 700; font-size: 15px; border: none; border-radius: 10px; cursor: pointer; transition: background .15s; margin-top: 24px; }
        .btn:hover { background: #b8943d; }
        .terms { font-size: 12px; color: #9ca3af; text-align: center; margin-top: 12px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">Styledinee</div>
    <h1>Become an Affiliate</h1>
    <p class="subtitle">Earn a commission on every purchase made by the customers you refer to us.</p>

    <form method="POST" action="{{ route('affiliate.register.submit') }}">
        @csrf

        <div class="section-title">Personal Information</div>

        <div class="grid-2">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="username">Username / Referral Code *</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}"
                    required pattern="[a-zA-Z0-9_\-]+" title="Letters, numbers, underscores, hyphens only">
                <p class="hint">This becomes your unique referral code. Cannot be changed later.</p>
                @error('username') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 08012345678">
                @error('phone') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="divider"></div>
        <div class="section-title">Bank Details (for commission payouts)</div>

        <div class="grid-2">
            <div class="form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                @error('bank_name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="account_number">Account Number</label>
                <input type="text" id="account_number" name="account_number" value="{{ old('account_number') }}" maxlength="10">
                @error('account_number') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="account_name">Account Name</label>
            <input type="text" id="account_name" name="account_name" value="{{ old('account_name') }}">
            @error('account_name') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="divider"></div>

        <div class="form-group">
            <label for="notes">Why do you want to be an affiliate? (optional)</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Tell us about yourself…">{{ old('notes') }}</textarea>
            @error('notes') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn">Submit Application</button>
        <p class="terms">Your application will be reviewed by our team. You will be contacted once approved.</p>
    </form>
</div>
</body>
</html>
