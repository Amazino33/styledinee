<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">My Active Assignments</x-slot>
        <x-slot name="description">Items currently assigned to you</x-slot>

        @if ($assignments->isEmpty())
            <div style="text-align:center;padding:2rem 0;color:var(--gray-400,#9ca3af);">
                <x-filament::icon icon="heroicon-o-check-circle" style="width:2.5rem;height:2.5rem;margin:0 auto .5rem;" />
                <p style="margin:0;">No active assignments — you're all caught up.</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                @foreach ($assignments as $assignment)
                    @php
                        $item  = $assignment->orderItem;
                        $order = $assignment->order;
                        $stage = $item?->item_stage ?? '—';
                        $stageLabel = \App\Models\OrderItem::PRODUCTION_STAGES[$stage] ?? ucfirst($stage);
                        $stageColors = [
                            'sewing'     => '#f59e0b',
                            'embroidery' => '#8b5cf6',
                            'printing'   => '#3b82f6',
                            'finishing'  => '#10b981',
                            'ready'      => '#22c55e',
                        ];
                        $stageColor = $stageColors[$stage] ?? '#6b7280';
                    @endphp
                    <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;background:var(--gray-50,#f9fafb);border-radius:.5rem;border:1px solid var(--gray-200,#e5e7eb);">
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $item?->description ?? '—' }}
                            </div>
                            <div style="font-size:.78rem;color:var(--gray-500,#6b7280);margin-top:.15rem;">
                                {{ $order?->reference ?? '—' }}
                                @if ($order?->customer)
                                    · {{ $order->customer->name }}
                                @endif
                            </div>
                        </div>
                        <span style="flex-shrink:0;font-size:.72rem;font-weight:600;padding:.2rem .6rem;border-radius:999px;background:{{ $stageColor }}22;color:{{ $stageColor }};border:1px solid {{ $stageColor }}44;">
                            {{ $stageLabel }}
                        </span>
                        <div style="flex-shrink:0;font-size:.72rem;color:var(--gray-400,#9ca3af);">
                            {{ $assignment->assigned_at?->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
