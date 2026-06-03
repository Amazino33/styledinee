<x-filament-panels::page>
<style>
:root {
    --bg:      #ffffff; --bg2:    #f9fafb; --bg3:    #f3f4f6;
    --border:  #e5e7eb;
    --text:    #111827; --text2:  #374151; --text3:  #6b7280;
    --gold:    #C9A84C; --gold-h: #b8943d;
    --green:   #059669; --red: #dc2626;
}
.dark {
    --bg:      #1f2937; --bg2:    #111827; --bg3:    #1a2535;
    --border:  #374151;
    --text:    #f9fafb; --text2:  #e5e7eb; --text3:  #d1d5db;
    --gold:    #C9A84C; --gold-h: #b8943d;
    --green:   #34d399; --red: #f87171;
}
.card {
    background: var(--bg); border: 1px solid var(--border);
    border-radius: 12px; padding: 24px; margin-bottom: 20px;
}
.card-title {
    font-size: 15px; font-weight: 600; color: var(--text);
    margin-bottom: 4px;
}
.card-desc {
    font-size: 13px; color: var(--text3); margin-bottom: 20px;
}
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
@media (max-width: 640px) { .field-row { grid-template-columns: 1fr; } }
.field-label {
    display: block; font-size: 13px; font-weight: 500;
    color: var(--text2); margin-bottom: 6px;
}
.field-input {
    width: 100%; padding: 8px 12px; border: 1px solid var(--border);
    border-radius: 8px; background: var(--bg2); color: var(--text);
    font-size: 14px; outline: none; box-sizing: border-box;
    transition: border-color .15s;
}
.field-input:focus { border-color: var(--gold); }
.toggle-row {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}
.toggle-label { font-size: 14px; font-weight: 500; color: var(--text2); }
.toggle-desc  { font-size: 12px; color: var(--text3); }
.toggle-switch {
    position: relative; width: 44px; height: 24px; flex-shrink: 0; cursor: pointer;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-track {
    position: absolute; inset: 0; background: #d1d5db;
    border-radius: 24px; transition: background .2s;
}
.toggle-switch input:checked ~ .toggle-track { background: var(--gold); }
.toggle-thumb {
    position: absolute; top: 3px; left: 3px; width: 18px; height: 18px;
    background: #fff; border-radius: 50%; transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform: translateX(20px); }
.btn {
    padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; transition: background .15s;
}
.btn-primary { background: var(--gold); color: #fff; }
.btn-primary:hover { background: var(--gold-h); }
.btn-secondary {
    background: var(--bg2); color: var(--text2);
    border: 1px solid var(--border);
}
.btn-secondary:hover { background: var(--bg3); }
.btn-row { display: flex; gap: 10px; justify-content: flex-end; }
.divider { height: 1px; background: var(--border); margin: 24px 0; }
.status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 100px; font-size: 12px; font-weight: 600;
}
.status-on  { background: #dcfce7; color: #166534; }
.status-off { background: #f3f4f6; color: #6b7280; }
.dark .status-on  { background: #14532d; color: #86efac; }
.dark .status-off { background: #374151; color: #9ca3af; }
</style>

<div>
    {{-- ── Credentials ── --}}
    <div class="card">
        <div class="card-title">WAWP Credentials</div>
        <div class="card-desc">
            Your WAWP instance ID and access token from
            <a href="https://app.wawp.net" target="_blank" style="color:var(--gold)">app.wawp.net</a>.
            These are stored in the database and never committed to code.
        </div>

        <div class="field-row">
            <div>
                <label class="field-label">Instance ID</label>
                <input type="text" wire:model="instance_id" class="field-input"
                    placeholder="e.g. 12345" autocomplete="off">
                @error('instance_id') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Access Token</label>
                <input type="password" wire:model="access_token" class="field-input"
                    placeholder="Paste your access token" autocomplete="new-password">
                @error('access_token') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Enable toggle --}}
        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="enabled">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div>
                <div class="toggle-label">Enable WhatsApp Notifications</div>
                <div class="toggle-desc">When off, messages are logged locally instead of being sent.</div>
            </div>
            <div style="margin-left:auto">
                <span class="status-badge {{ $enabled ? 'status-on' : 'status-off' }}">
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                    {{ $enabled ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        </div>

        <div class="btn-row">
            <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </div>

    {{-- ── SMS Fallback ── --}}
    <div class="card">
        <div class="card-title">SMS Fallback</div>
        <div class="card-desc">
            When WhatsApp delivery fails, messages are retried via SMS.
            Currently supports Termii and BulkSMS Nigeria.
        </div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="sms_enabled">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div>
                <div class="toggle-label">Enable SMS Fallback</div>
                <div class="toggle-desc">Sends SMS automatically when WhatsApp cannot deliver.</div>
            </div>
            <div style="margin-left:auto">
                <span class="status-badge {{ $sms_enabled ? 'status-on' : 'status-off' }}">
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                    {{ $sms_enabled ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        </div>

        <div class="field-row" style="margin-top:16px;">
            <div>
                <label class="field-label">SMS Provider</label>
                <select wire:model="sms_provider" class="field-input" style="cursor:pointer;">
                    <option value="termii">Termii</option>
                    <option value="bulksms">BulkSMS Nigeria</option>
                    <option value="kudisms">KudiSMS</option>
                </select>
            </div>
            <div>
                <label class="field-label">Sender ID / Name</label>
                <input type="text" wire:model="sms_sender_id" class="field-input" placeholder="Styledinee" maxlength="11">
                @error('sms_sender_id') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="field-row">
            <div>
                <label class="field-label">API Key</label>
                <input type="password" wire:model="sms_api_key" class="field-input" placeholder="Paste API key" autocomplete="new-password">
                @error('sms_api_key') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">API Secret <span style="font-size:11px;color:var(--text3)">(BulkSMS only)</span></label>
                <input type="password" wire:model="sms_api_secret" class="field-input" placeholder="Only required for BulkSMS" autocomplete="new-password">
                @error('sms_api_secret') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="btn-row">
            <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </div>

    {{-- ── OTP Rate Limiting ── --}}
    <div class="card">
        <div class="card-title">OTP Rate Limiting</div>
        <div class="card-desc">
            Prevent spam by limiting how many login codes can be sent to the same number within a time window.
        </div>
        <div class="field-row">
            <div>
                <label class="field-label">Time Window (minutes)</label>
                <input type="number" min="1" max="60" wire:model="otp_window_minutes" class="field-input">
                @error('otp_window_minutes') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
                <p style="font-size:11px;color:var(--text3);margin-top:4px;">How long the window is tracked. Default: 10 min.</p>
            </div>
            <div>
                <label class="field-label">Max OTP Requests per Window</label>
                <input type="number" min="1" max="10" wire:model="otp_max_attempts" class="field-input">
                @error('otp_max_attempts') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
                <p style="font-size:11px;color:var(--text3);margin-top:4px;">Block requests beyond this count. Default: 3.</p>
            </div>
        </div>
        <div class="btn-row">
            <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </div>

    {{-- ── Test message ── --}}
    <div class="card">
        <div class="card-title">Send Test Message</div>
        <div class="card-desc">
            Verify your configuration by sending a test WhatsApp to any number.
            Include country code, e.g. <code style="background:var(--bg3);padding:1px 5px;border-radius:4px;font-size:12px">2348012345678</code>.
        </div>

        <div style="display:flex;gap:12px;align-items:flex-start">
            <div style="flex:1">
                <input type="text" wire:model="test_number" class="field-input"
                    placeholder="2348012345678" style="max-width:320px">
                @error('test_number') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-secondary" wire:click="sendTest" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="sendTest">Send Test</span>
                <span wire:loading wire:target="sendTest">Sending…</span>
            </button>
        </div>
    </div>

    {{-- ── Info panel ── --}}
    <div class="card" style="background:var(--bg2)">
        <div class="card-title" style="margin-bottom:12px">What gets sent via WhatsApp?</div>
        <ul style="margin:0;padding-left:20px;color:var(--text2);font-size:13px;line-height:1.8">
            <li>Order confirmation (customer)</li>
            <li>Production stage updates (customer)</li>
            <li>Order ready for pickup / delivery (customer)</li>
            <li>Delivery OTP (customer)</li>
            <li>Staff assignment notifications (staff member's personal number)</li>
            <li>Customer login OTP</li>
        </ul>
    </div>
</div>
</x-filament-panels::page>
