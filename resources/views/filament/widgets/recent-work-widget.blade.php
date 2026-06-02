<x-filament-widgets::widget>
<x-filament::section>
<x-slot name="heading">Recent Work</x-slot>
<x-slot name="description">Your last completed assignments</x-slot>
<x-slot name="headerEnd">
    <div style="display:flex;gap:1rem;align-items:center;">
        <div style="text-align:right;">
            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400,#9ca3af);">This week</div>
            <div style="font-size:1.2rem;font-weight:700;color:#C9A84C;">{{ $totalThisWeek }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400,#9ca3af);">This month</div>
            <div style="font-size:1.2rem;font-weight:700;color:#C9A84C;">{{ $totalThisMonth }}</div>
        </div>
        <a href="{{ \App\Filament\Pages\StaffHistory::getUrl() }}"
           style="font-size:.75rem;font-weight:600;color:#C9A84C;text-decoration:underline;text-underline-offset:2px;white-space:nowrap;">
            View all →
        </a>
    </div>
</x-slot>

@php
    $stageColors = [
        'sewing'     => ['bg' => 'rgba(99,102,241,.12)',  'text' => '#6366f1', 'label' => 'Sewing'],
        'embroidery' => ['bg' => 'rgba(168,85,247,.12)',  'text' => '#a855f7', 'label' => 'Embroidery'],
        'printing'   => ['bg' => 'rgba(59,130,246,.12)',  'text' => '#3b82f6', 'label' => 'Printing'],
        'finishing'  => ['bg' => 'rgba(245,158,11,.12)',  'text' => '#d97706', 'label' => 'Finishing'],
        'washing'    => ['bg' => 'rgba(20,184,166,.12)',  'text' => '#14b8a6', 'label' => 'Washing'],
        'delivery'   => ['bg' => 'rgba(34,197,94,.12)',   'text' => '#16a34a', 'label' => 'Delivery'],
    ];
@endphp

@if ($recentWork->isEmpty())
    <div style="text-align:center;padding:2rem 0;color:var(--gray-400,#9ca3af);">
        <x-filament::icon icon="heroicon-o-clock" style="width:2.5rem;height:2.5rem;margin:0 auto .5rem;" />
        <p style="margin:0;">No completed work yet.</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:.5rem;">
        @foreach ($recentWork as $assignment)
            @php
                $dept  = $assignment->department ?? $assignment->orderItem?->item_stage ?? 'sewing';
                $color = $stageColors[$dept] ?? ['bg' => 'rgba(107,114,128,.1)', 'text' => '#6b7280', 'label' => ucfirst($dept)];
                $item  = $assignment->orderItem;
                $order = $assignment->order;
            @endphp
            <div style="display:flex;align-items:center;gap:.85rem;padding:.65rem .9rem;
                        background:var(--gray-50,#f9fafb);border-radius:8px;
                        border:1px solid var(--gray-200,#e5e7eb);">

                {{-- Stage pill --}}
                <span style="flex-shrink:0;font-size:.65rem;font-weight:700;text-transform:uppercase;
                             letter-spacing:.06em;padding:.2rem .55rem;border-radius:999px;
                             background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                    {{ $color['label'] }}
                </span>

                {{-- Item details --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                                color:var(--gray-900,#111827);">
                        {{ $item?->description ?? '—' }}
                    </div>
                    <div style="font-size:.75rem;color:var(--gray-500,#6b7280);margin-top:.1rem;">
                        {{ $order?->reference ?? '—' }}
                        @if ($order?->customer)
                            · <span style="font-weight:500;">{{ $order->customer->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Completed time --}}
                <div style="flex-shrink:0;font-size:.72rem;color:var(--gray-400,#9ca3af);white-space:nowrap;">
                    {{ $assignment->completed_at?->diffForHumans() ?? '—' }}
                </div>

                {{-- Done tick --}}
                <span style="flex-shrink:0;color:#16a34a;font-size:1rem;">✓</span>
            </div>
        @endforeach
    </div>
@endif
</x-filament::section>
</x-filament-widgets::widget>
