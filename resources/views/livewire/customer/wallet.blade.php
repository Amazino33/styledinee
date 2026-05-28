<div>
    <h1 class="page-title">My Wallet</h1>
    <p class="page-subtitle">Your referral credits and transaction history.</p>

    {{-- Balance card --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.4rem;">Available Balance</div>
                <div style="font-size:2.4rem;font-weight:700;font-family:'Cormorant Garamond',serif;color:var(--gold);">
                    ₦{{ number_format($balance, 0) }}
                </div>
                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;">Referral &amp; affiliate credits</div>
            </div>
            @if($customer->username)
            <div style="text-align:right;">
                <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.35rem;">Your Referral Code</div>
                <div class="referral-code" style="font-size:1.2rem;">{{ $customer->username }}</div>
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem;">Share to earn credits</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Transaction history --}}
    <div class="card">
        <div class="section-head">
            <h2>Transaction History</h2>
        </div>

        @if($transactions->isEmpty())
        <div style="text-align:center;padding:2rem 0;color:var(--text-muted);font-size:.9rem;">
            No transactions yet. Share your referral code to start earning!
        </div>
        @else
        <div class="tbl-wrap"><table class="tbl">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem;white-space:nowrap;">
                        {{ $tx->created_at->format('d M Y') }}
                    </td>
                    <td style="font-size:.88rem;">{{ $tx->description }}</td>
                    <td style="text-align:right;font-weight:600;white-space:nowrap;">
                        @if($tx->type === 'credit')
                        <span style="color:#2d9f5e;">+₦{{ number_format($tx->amount, 0) }}</span>
                        @else
                        <span style="color:#c0392b;">-₦{{ number_format($tx->amount, 0) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        @endif
    </div>
</div>
