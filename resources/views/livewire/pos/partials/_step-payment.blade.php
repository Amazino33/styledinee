{{-- ── PAYMENT STEP ─────────────────────────────────────────────────────────── --}}
{{-- All payment math is Alpine-only. $wire.completeOrder() finalises in Livewire. --}}

<div
    x-data="{
        payMode: 'cash',
        cashAmt: '',
        transferAmt: '',
        subtotal: {{ $this->subtotal() }},
        discount: {{ $this->discountAmount() }},

        get total()       { return Math.max(0, this.subtotal - this.discount); },
        get cashNum()     { return parseFloat(this.cashAmt)     || 0; },
        get transferNum() { return parseFloat(this.transferAmt) || 0; },
        get totalPaid() {
            if (this.payMode === 'cash')     return this.cashNum;
            if (this.payMode === 'transfer') return this.transferNum;
            return this.cashNum + this.transferNum;
        },
        get change()     { return Math.max(0, this.totalPaid - this.total); },
        get remaining()  { return this.payMode === 'split' ? Math.max(0, this.total - this.cashNum - this.transferNum) : 0; },
        get isValid() { return true; },
        get showBreakdown() { return this.payMode === 'split' && (this.cashNum > 0 || this.transferNum > 0); },

        switchMode(m) { this.payMode = m; this.cashAmt = ''; this.transferAmt = ''; },
        onCashInput() {
            if (this.payMode === 'split') {
                const rem = Math.max(0, this.total - this.cashNum);
                this.transferAmt = rem > 0 ? String(rem) : '';
            }
        },
        fmt(n) { return '₦' + Math.round(n).toLocaleString('en-NG'); },
        async complete() {
            if (!this.isValid) return;
            const cash     = this.payMode === 'cash'     ? this.cashNum     : (this.payMode === 'split' ? this.cashNum     : 0);
            const transfer = this.payMode === 'transfer' ? this.transferNum : (this.payMode === 'split' ? this.transferNum : 0);
            await $wire.completeOrder(this.payMode, cash, transfer);
        }
    }"
    style="display:flex;flex-direction:column;height:100%;"
>

    {{-- Header --}}
    <div class="psb-step-hdr">
        <button class="psb-back-btn" wire:click="goTo('summary')">←</button>
        <span class="psb-step-lbl">Payment</span>
        <span></span>
    </div>

    {{-- Body --}}
    <div class="psb-scroll" style="padding:18px 18px;display:flex;flex-direction:column;gap:18px;">

        {{-- Total due --}}
        <div>
            <div class="psb-amt-lbl">TOTAL DUE</div>
            <div class="psb-serif" style="font-size:32px;font-weight:500;color:#1a1a18;letter-spacing:-0.02em;line-height:1;"
                 x-text="fmt(total)"></div>
            @if($couponCode)
                <div style="font-size:11px;color:#9a9690;margin-top:3px;text-decoration:line-through;">
                    ₦{{ number_format($this->subtotal(),0) }}
                </div>
            @endif
        </div>

        {{-- Coupon --}}
        @if($couponCode)
            <div class="psb-info-card" style="background:#edf7f1;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;flex-direction:column;gap:1px;">
                    <span style="font-size:11px;font-weight:600;color:#2a7a4b;letter-spacing:0.04em;">{{ $couponCode }}</span>
                    <span style="font-size:10px;color:#2a7a4b;">−₦{{ number_format($this->discountAmount(),0) }} saved</span>
                </div>
                <button wire:click="removeCoupon"
                        style="border:none;background:none;cursor:pointer;font-size:14px;color:#2a7a4b;padding:0;line-height:1;">×</button>
            </div>
        @else
            <div>
                <div class="psb-amt-lbl" style="margin-bottom:6px;">COUPON CODE</div>
                <div class="psb-coupon-row">
                    <input type="text"
                           wire:model="couponInput"
                           placeholder="Enter code…"
                           class="psb-coupon-input {{ $couponError ? 'error' : '' }}"
                           wire:keydown.enter="applyCoupon" />
                    @if($couponError)
                        <span style="font-size:10px;color:#c0392b;flex-shrink:0;">{{ $couponError }}</span>
                    @else
                        <button wire:click="applyCoupon"
                                style="border:none;background:none;cursor:pointer;font-size:11px;color:#9a9690;padding:0;flex-shrink:0;">Apply</button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Outstanding balance warning --}}
        @if($this->customer && ($balance = $this->customer->creditBalance()) > 0)
            <div class="psb-info-card" style="background:#fdf0e8;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:11px;color:#7a3a18;">Outstanding balance</span>
                <span class="psb-serif" style="font-size:13px;font-weight:500;color:#7a3a18;">₦{{ number_format($balance,0) }}</span>
            </div>
        @endif

        {{-- Pay mode segment --}}
        <div class="psb-seg-bar">
            <button class="psb-seg-btn" :class="{ active: payMode === 'cash' }"     @click="switchMode('cash')">Cash</button>
            <button class="psb-seg-btn" :class="{ active: payMode === 'transfer' }" @click="switchMode('transfer')">Transfer</button>
            <button class="psb-seg-btn" :class="{ active: payMode === 'split' }"    @click="switchMode('split')">Split</button>
        </div>

        {{-- Cash input --}}
        <div x-show="payMode === 'cash' || payMode === 'split'" x-cloak>
            <div class="psb-amt-lbl" x-text="payMode === 'split' ? 'CASH' : 'CASH TENDERED'"></div>
            <div class="psb-amt-row">
                <span class="psb-amt-sym">₦</span>
                <input class="psb-amt-input" type="number" placeholder="0"
                       x-model="cashAmt" @input="onCashInput()" />
            </div>
        </div>

        {{-- Transfer input (transfer mode) --}}
        <div x-show="payMode === 'transfer'" x-cloak>
            <div class="psb-amt-lbl">TRANSFER AMOUNT</div>
            <div class="psb-amt-row">
                <span class="psb-amt-sym">₦</span>
                <input class="psb-amt-input" type="number" placeholder="0"
                       x-model="transferAmt" />
            </div>
        </div>

        {{-- Transfer input (split mode) --}}
        <div x-show="payMode === 'split'" x-cloak>
            <div class="psb-amt-lbl">TRANSFER</div>
            <div class="psb-amt-row">
                <span class="psb-amt-sym">₦</span>
                <input class="psb-amt-input" type="number" placeholder="0" x-model="transferAmt" />
            </div>
        </div>

        {{-- Split breakdown --}}
        <div x-show="showBreakdown" x-cloak class="psb-info-card"
             style="background:#f5f3ef;display:flex;flex-direction:column;gap:5px;">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#9a9690;">
                <span>Cash</span><span x-text="fmt(cashNum)"></span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#9a9690;">
                <span>Transfer</span><span x-text="fmt(transferNum)"></span>
            </div>
            <div style="border-top:0.5px solid #e0dbd3;padding-top:5px;display:flex;justify-content:space-between;font-size:12px;font-weight:500;color:#1a1a18;">
                <span>Total paid</span><span x-text="fmt(cashNum + transferNum)"></span>
            </div>
        </div>

        {{-- Change due --}}
        <div x-show="change > 0" x-cloak class="psb-info-card"
             style="background:#edf7f1;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:11px;color:#2a7a4b;">Change</span>
            <span class="psb-serif" style="font-size:15px;font-weight:500;color:#2a7a4b;" x-text="fmt(change)"></span>
        </div>

        {{-- Remaining (split underpaid) --}}
        <div x-show="payMode === 'split' && remaining > 0 && (cashNum > 0 || transferNum > 0)" x-cloak
             class="psb-info-card" style="background:#fff5f5;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:11px;color:#c0392b;">Remaining</span>
            <span class="psb-serif" style="font-size:15px;font-weight:500;color:#c0392b;" x-text="fmt(remaining)"></span>
        </div>

    </div>

    {{-- Footer --}}
    <div class="psb-foot">
        <button class="psb-gold-btn"
                :style="{ opacity: isValid ? 1 : 0.4, cursor: isValid ? 'pointer' : 'not-allowed' }"
                :disabled="!isValid"
                @click="complete()">
            Complete Order →
        </button>
    </div>

</div>
