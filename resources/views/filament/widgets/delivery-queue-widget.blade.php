<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">My Deliveries</x-slot>
        <x-slot name="description">Orders assigned to you for pickup or delivery</x-slot>

        @if ($orders->isEmpty())
            <div style="text-align:center;padding:2rem 0;color:var(--gray-400,#9ca3af);">
                <x-filament::icon icon="heroicon-o-truck" style="width:2.5rem;height:2.5rem;margin:0 auto .5rem;" />
                <p style="margin:0;">No deliveries assigned to you right now.</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'ready'       => '#22c55e',
                            'in_progress' => '#f59e0b',
                        ];
                        $statusColor = $statusColors[$order->status] ?? '#6b7280';
                        $statusLabel = ucfirst(str_replace('_', ' ', $order->status));
                    @endphp
                    <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;background:var(--gray-50,#f9fafb);border-radius:.5rem;border:1px solid var(--gray-200,#e5e7eb);">
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:.9rem;">
                                {{ $order->reference }}
                            </div>
                            <div style="font-size:.78rem;color:var(--gray-500,#6b7280);margin-top:.15rem;">
                                {{ $order->customer?->name ?? '—' }}
                                @if ($order->customer?->phone)
                                    · {{ $order->customer->phone }}
                                @endif
                            </div>
                            @if ($order->delivery_notes)
                                <div style="font-size:.75rem;color:var(--gray-400,#9ca3af);margin-top:.15rem;">
                                    {{ $order->delivery_notes }}
                                </div>
                            @endif
                        </div>
                        <span style="flex-shrink:0;font-size:.72rem;font-weight:600;padding:.2rem .6rem;border-radius:999px;background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}44;">
                            {{ $statusLabel }}
                        </span>
                        <div style="flex-shrink:0;font-size:.72rem;color:var(--gray-400,#9ca3af);">
                            {{ $order->updated_at?->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
