<x-filament-widgets::widget>
<x-filament::section>
<x-slot name="heading">Recent Work</x-slot>
<x-slot name="description">Your last completed {{ $isDriver ? 'deliveries' : 'assignments' }}</x-slot>
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
        @if(! $isDriver)
        <a href="{{ \App\Filament\Pages\StaffHistory::getUrl() }}"
           style="font-size:.75rem;font-weight:600;color:#C9A84C;text-decoration:underline;text-underline-offset:2px;white-space:nowrap;">
            View all →
        </a>
        @endif
    </div>
</x-slot>

@if ($recentWork->isEmpty())
    <div style="text-align:center;padding:2rem 0;color:var(--gray-400,#9ca3af);">
        <x-filament::icon icon="heroicon-o-clock" style="width:2.5rem;height:2.5rem;margin:0 auto .5rem;" />
        <p style="margin:0;">No completed work yet.</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:.5rem;">
        @foreach ($recentWork as $item)
            <div style="display:flex;align-items:center;gap:.85rem;padding:.65rem .9rem;
                        background:var(--gray-50,#f9fafb);border-radius:8px;
                        border:1px solid var(--gray-200,#e5e7eb);">

                {{-- Stage / type pill --}}
                @if($isDriver)
                    <span style="flex-shrink:0;font-size:.65rem;font-weight:700;text-transform:uppercase;
                                 letter-spacing:.06em;padding:.2rem .55rem;border-radius:999px;
                                 background:rgba(34,197,94,.12);color:#16a34a;">
                        🚚 Delivery
                    </span>
                @else
                    <span style="flex-shrink:0;font-size:.65rem;font-weight:700;text-transform:uppercase;
                                 letter-spacing:.06em;padding:.2rem .55rem;border-radius:999px;
                                 background:{{ $item->color }}22;color:{{ $item->color }};">
                        {{ $item->label }}
                    </span>
                @endif

                {{-- Details --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:.88rem;white-space:nowrap;overflow:hidden;
                                text-overflow:ellipsis;color:var(--gray-900,#111827);">
                        {{ $item->description }}
                    </div>
                    <div style="font-size:.75rem;color:var(--gray-500,#6b7280);margin-top:.1rem;">
                        {{ $item->sub }}
                    </div>
                </div>

                {{-- Time --}}
                <div style="flex-shrink:0;font-size:.72rem;color:var(--gray-400,#9ca3af);white-space:nowrap;">
                    {{ $item->completed_at?->diffForHumans() ?? '—' }}
                </div>

                <span style="flex-shrink:0;color:#16a34a;">✓</span>
            </div>
        @endforeach
    </div>
@endif
</x-filament::section>
</x-filament-widgets::widget>
