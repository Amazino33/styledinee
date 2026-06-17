@extends('layouts.messaging')
@section('content')
<div id="toast" style="display:none; position:fixed; bottom:2rem; right:2rem; background:#0D0D0D; color:#fff; padding:.75rem 1.5rem; border-radius:8px; font-size:.85rem; z-index:999;"></div>

<form id="broadcastForm" method="post" action="">
    @csrf
    <input type="hidden" name="recipients" id="recipientsInput">

    <div class="card">
        <span class="page-title">Message Box</span>

        {{-- Audience dropdown --}}
        <div style="margin:1rem 0;">
            <label style="display:block; font-size:.85rem; margin-bottom:.4rem; color:var(--text-muted);">Audience</label>
            <select name="audience_type" id="audienceType" class="field__input" style="cursor:pointer;">
                <option value="manual">Manual — enter numbers below</option>
                <option value="all_customers">All Customers</option>
                <option value="new_customers">New Customers (last 30 days)</option>
            </select>
        </div>

        {{-- Add recipient search --}}
        <div style="margin-bottom:.75rem; position:relative;">
            <label style="display:block; font-size:.85rem; margin-bottom:.4rem; color:var(--text-muted);">Add Recipient</label>
            <input type="text" id="customerSearch" class="field__input" placeholder="Search customer by name or phone..." autocomplete="off">
            <div id="searchResults" style="display:none; position:absolute; z-index:100; background:#fff; border:1px solid #e5e7eb; border-radius:8px; width:100%; max-height:200px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,.1);"></div>
        </div>

        {{-- Chip container --}}
        <div style="margin-bottom:.75rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.4rem;">
                <label style="font-size:.85rem; color:var(--text-muted);">Recipients <span id="recipientCount" style="font-weight:600; color:var(--gold);">0</span></label>
                <button type="button" id="clearAll" onclick="clearRecipients()" style="font-size:.75rem; color:#dc2626; background:none; border:none; cursor:pointer; display:none;">Clear all</button>
            </div>
            <div id="chipContainer" style="display:flex; flex-wrap:wrap; gap:.4rem; min-height:42px; padding:.5rem; border:1px solid #e5e7eb; border-radius:8px; background:#f9fafb;"></div>
        </div>

        {{-- Filter/remove search --}}
        <div style="margin-bottom:1rem;">
            <input type="text" id="filterSearch" class="field__input" placeholder="Search within recipients to remove..." autocomplete="off">
        </div>

        {{-- Message --}}
        <textarea name="message" class="field__input" placeholder="Type your message here..." rows="5" style="margin-bottom:1rem;"></textarea>

        <input type="submit" value="Send Message" class="btn btn--gold">
    </div>
</form>

<div class="card" style="margin-top:1.5rem;">
    <div class="section-head"><h2>Recent Broadcasts</h2></div>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Sent By</th><th>Audience</th><th>Recipients</th><th>Sent</th><th>Failed</th><th>Date</th>
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
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">No broadcasts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
let recipients = [];

const chipContainer   = document.getElementById('chipContainer');
const recipientsInput = document.getElementById('recipientsInput');
const recipientCount  = document.getElementById('recipientCount');
const clearAllBtn     = document.getElementById('clearAll');
const toast           = document.getElementById('toast');

function addRecipient(phone, label) {
    if (recipients.includes(phone)) return;
    recipients.push(phone);
    renderChips();
    updateInput();
}

function removeRecipient(phone) {
    recipients = recipients.filter(r => r !== phone);
    renderChips();
    updateInput();
}

function clearRecipients() {
    recipients = [];
    renderChips();
    updateInput();
}

function updateInput() {
    recipientsInput.value = recipients.join(',');
    recipientCount.textContent = recipients.length;
    clearAllBtn.style.display = recipients.length > 0 ? 'inline' : 'none';
}

function renderChips(filter = '') {
    chipContainer.innerHTML = '';
    recipients.forEach(phone => {
        const match = filter === '' || phone.includes(filter);
        const chip = document.createElement('span');
        chip.style.cssText = `display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .6rem; border-radius:100px; font-size:.78rem; font-weight:500; background:${match ? '#fef3c7' : '#f3f4f6'}; color:${match ? '#92400e' : '#9ca3af'}; border:1px solid ${match ? '#fcd34d' : '#e5e7eb'};`;
        chip.innerHTML = `${phone} <button type="button" onclick="removeRecipient('${phone}')" style="background:none;border:none;cursor:pointer;font-size:.9rem;color:inherit;padding:0;line-height:1;">×</button>`;
        chipContainer.appendChild(chip);
    });
}

// Audience dropdown
document.getElementById('audienceType').addEventListener('change', function () {
    if (this.value === 'manual') {
        recipients = [];
        renderChips();
        updateInput();
        return;
    }
    fetch(`{{ route('messaging.audience-phones') }}?type=${this.value}`, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        recipients = data.phones;
        renderChips();
        updateInput();
    });
});

// Add customer search
let searchTimeout;
const searchResults = document.getElementById('searchResults');

document.getElementById('customerSearch').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 2) { searchResults.style.display = 'none'; return; }
    searchTimeout = setTimeout(() => {
        fetch(`{{ route('messaging.search-customers') }}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            searchResults.innerHTML = '';
            if (!data.customers.length) {
                searchResults.innerHTML = '<div style="padding:.6rem 1rem; font-size:.85rem; color:#9ca3af;">No customers found.</div>';
            } else {
                data.customers.forEach(c => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding:.6rem 1rem; cursor:pointer; font-size:.85rem; border-bottom:1px solid #f3f4f6;';
                    item.innerHTML = `<strong>${c.name}</strong> <span style="color:#9ca3af;">${c.phone}</span>`;
                    item.addEventListener('mouseenter', () => item.style.background = '#f9fafb');
                    item.addEventListener('mouseleave', () => item.style.background = '');
                    item.addEventListener('click', () => {
                        addRecipient(c.phone, c.name);
                        document.getElementById('customerSearch').value = '';
                        searchResults.style.display = 'none';
                    });
                    searchResults.appendChild(item);
                });
            }
            searchResults.style.display = 'block';
        });
    }, 300);
});

document.addEventListener('click', e => {
    if (!document.getElementById('customerSearch').contains(e.target)) {
        searchResults.style.display = 'none';
    }
});

// Filter/remove search
document.getElementById('filterSearch').addEventListener('input', function () {
    renderChips(this.value.trim());
});

// Form submit
document.getElementById('broadcastForm').addEventListener('submit', function (e) {
    e.preventDefault();
    if (recipients.length === 0) { showToast('No recipients selected.', '#dc2626'); return; }
    const data = new FormData(this);
    const token = document.querySelector('meta[name="csrf-token"]').content;
    fetch('{{ route("messaging.send") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token },
        body: data
    })
    .then(r => r.json())
    .then(json => {
        if (json.error) { showToast(json.error, '#dc2626'); }
        else { showToast(json.message, '#0D0D0D'); }
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
