<div wire:poll.10s>
    <h1 class="page-title">My Orders</h1>
    <p class="page-subtitle">Track your orders and production status in real-time.</p>

    @if($orders->isEmpty())
    <div class="card" style="text-align:center;padding:3rem 0;color:var(--text-muted);font-size:.95rem;">
        You haven't placed any orders yet.
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:.75rem;">
        @foreach($orders as $order)
        <div class="card" style="padding:0;overflow:hidden;">

            {{-- Order header row --}}
            <div
                style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;cursor:pointer;gap:1rem;"
                wire:click="toggle({{ $order->id }})">
                <div style="display:flex;align-items:center;gap:1rem;flex:1;min-width:0;">
                    <span style="font-family:monospace;font-size:.85rem;color:var(--text-muted);white-space:nowrap;">
                        {{ $order->reference }}
                    </span>
                    <span class="badge badge--{{ $order->status }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:1.5rem;flex-shrink:0;">
                    <span style="font-weight:600;">₦{{ number_format($order->total_amount, 0) }}</span>
                    <span class="order-row__date" style="color:var(--text-muted);font-size:.82rem;white-space:nowrap;">
                        {{ $order->created_at->format('d M Y') }}
                    </span>
                    <svg style="width:1rem;height:1rem;color:var(--text-muted);transition:transform .2s;{{ $expandedId === $order->id ? 'transform:rotate(180deg)' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            {{-- Expanded detail --}}
            @if($expandedId === $order->id)
            <div style="border-top:1px solid var(--border);padding:1.25rem;">

                {{-- Items table --}}
                @if($order->items->isNotEmpty())
                <div style="margin-bottom:1.5rem;">
                    <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.6rem;">Order Items</div>
                    <div class="tbl-wrap"><table class="tbl">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th style="text-align:right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td style="text-align:right;">₦{{ number_format($item->price, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table></div>

                    {{-- Discount summary --}}
                    @if($order->coupon_discount > 0 || $order->referral_credit_used > 0)
                    <div style="margin-top:.75rem;padding:.75rem;background:var(--gray-100);border-radius:.5rem;font-size:.85rem;">
                        @if($order->coupon_discount > 0)
                        <div style="display:flex;justify-content:space-between;color:var(--text-muted);">
                            <span>Coupon discount</span>
                            <span style="color:#2d9f5e;">-₦{{ number_format($order->coupon_discount, 0) }}</span>
                        </div>
                        @endif
                        @if($order->referral_credit_used > 0)
                        <div style="display:flex;justify-content:space-between;color:var(--text-muted);margin-top:.25rem;">
                            <span>Wallet credit used</span>
                            <span style="color:#2d9f5e;">-₦{{ number_format($order->referral_credit_used, 0) }}</span>
                        </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;font-weight:600;margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--border);">
                            <span>Total paid</span>
                            <span>₦{{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Production timeline --}}
                @if($order->statusLogs->isNotEmpty())
                <div>
                    <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.75rem;">Production Timeline</div>
                    <div class="timeline">
                        @foreach($order->statusLogs as $log)
                        <div class="timeline__item">
                            <div class="timeline__dot"></div>
                            <div class="timeline__body">
                                <div class="timeline__label">{{ $log->label ?? ucfirst(str_replace('_', ' ', $log->status)) }}</div>
                                @if($log->note)
                                <div class="timeline__note">{{ $log->note }}</div>
                                @endif
                                <div class="timeline__time">{{ $log->created_at->format('d M Y, g:ia') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div style="font-size:.85rem;color:var(--text-muted);font-style:italic;">
                    No production updates yet.
                </div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
