<x-filament-widgets::widget>
<x-filament::section>
<x-slot name="heading">My Recent Activity</x-slot>
<x-slot name="description">Payments you have recorded today and this week</x-slot>
<x-slot name="headerEnd">
    <div style="display:flex;gap:1.25rem;align-items:center;">
        <div style="text-align:right;">
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400,#9ca3af);">Today's orders</div>
            <div style="font-size:1.2rem;font-weight:700;color:#C9A84C;">{{ $todayOrders }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400,#9ca3af);">Today's revenue</div>
            <div style="font-size:1.2rem;font-weight:700;color:#C9A84C;">₦{{ number_format($todayRevenue, 0) }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400,#9ca3af);">This week</div>
            <div style="font-size:1.2rem;font-weight:700;color:#C9A84C;">₦{{ number_format($weekRevenue, 0) }}</div>
        </div>
        <a href="{{ \App\Filament\Pages\CashierHistory::getUrl() }}"
           style="font-size:.75rem;font-weight:600;color:#C9A84C;text-decoration:underline;text-underline-offset:2px;white-space:nowrap;">
            View all →
        </a>
    </div>
</x-slot>

@php
    $methodColors = [
        'cash'       => ['bg' => 'rgba(34,197,94,.12)',   'text' => '#16a34a', 'label' => 'Cash'],
        'transfer'   => ['bg' => 'rgba(59,130,246,.12)',  'text' => '#2563eb', 'label' => 'Transfer'],
        'card'       => ['bg' => 'rgba(168,85,247,.12)',  'text' => '#9333ea', 'label' => 'Card'],
        'pos'        => ['bg' => 'rgba(245,158,11,.12)',  'text' => '#d97706', 'label' => 'POS'],
        'adjustment' => ['bg' => 'rgba(107,114,128,.12)', 'text' => '#6b7280', 'label' => 'Adjustment'],
    ];
@endphp

@if ($recentPayments->isEmpty())
    <div style="text-align:center;padding:2rem 0;color:var(--gray-400,#9ca3af);">
        <x-filament::icon icon="heroicon-o-banknotes" style="width:2.5rem;height:2.5rem;margin:0 auto .5rem;" />
        <p style="margin:0;">No payments recorded yet today.</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:.5rem;">
        @foreach ($recentPayments as $payment)
            @php
                $color  = $methodColors[$payment->method] ?? ['bg' => 'rgba(107,114,128,.12)', 'text' => '#6b7280', 'label' => ucfirst($payment->method)];
                $order  = $payment->order;
            @endphp
            <div style="display:flex;align-items:center;gap:.85rem;padding:.65rem .9rem;
                        background:var(--gray-50,#f9fafb);border-radius:8px;
                        border:1px solid var(--gray-200,#e5e7eb);">

                {{-- Method pill --}}
                <span style="flex-shrink:0;font-size:.65rem;font-weight:700;text-transform:uppercase;
                             letter-spacing:.06em;padding:.2rem .55rem;border-radius:999px;
                             background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                    {{ $color['label'] }}
                </span>

                {{-- Order details --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:.88rem;color:var(--gray-900,#111827);
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $order?->customer_name ?? '—' }}
                    </div>
                    <div style="font-size:.75rem;color:var(--gray-500,#6b7280);margin-top:.1rem;">
                        {{ $order?->reference ?? '—' }}
                        @if($order?->type)
                            · <span>{{ ucfirst(str_replace('_', ' ', $order->type)) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Amount --}}
                <div style="flex-shrink:0;font-weight:700;font-size:.92rem;color:var(--gray-900,#111827);">
                    ₦{{ number_format($payment->amount, 0) }}
                </div>

                {{-- Time --}}
                <div style="flex-shrink:0;font-size:.72rem;color:var(--gray-400,#9ca3af);white-space:nowrap;">
                    {{ $payment->created_at?->diffForHumans() }}
                </div>
            </div>
        @endforeach
    </div>
@endif
</x-filament::section>
</x-filament-widgets::widget>
