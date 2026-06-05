<style>
@media(max-width:640px){
    .ot-grid { grid-template-columns: 1fr !important; }
}
</style>

<div wire:poll.30s>

    {{-- ── Back link ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <h1 class="page-title" style="margin-bottom:.1rem;">Order Tracking</h1>
            <p class="page-subtitle" style="margin:0;">Real-time updates on your order.</p>
        </div>
        <a href="{{ route('account.orders') }}" class="btn btn--outline btn--sm">← All Orders</a>
    </div>

    {{-- ── Header card ── --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="font-size:.7rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.35rem;">
                    Order Reference
                </div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:500;letter-spacing:.04em;color:var(--black);">
                    {{ $order->reference }}
                </div>
                <div style="margin-top:.5rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                    <span class="badge badge--{{ $order->status }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                    @if($order->payment_status === 'paid')
                        <span class="badge badge--delivered">Paid</span>
                    @elseif($order->payment_status === 'partial')
                        <span class="badge badge--in_progress">Part Paid</span>
                    @else
                        <span class="badge badge--cancelled">Unpaid</span>
                    @endif
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ $order->created_at->format('d M Y') }}</span>
                </div>
            </div>

            <div style="text-align:right;">
                <div style="font-size:.7rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.35rem;">
                    Total Amount
                </div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:500;color:var(--gold);">
                    ₦{{ number_format($order->total_amount, 0) }}
                </div>
                @if($order->estimated_completion_date)
                <div style="font-size:.78rem;color:var(--text-muted);margin-top:.3rem;">
                    Est. completion: {{ \Carbon\Carbon::parse($order->estimated_completion_date)->format('d M Y') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Production pipeline ── --}}
    @unless($order->status === 'cancelled')
    <div class="card" style="margin-bottom:1.25rem;">
        <div style="font-size:.7rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1.25rem;">
            Production Progress
        </div>

        @php
            $stageKeys   = array_column($stages, 'key');
            $currentIdx  = array_search($current, $stageKeys);
            $totalStages = count($stages);
        @endphp

        <div style="overflow-x:auto;padding-bottom:.5rem;">
            <div style="display:flex;align-items:flex-start;min-width:max-content;">
                @foreach($stages as $i => $stage)
                    @php
                        $isDone   = $currentIdx !== false && $i < $currentIdx;
                        $isActive = $current === $stage['key'];
                        $dotColor = $isDone || $isActive ? 'var(--gold)' : 'var(--border)';
                        $dotBg    = $isDone ? 'var(--gold)' : ($isActive ? 'var(--white)' : 'var(--border)');
                        $dotShadow = $isActive ? '0 0 0 3px rgba(201,168,76,.2)' : 'none';
                        $lineColor = $isDone ? 'var(--gold)' : 'var(--border)';
                    @endphp

                    <div class="tl-wrap">
                        <div class="tl-step">
                            <div class="tl-dot {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}"></div>
                            @if(! $loop->last)
                                <div class="tl-line {{ $isDone ? 'done' : '' }}" style="width:48px;"></div>
                            @endif
                        </div>
                        <div class="tl-label" style="color:{{ $isDone || $isActive ? 'var(--text)' : 'var(--text-muted)' }};font-weight:{{ $isActive ? '600' : '400' }};">
                            {{ $stage['label'] }}
                            @if($isActive)
                                <div style="font-size:.58rem;color:var(--gold);font-weight:700;margin-top:1px;">← Now</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endunless

    @if($order->status === 'cancelled')
    <div class="card" style="margin-bottom:1.25rem;border-color:#FECACA;background:#FEF2F2;">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <span style="font-size:1.4rem;">⚠️</span>
            <div>
                <div style="font-weight:600;color:#991B1B;">Order Cancelled</div>
                <div style="font-size:.85rem;color:#B91C1C;margin-top:.15rem;">
                    This order has been cancelled. Please contact us if you have questions.
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="ot-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

        {{-- ── Status timeline ── --}}
        <div class="card">
            <div class="section-head" style="margin-bottom:1rem;">
                <h2>Status Updates</h2>
            </div>

            @if($order->statusLogs->isEmpty())
            <div style="font-size:.85rem;color:var(--text-muted);font-style:italic;padding:.5rem 0;">
                No updates yet — check back soon.
            </div>
            @else
            <div class="timeline">
                @foreach($order->statusLogs as $log)
                <div class="timeline__item">
                    <div class="timeline__dot"></div>
                    <div class="timeline__body">
                        <div class="timeline__label">
                            {{ $log->client_message ? '' : ucfirst(str_replace('_', ' ', $log->status)) }}
                            @if($log->client_message)
                                {{ $log->client_message }}
                            @endif
                        </div>
                        @if($log->notes && ! $log->client_message)
                        <div class="timeline__note">{{ $log->notes }}</div>
                        @endif
                        <div class="timeline__time">{{ $log->created_at->format('d M Y, g:ia') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Order items + payment ── --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            <div class="card">
                <div class="section-head" style="margin-bottom:.75rem;">
                    <h2>Items</h2>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            @if(trim($item->description ?? ''))
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $item->description }}</div>
                                    @if($item->product)
                                    <div style="font-size:.78rem;color:var(--text-muted);">{{ $item->product->name }}</div>
                                    @endif
                                </td>
                                <td style="text-align:center;color:var(--text-muted);">{{ $item->quantity }}</td>
                                <td style="text-align:right;font-weight:600;">₦{{ number_format($item->subtotal, 0) }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Discounts & total --}}
                <div style="padding:.75rem .75rem 0;border-top:1px solid var(--border);margin-top:.25rem;">
                    @if($order->coupon_discount > 0)
                    <div style="display:flex;justify-content:space-between;font-size:.85rem;color:var(--text-muted);margin-bottom:.3rem;">
                        <span>Coupon discount</span>
                        <span style="color:#2d9f5e;">-₦{{ number_format($order->coupon_discount, 0) }}</span>
                    </div>
                    @endif
                    @if($order->referral_credit_used > 0)
                    <div style="display:flex;justify-content:space-between;font-size:.85rem;color:var(--text-muted);margin-bottom:.3rem;">
                        <span>Wallet credit</span>
                        <span style="color:#2d9f5e;">-₦{{ number_format($order->referral_credit_used, 0) }}</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;font-weight:600;font-size:.95rem;">
                        <span>Total</span>
                        <span>₦{{ number_format($order->total_amount, 0) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;color:var(--text-muted);margin-top:.25rem;">
                        <span>Amount paid</span>
                        <span style="color:{{ $order->payment_status === 'paid' ? '#2d9f5e' : 'inherit' }};">
                            ₦{{ number_format($order->amount_paid, 0) }}
                        </span>
                    </div>
                    @if($order->total_amount > $order->amount_paid)
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;font-weight:600;color:#B45309;margin-top:.2rem;">
                        <span>Balance due</span>
                        <span>₦{{ number_format($order->total_amount - $order->amount_paid, 0) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Delivery info if applicable --}}
            @if($order->delivery_type === 'delivery' && $order->customer_address)
            <div class="card">
                <div class="section-head" style="margin-bottom:.75rem;">
                    <h2>Delivery Details</h2>
                </div>
                <div style="font-size:.88rem;color:var(--text-muted);line-height:1.6;">
                    {{ $order->customer_address }}
                </div>
                @if($order->delivery_date)
                <div style="font-size:.82rem;color:var(--text-muted);margin-top:.5rem;">
                    Expected: <strong>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</strong>
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>

</div>
