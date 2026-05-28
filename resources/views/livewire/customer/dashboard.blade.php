<div>
    <h1 class="page-title">Welcome back, {{ $customer->name }}</h1>
    <p class="page-subtitle">Here's an overview of your account.</p>

    {{-- Stats ──────────────────────────────────────────── --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-card__label">Active Orders</div>
            <div class="stat-card__value">{{ $activeOrders }}</div>
            <div class="stat-card__sub">{{ $totalOrders }} total</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Wallet Balance</div>
            <div class="stat-card__value gold">₦{{ number_format($walletBalance, 0) }}</div>
            <div class="stat-card__sub">Referral credits</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Referral Code</div>
            <div class="stat-card__value" style="font-size:1.4rem;letter-spacing:.04em;">
                {{ $customer->username ?? '—' }}
            </div>
            <div class="stat-card__sub">Share to earn ₦ rewards</div>
        </div>
    </div>

    {{-- Referral share box ───────────────────────────── --}}
    @if($customer->username)
    <div class="card" style="margin-bottom:1rem;">
        <div class="section-head">
            <h2>Your Referral Code</h2>
        </div>
        <div class="referral-box">
            <div>
                <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.25rem;">Share this code</div>
                <div class="referral-code">{{ $customer->username }}</div>
                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;">
                    Link: <span style="font-family:monospace;">{{ url('/account/login?ref=' . $customer->username) }}</span>
                </div>
            </div>
            <button class="copy-btn" onclick="copyReferral(this, '{{ url('/account/login?ref=' . $customer->username) }}')">Copy Link</button>
        </div>
    </div>
    @endif

    {{-- Recent Orders ────────────────────────────────── --}}
    <div class="card">
        <div class="section-head">
            <h2>Recent Orders</h2>
            <a href="{{ route('account.orders') }}" class="btn btn--outline btn--sm">View All</a>
        </div>

        @if($recentOrders->isEmpty())
        <div style="text-align:center;padding:2rem 0;color:var(--text-muted);font-size:.9rem;">
            You haven't placed any orders yet.
        </div>
        @else
        <div class="tbl-wrap"><table class="tbl">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr>
                    <td style="font-family:monospace;font-size:.85rem;">{{ $order->reference }}</td>
                    <td><span class="badge badge--{{ $order->status }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span></td>
                    <td>{{ $order->items->count() }}</td>
                    <td>₦{{ number_format($order->total_amount, 0) }}</td>
                    <td style="color:var(--text-muted);font-size:.85rem;">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyReferral(btn, url) {
    const done = () => {
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy Link', 2000);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(done).catch(() => _copyFallback(url, done));
    } else {
        _copyFallback(url, done);
    }
}
function _copyFallback(text, cb) {
    const el = document.createElement('textarea');
    el.value = text;
    el.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
    document.body.appendChild(el);
    el.focus(); el.select();
    try { document.execCommand('copy'); cb(); } catch(e) {}
    document.body.removeChild(el);
}
</script>
@endpush
