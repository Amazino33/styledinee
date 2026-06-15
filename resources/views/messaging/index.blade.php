@extends('layouts.messaging')
@section('content')
<div id="toast" style="display:none; position:fixed; bottom:2rem; right:2rem; background:#0D0D0D; color:#fff; padding:.75rem 1.5rem; border-radius:8px; font-size:.85rem; z-index:999;"></div>
<form id="broadcastForm" method="post" action="">
    @csrf
    <div x-data="{ audience: 'manual' }" class="card">
        <span class="page-title">Message Box</span>
        <div style="display:flex; gap:1.5rem; align-items:center; margin:1rem 0;">
            <input id="manual" type="radio" name="audience_type" x-model="audience" value="manual">
                <label for="manual">Manual</label>
            <input id="all_customers" type="radio" name="audience_type" x-model="audience" value="all_customers">
                <label for="all_customers">All Customers</label>
            <input id="new_customers" type="radio" name="audience_type" x-model="audience" value="new_customers">
                <label for="new_customers">New Customers</label>
        </div>

        <textarea name="recipients" class="field__input" x-show="audience === 'manual'" placeholder="08012345678, 08098765432, ... " style="margin-bottom:1rem;"></textarea>

        <textarea name="message" class="field__input" placeholder="Type your message here... " rows="5" style="margin-bottom:1rem;"></textarea>

        <input type="submit" value="Send Message" class="btn btn--gold">
    </div>
</form>

<div class="card" style="margin-top:1.5rem;">
    <div class="section-head"><h2>Recent Broadcasts</h2></div>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Sent By</th>
                    <th>Audience</th>
                    <th>Recipients</th>
                    <th>Sent</th>
                    <th>Failed</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->sender->name ?? '-' }}</td>
                    <td>{{ $log->audience_type }}</td>
                    <td>{{ $log->recipient_count }}</td>
                    <td>{{ $log->sent_count }}</td>
                    <td>{{ $log->failed_count }}</td>
                    <td>{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">No Broadcasts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const form = document.getElementById('broadcastForm');
const toast = document.getElementById('toast');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const data = new FormData(form);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route("messaging.send") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token },
        body: data
    })
    .then(response => response.json())
    .then(json => {
        if (json.error) {
            showToast(json.error, '#dc2626');
        } else {
            showToast(json.message, '#0D0D0D');
        }
    })
    .catch(() => showToast('Something went wrong. Please try again.', '#dc2626'));
});

function showToast(msg, bg) {
    toast.textContent = msg;
    toast.style.background = bg;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}
</script>
@endpush