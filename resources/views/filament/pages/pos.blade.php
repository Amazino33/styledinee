<x-filament-panels::page>
<style>
/* ── Variables ── */
:root {
    --bg:      #ffffff; --bg2:    #f9fafb; --bg3:    #f3f4f6;
    --border:  #e5e7eb; --border2:#d1d5db;
    --text:    #111827; --text2:  #374151; --text3:  #6b7280; --muted: #9ca3af;
    --gold:    #C9A84C; --gold-h: #b8943d; --gold-light: rgba(201,168,76,0.10);
    --radius:  10px;
}
.dark {
    --bg:      #1f2937; --bg2:    #111827; --bg3:    #1a2535;
    --border:  #374151; --border2:#4b5563;
    --text:    #f9fafb; --text2:  #e5e7eb; --text3:  #d1d5db; --muted: #6b7280;
}

/* ── Layout ── */
.pos-shell { display:flex; height:calc(100vh - 8rem); gap:0; overflow:hidden; }
.pos-left  { flex:1; min-width:0; overflow-y:auto; overflow-x:hidden; padding:1rem; border-right:1px solid var(--border); }
.pos-right { width:360px; flex-shrink:0; display:flex; flex-direction:column; overflow:hidden; min-width:0; }
@media(max-width:1023px) {
    .pos-shell { flex-direction:column; height:auto; overflow:visible; }
    .pos-left  { border-right:none; border-bottom:1px solid var(--border); }
    .pos-right { width:100%; }
}

/* ── Card ── */
.pcard { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); padding:1rem; }

/* ── Section label ── */
.plbl { display:block; font-size:.62rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--muted); margin-bottom:.6rem; }

/* ── Search bar ── */
.pos-search-wrap { position:relative; margin-bottom:1rem; }
.pos-search-wrap input {
    width:100%; padding:.55rem .75rem .55rem 2.25rem;
    border:1px solid var(--border2); border-radius:8px; background:var(--bg2);
    font-size:.875rem; outline:none; font-family:inherit; color:var(--text);
    transition:border-color .15s;
}
.pos-search-wrap input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.pos-search-icon { position:absolute; left:.65rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:1rem; }

/* ── Product Grid ── */
.prod-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:.6rem; }
.prod-card {
    border:1px solid var(--border); border-radius:8px; overflow:hidden;
    cursor:pointer; transition:all .15s; background:var(--bg);
    display:flex; flex-direction:column;
}
.prod-card:hover { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.12); transform:translateY(-1px); }

/* ── Product image stack ── */
.prod-img-wrap {
    position:relative; width:100%; aspect-ratio:1; overflow:hidden;
    background:var(--bg3); flex-shrink:0;
}
/* Placeholder always sits at the base */
.prod-img-ph {
    position:absolute; inset:0; display:flex; flex-direction:column;
    align-items:center; justify-content:center; gap:.2rem;
    font-size:1.7rem; color:var(--muted);
}
.prod-img-ph-label { font-size:.6rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.08em; color:var(--muted); }
/* Real image overlays the placeholder */
.prod-img {
    position:absolute; inset:0; width:100%; height:100%;
    object-fit:cover; object-position:center;
    transition:opacity .2s ease;
}
/* Shimmer sweeps across while loading */
@keyframes pos-shimmer { to { background-position: 200% center; } }
.prod-img-wrap.is-loading::after {
    content:''; position:absolute; inset:0; z-index:1; pointer-events:none;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.18) 50%, transparent 100%);
    background-size:200% 100%;
    animation: pos-shimmer 1.4s ease-in-out infinite;
}
.dark .prod-img-wrap.is-loading::after {
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.07) 50%, transparent 100%);
    background-size:200% 100%;
}
.prod-info { padding:.5rem; flex:1; }
.prod-name  { font-size:.75rem; font-weight:600; color:var(--text); line-height:1.3; margin-bottom:.2rem; }
.prod-price { font-size:.72rem; color:var(--text3); }
.prod-badge { display:inline-block; font-size:.55rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:.15rem .4rem; border-radius:4px; margin-bottom:.2rem; }
.prod-badge.production { background:#fef3c7; color:#92400e; }
.prod-badge.ready      { background:#d1fae5; color:#065f46; }
.dark .prod-badge.production { background:rgba(251,191,36,.1); color:#fbbf24; }
.dark .prod-badge.ready      { background:rgba(16,185,129,.1); color:#34d399; }
.no-results { text-align:center; color:var(--muted); font-size:.85rem; padding:2rem 0; }

/* ── Service Pills ── */
.svc-pills { display:flex; flex-wrap:wrap; gap:.4rem; }
.svc-pill {
    display:inline-flex; align-items:center; gap:.25rem;
    padding:.3rem .7rem; border-radius:999px; border:1px solid var(--border);
    background:transparent; font-size:.7rem; font-weight:500; color:var(--text2);
    cursor:pointer; transition:all .15s; font-family:inherit;
}
.svc-pill:hover { border-color:var(--gold); color:var(--gold); background:var(--gold-light); }
.svc-pill-price { color:var(--muted); font-size:.65rem; }

/* ── Order Type Tabs ── */
.otype-wrap { display:grid; grid-template-columns:repeat(4,1fr); gap:.4rem; }
@media(max-width:600px){ .otype-wrap{grid-template-columns:repeat(2,1fr);} }
.otype-btn {
    padding:.45rem .25rem; border:2px solid var(--border); border-radius:7px; background:transparent;
    font-size:.68rem; font-weight:600; color:var(--text3); cursor:pointer; transition:all .15s;
    font-family:inherit; display:flex; flex-direction:column; align-items:center; gap:.2rem; line-height:1.2;
}
.otype-btn:hover { border-color:var(--gold); color:var(--gold); }
.otype-btn.active { border-color:var(--gold); background:var(--gold-light); color:#92740a; }
.dark .otype-btn.active { color:var(--gold); }

/* ── Cart ── */
.cart-scroll { flex:1; overflow-y:auto; overflow-x:hidden; padding:.75rem .85rem 0; box-sizing:border-box; }
.cart-foot   { padding:.65rem .85rem .85rem; border-top:1px solid var(--border); flex-shrink:0; }

/* shared 2-row item layout */
.cart-item, .manual-row { display:flex; flex-direction:column; gap:.25rem; padding:.45rem 0; border-bottom:1px solid var(--bg3); }
.dark .cart-item, .dark .manual-row { border-color:var(--border); }
.item-top { display:flex; align-items:flex-start; gap:.35rem; }
.item-bot { display:flex; align-items:center; gap:.35rem; }
.item-name-col { flex:1; min-width:0; }
.ci-name { font-size:.82rem; font-weight:500; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ci-meta { font-size:.65rem; color:var(--muted); }
.ci-badge-prod { font-size:.58rem; font-weight:700; text-transform:uppercase; padding:.1rem .3rem; border-radius:3px; background:#fef3c7; color:#92400e; }
.dark .ci-badge-prod { background:rgba(251,191,36,.1); color:#fbbf24; }
.ci-input { width:48px; text-align:center; padding:.3rem .2rem; border:1px solid var(--border); border-radius:5px; background:var(--bg2); font-size:.8rem; font-family:inherit; color:var(--text); outline:none; flex-shrink:0; }
.ci-input:focus { border-color:var(--gold); }
.ci-price { font-size:.82rem; font-weight:600; color:var(--text); text-align:right; white-space:nowrap; flex-shrink:0; }
.ci-rm { background:none; border:none; cursor:pointer; color:var(--border2); padding:.2rem .3rem; border-radius:4px; font-size:.9rem; line-height:1; transition:color .15s; flex-shrink:0; }
.ci-rm:hover { color:#ef4444; }
.add-row-btn { display:inline-flex; align-items:center; gap:.25rem; font-size:.75rem; font-weight:500; color:var(--gold); background:none; border:none; cursor:pointer; padding:.35rem 0; font-family:inherit; transition:opacity .15s; margin-top:.25rem; }
.add-row-btn:hover { opacity:.7; }

/* ── Manual / shared inputs ── */
.mi { padding:.35rem .45rem; border:1px solid var(--border); border-radius:5px; background:var(--bg2); font-size:.8rem; font-family:inherit; color:var(--text); outline:none; min-width:0; box-sizing:border-box; }
.mi:focus { border-color:var(--gold); }
.mi-desc  { flex:1; min-width:0; }
.mi-qty   { width:48px; text-align:center; flex-shrink:0; }
.mi-price { width:80px; text-align:right; flex-shrink:0; }
.mi-sub { font-size:.78rem; font-weight:600; text-align:right; color:var(--text); padding-right:.25rem; }

/* ── Production path select ── */
.path-select {
    font-size:.62rem; font-weight:600; padding:.18rem .35rem;
    border:1px solid var(--border); border-radius:4px;
    background:var(--bg2); color:var(--text3); cursor:pointer;
    outline:none; font-family:inherit; margin-top:.2rem; max-width:100%;
    transition:border-color .15s;
}
.path-select:focus { border-color:var(--gold); }
.path-select.has-path { border-color:rgba(201,168,76,.5); color:var(--gold); }

/* ── Customer strip ── */
.cust-strip { display:flex; align-items:center; justify-content:space-between; background:var(--bg2); border:1px solid var(--border); border-radius:8px; padding:.55rem .75rem; margin-top:.85rem; margin-bottom:.65rem; cursor:pointer; transition:border-color .15s; }
.cust-strip:hover { border-color:var(--gold); }
.cust-strip-info { display:flex; align-items:center; gap:.5rem; min-width:0; overflow:hidden; }
.cust-strip-icon { font-size:1rem; flex-shrink:0; }
.cust-strip-name { font-size:.85rem; font-weight:700; color:var(--text); }
.cust-strip-phone { font-size:.78rem; color:var(--text3); }
.cust-strip-date  { font-size:.73rem; color:var(--gold); font-weight:600; }
.cust-strip-edit  { font-size:.68rem; font-weight:600; color:var(--muted); background:none; border:none; cursor:pointer; padding:.2rem .45rem; border-radius:4px; font-family:inherit; transition:color .15s; flex-shrink:0; white-space:nowrap; }
.cust-strip-edit:hover { color:var(--gold); }
.cust-add-btn { display:flex; align-items:center; justify-content:space-between; width:100%; padding:.55rem .75rem; background:var(--bg2); border:1.5px dashed var(--border2); border-radius:8px; margin-top:.85rem; margin-bottom:.65rem; cursor:pointer; font-family:inherit; transition:all .15s; font-size:.85rem; font-weight:600; color:var(--text3); box-sizing:border-box; }
.cust-add-btn:hover { border-color:var(--gold); color:var(--gold); background:var(--gold-light); }
.cust-add-hint { font-size:.65rem; font-weight:700; color:#ef4444; text-transform:uppercase; letter-spacing:.08em; }

/* ── Shared form field ── */
.pf { display:flex; flex-direction:column; gap:.2rem; }
.pf label { font-size:.65rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--text3); }
.pf input, .pf select { padding:.42rem .6rem; border:1px solid var(--border2); border-radius:6px; background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit; transition:border-color .15s; }
.pf input:focus, .pf select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.pf .ferr { font-size:.68rem; color:#ef4444; }

/* ── Notes input ── */
.notes-input { width:100%; padding:.42rem .6rem; border:1px solid var(--border2); border-radius:6px; background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit; transition:border-color .15s; }
.notes-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }

/* ── Step breadcrumb ── */
.step-breadcrumb { display:flex; align-items:center; gap:.5rem; margin-bottom:.85rem; padding:.4rem .5rem; background:rgba(201,168,76,.07); border-radius:6px; }
.step-bc-between  { display:flex; align-items:center; justify-content:space-between; margin-bottom:.85rem; padding:.4rem .5rem; background:rgba(201,168,76,.07); border-radius:6px; }
.step-bc-row      { display:flex; align-items:center; gap:.5rem; }
.step-bc-active   { font-size:.65rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; color:var(--gold); }
.step-bc-sep      { font-size:.65rem; color:var(--muted); }
.step-bc-inactive { font-size:.65rem; font-weight:500; color:var(--muted); }
.step-bc-back { font-size:.7rem; font-weight:600; color:var(--muted); background:none; border:none; cursor:pointer; font-family:inherit; padding:.15rem .35rem; border-radius:4px; transition:color .15s; letter-spacing:.03em; }
.step-bc-back:hover { color:var(--gold); }

/* ── Order Summary (payment step) ── */
.order-summary { background:var(--bg2); border:1px solid var(--border); border-radius:8px; padding:.6rem .75rem; margin-bottom:.85rem; }
.ord-sum-hdr   { display:flex; justify-content:space-between; align-items:center; margin-bottom:.35rem; }
.ord-sum-toggle { font-size:.7rem; font-weight:600; color:var(--muted); background:none; border:none; cursor:pointer; font-family:inherit; padding:.1rem .3rem; border-radius:4px; transition:color .15s; }
.ord-sum-toggle:hover { color:var(--gold); }
.ord-sum-item  { display:flex; justify-content:space-between; align-items:center; padding:.25rem 0; font-size:.82rem; border-bottom:1px solid var(--border); }
.ord-sum-iname { color:var(--text2); flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding-right:.5rem; }
.ord-sum-imeta { color:var(--muted); }
.ord-sum-iamt  { font-weight:600; color:var(--text); flex-shrink:0; }
.ord-sum-total { display:flex; justify-content:space-between; align-items:baseline; padding-top:.4rem; margin-top:.1rem; }
.ord-sum-tlbl  { font-size:.75rem; font-weight:700; color:var(--text2); }
.ord-sum-tamt  { font-size:1.35rem; font-weight:800; color:var(--text); font-variant-numeric:tabular-nums; }

/* ── Customer quick-view ── */
.cust-qv { font-size:.78rem; color:var(--text3); margin-bottom:.85rem; display:flex; align-items:center; gap:.4rem; }
.cust-qv strong { color:var(--text2); }

/* ── Payment ── */
.pmethod-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.4rem; margin-bottom:.5rem; }
.pm-btn { padding:.4rem; border:2px solid var(--border); border-radius:6px; background:transparent; font-size:.72rem; font-weight:700; cursor:pointer; color:var(--text3); font-family:inherit; transition:all .15s; }
.pm-btn.active { border-color:var(--gold); background:var(--gold-light); color:#92740a; }
.dark .pm-btn.active { color:var(--gold); }
.amount-wrap { position:relative; }
.amount-prefix { position:absolute; left:.65rem; top:50%; transform:translateY(-50%); font-weight:700; color:var(--muted); font-size:.9rem; pointer-events:none; }
.amount-input { width:100%; padding:.55rem .65rem .55rem 1.7rem; border:1px solid var(--border2); border-radius:6px; background:var(--bg2); font-size:1rem; font-weight:700; color:var(--text); outline:none; font-family:inherit; transition:border-color .15s; }
.amount-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.quick-amts { display:flex; flex-wrap:wrap; gap:.3rem; margin-top:.4rem; }
.q-btn { padding:.25rem .55rem; border:1px solid var(--border); border-radius:4px; background:transparent; font-size:.68rem; font-weight:500; color:var(--text3); cursor:pointer; font-family:inherit; transition:all .15s; }
.q-btn:hover { border-color:var(--gold); color:var(--gold); }

/* ── Total / Change ── */
.total-row { display:flex; justify-content:space-between; align-items:baseline; padding-top:.5rem; margin-bottom:.75rem; }
.total-lbl { font-size:.75rem; font-weight:700; color:var(--text2); }
.total-amt { font-size:1.6rem; font-weight:800; color:var(--text); font-variant-numeric:tabular-nums; }
.change-box { border-radius:7px; padding:.5rem .8rem; display:flex; justify-content:space-between; align-items:center; margin-top:.5rem; }
.change-box.change  { background:#f0fdf4; border:1px solid #bbf7d0; }
.change-box.balance { background:#fffbeb; border:1px solid #fde68a; }
.change-box.exact   { background:#f0fdf4; border:1px solid #bbf7d0; justify-content:center; }
.dark .change-box.change  { background:rgba(16,185,129,.1); border-color:rgba(16,185,129,.3); }
.dark .change-box.balance { background:rgba(245,158,11,.1); border-color:rgba(245,158,11,.3); }
.dark .change-box.exact   { background:rgba(16,185,129,.1); border-color:rgba(16,185,129,.3); }
.chg-lbl { font-size:.78rem; font-weight:500; }
.change  .chg-lbl { color:#15803d; }
.balance .chg-lbl { color:#92400e; }
.exact   .chg-lbl { color:#15803d; }
.dark .change  .chg-lbl { color:#4ade80; }
.dark .balance .chg-lbl { color:#fbbf24; }
.dark .exact   .chg-lbl { color:#4ade80; }
.chg-amt { font-size:1.1rem; font-weight:700; font-variant-numeric:tabular-nums; }
.change  .chg-amt { color:#15803d; }
.balance .chg-amt { color:#92400e; }
.dark .change  .chg-amt { color:#4ade80; }
.dark .balance .chg-amt { color:#fbbf24; }

/* ── Complete button ── */
.complete-btn { width:100%; padding:.75rem; border-radius:8px; background:var(--gold); color:#111827; font-size:.88rem; font-weight:800; letter-spacing:.04em; border:none; cursor:pointer; transition:background .15s; display:flex; align-items:center; justify-content:center; gap:.4rem; font-family:inherit; margin-top:.75rem; }
.complete-btn:hover { background:var(--gold-h); }
.complete-btn:disabled { opacity:.55; cursor:not-allowed; }
.btn-loading { display:flex; align-items:center; gap:.4rem; }

/* ── Modal ── */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:50; display:flex; align-items:center; justify-content:center; padding:1rem; }
.modal-box { background:var(--bg); border-radius:14px; width:100%; max-width:560px; max-height:92vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.25); }
.modal-head { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem .75rem; border-bottom:1px solid var(--bg3); position:sticky; top:0; background:var(--bg); z-index:1; border-radius:14px 14px 0 0; }
.dark .modal-head { border-color:var(--border); }
.modal-title { font-size:1rem; font-weight:700; color:var(--text); }
.modal-close { background:none; border:none; cursor:pointer; color:var(--muted); font-size:1.25rem; line-height:1; padding:.2rem; transition:color .15s; }
.modal-close:hover { color:#ef4444; }
.modal-body { padding:1.25rem 1.5rem; }
.modal-foot { display:flex; justify-content:flex-end; gap:.6rem; padding:.85rem 1.5rem 1.25rem; border-top:1px solid var(--bg3); }
.dark .modal-foot { border-color:var(--border); }
.modal-desc    { font-size:.82rem; color:var(--text3); margin-bottom:1rem; }
.modal-ok      { color:#15803d; }
.dark .modal-ok { color:#4ade80; }

/* ── Modal Steps ── */
.step-indicator { display:flex; align-items:center; gap:.4rem; margin-bottom:1.25rem; }
.step-dot { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:700; border:2px solid var(--border); color:var(--muted); }
.step-dot.done   { background:var(--gold); border-color:var(--gold); color:#111827; }
.step-dot.active { background:var(--gold-light); border-color:var(--gold); color:#92740a; }
.step-line      { flex:1; height:2px; background:var(--border); }
.step-line.done { background:var(--gold); }
.dark .step-dot.active { background:rgba(201,168,76,.15); color:var(--gold); }

/* ── Customer search in modal ── */
.cust-search-wrap { position:relative; margin-bottom:.75rem; }
.cust-search-wrap input { width:100%; padding:.5rem .75rem; border:1px solid var(--border2); border-radius:7px; font-size:.85rem; font-family:inherit; color:var(--text); background:var(--bg2); outline:none; }
.cust-search-wrap input:focus { border-color:var(--gold); }
.cust-dropdown { border:1px solid var(--border); border-radius:7px; overflow:hidden; margin-bottom:.75rem; }
.cust-opt { padding:.6rem .85rem; cursor:pointer; border-bottom:1px solid var(--bg3); font-size:.82rem; color:var(--text2); }
.dark .cust-opt { border-color:var(--border); }
.cust-opt:last-child { border-bottom:none; }
.cust-opt:hover { background:var(--gold-light); }
.cust-opt-sub { color:var(--muted); }

/* ── Measurement fields ── */
.meas-grid { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; }
.mfield { display:flex; flex-direction:column; gap:.25rem; }
.mfield label { font-size:.65rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--text3); }
.mfield input { padding:.42rem .6rem; border:1px solid var(--border2); border-radius:6px; background:var(--bg2); font-size:.85rem; color:var(--text); outline:none; font-family:inherit; }
.mfield input:focus { border-color:var(--gold); }

/* ── BOM list ── */
.bom-item { display:grid; grid-template-columns:1fr 80px 60px; gap:.5rem; align-items:center; padding:.4rem 0; border-bottom:1px solid var(--bg3); }
.dark .bom-item { border-color:var(--border); }
.bom-name { font-size:.82rem; color:var(--text2); }
.bom-unit { font-size:.72rem; color:var(--muted); }
.bom-qty-input { width:70px; padding:.3rem .4rem; border:1px solid var(--border2); border-radius:5px; font-size:.8rem; text-align:center; font-family:inherit; background:var(--bg2); outline:none; color:var(--text); }
.bom-qty-input:focus { border-color:var(--gold); }

/* ── Washing section ── */
.wash-section { margin-top:1rem; padding:.75rem; background:var(--bg2); border-radius:8px; border:1px solid var(--border); }
.wash-lbl { display:flex; align-items:center; gap:.5rem; cursor:pointer; font-size:.85rem; font-weight:600; color:var(--text2); }

/* ── Modal buttons ── */
.mbtn { padding:.5rem 1.1rem; border-radius:7px; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .15s; border:none; }
.mbtn-secondary { background:var(--bg3); color:var(--text2); }
.dark .mbtn-secondary { background:var(--border); }
.mbtn-secondary:hover { background:var(--border2); }
.mbtn-primary { background:var(--gold); color:#111827; }
.mbtn-primary:hover { background:var(--gold-h); }

/* ── Receipt ── */
.receipt-wrap    { max-width:440px; margin:0 auto; }
.receipt-actions { display:flex; justify-content:flex-end; gap:.6rem; margin-bottom:1rem; }
.r-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.5rem 1rem; border-radius:7px; font-size:.8rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all .15s; border:none; }
.r-btn.outline { background:transparent; border:1px solid var(--border2); color:var(--text2); }
.r-btn.primary  { background:var(--gold); color:#111827; }
.r-btn.primary:hover { background:var(--gold-h); }
.receipt-card { background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:2rem; }

/* Receipt content */
.rc-center    { text-align:center; margin-bottom:1.25rem; }
.rc-brand     { font-family:Georgia,serif; font-size:1.5rem; font-weight:700; letter-spacing:.15em; color:var(--text); }
.rc-tagline   { font-size:.7rem; color:var(--muted); margin-top:.2rem; }
.rc-divider   { border-top:1px dashed var(--border2); margin:.6rem 0; }
.rc-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:.2rem .5rem; font-size:.75rem; margin-bottom:.8rem; }
.rc-lbl       { color:var(--muted); }
.rc-val       { text-align:right; color:var(--text2); }
.rc-ref       { text-align:right; font-weight:700; font-family:monospace; color:var(--text); }
.rc-cust-section { font-size:.75rem; margin-bottom:.75rem; }
.rc-cust-lbl  { color:var(--muted); text-transform:uppercase; letter-spacing:.1em; font-size:.62rem; margin-bottom:.25rem; }
.rc-cust-name { font-weight:600; color:var(--text); }
.rc-cust-sub  { color:var(--text3); }
.rc-items-table { width:100%; font-size:.75rem; margin-bottom:.75rem; border-collapse:collapse; }
.rc-th { padding-bottom:.35rem; font-weight:600; color:var(--muted); font-size:.62rem; text-transform:uppercase; letter-spacing:.08em; }
.rc-th.l { text-align:left; }
.rc-th.c { text-align:center; }
.rc-th.r { text-align:right; }
.rc-td-row   { border-top:1px solid var(--bg3); }
.dark .rc-td-row { border-color:var(--border); }
.rc-td-name  { padding:.3rem .3rem .3rem 0; color:var(--text2); }
.rc-td-qty   { text-align:center; color:var(--text3); }
.rc-td-price { text-align:right; color:var(--text3); }
.rc-td-amt   { text-align:right; font-weight:600; color:var(--text); }
.rc-bespoke  { font-size:.6rem; color:#92400e; background:#fef3c7; padding:.1rem .3rem; border-radius:3px; margin-left:.3rem; }
.dark .rc-bespoke { color:#fbbf24; background:rgba(251,191,36,.1); }
.rc-totals   { font-size:.85rem; margin-bottom:.75rem; }
.rc-tot-row  { display:flex; justify-content:space-between; margin-bottom:.25rem; color:var(--text3); }
.rc-tot-main { font-weight:700; font-size:1rem; color:var(--text); }
.rc-balance  { color:#dc2626; font-weight:700; }
.dark .rc-balance { color:#f87171; }
.rc-status-badge   { display:inline-block; padding:.3rem 1rem; border-radius:999px; font-size:.7rem; font-weight:700; letter-spacing:.1em; }
.rc-status-paid    { background:#dcfce7; color:#15803d; }
.rc-status-partial { background:#fef9c3; color:#854d0e; }
.rc-status-unpaid  { background:#fee2e2; color:#991b1b; }
.dark .rc-status-paid    { background:rgba(16,185,129,.15); color:#4ade80; }
.dark .rc-status-partial { background:rgba(245,158,11,.15); color:#fbbf24; }
.dark .rc-status-unpaid  { background:rgba(220,38,38,.15); color:#f87171; }
.rc-notes  { font-size:.72rem; color:var(--muted); font-style:italic; margin-bottom:.6rem; }
.rc-footer { text-align:center; font-size:.7rem; color:var(--muted); line-height:2; }

/* ── Spinner ── */
.pos-spin { width:1rem; height:1rem; border:2px solid rgba(17,24,39,.3); border-top-color:#111827; border-radius:50%; animation:spin .6s linear infinite; }
.dark .pos-spin { border-color:rgba(249,250,251,.3); border-top-color:#f9fafb; }
@keyframes spin { to { transform:rotate(360deg); } }

@media print {
    .receipt-actions, nav, header, aside, .fi-sidebar { display:none !important; }
    .receipt-card { max-width:100%; border:none; box-shadow:none; border-radius:0; }
}
</style>

{{-- ═══════════════ RECEIPT ═══════════════ --}}
@if ($showReceipt && $this->getCompletedOrder())
@php $order = $this->getCompletedOrder(); @endphp
<div class="receipt-wrap">
    <div class="receipt-actions">
        <button onclick="window.print()" class="r-btn outline">🖨 Print</button>
        <button wire:click="newSale" class="r-btn primary">+ New Sale</button>
    </div>
    <div class="receipt-card">
        <div class="rc-center">
            <div class="rc-brand">STYLED<span style="color:#C9A84C;">INEE</span></div>
            <div class="rc-tagline">Premium Bespoke Tailoring · Uyo, Nigeria</div>
            <div class="rc-divider" style="margin-top:.8rem;"></div>
        </div>
        <div class="rc-meta-grid">
            <span class="rc-lbl">Receipt No.</span><span class="rc-ref">{{ $order->reference }}</span>
            <span class="rc-lbl">Date</span><span class="rc-val">{{ $order->created_at->format('d M Y, g:ia') }}</span>
            <span class="rc-lbl">Type</span><span class="rc-val">{{ ucwords(str_replace('_',' ',$order->type)) }}</span>
            <span class="rc-lbl">Served by</span><span class="rc-val">{{ auth()->user()->name }}</span>
            @if($order->estimated_completion_date)
            <span class="rc-lbl">Ready by</span><span class="rc-val" style="font-weight:600;">{{ $order->estimated_completion_date->format('D, d M Y') }}</span>
            @endif
        </div>
        <div class="rc-divider"></div>
        <div class="rc-cust-section">
            <div class="rc-cust-lbl">Customer</div>
            <div class="rc-cust-name">{{ $order->customer_name }}</div>
            <div class="rc-cust-sub">{{ $order->customer_phone }}</div>
            @if($order->customer_email)<div class="rc-cust-sub">{{ $order->customer_email }}</div>@endif
        </div>
        <div class="rc-divider"></div>
        <table class="rc-items-table">
            <thead>
                <tr>
                    <th class="rc-th l">Item</th>
                    <th class="rc-th c">Qty</th>
                    <th class="rc-th r">Price</th>
                    <th class="rc-th r">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr class="rc-td-row">
                    <td class="rc-td-name">
                        {{ $item->description }}
                        @if($item->production_type==='production')<span class="rc-bespoke">BESPOKE</span>@endif
                    </td>
                    <td class="rc-td-qty">{{ $item->quantity }}</td>
                    <td class="rc-td-price">₦{{ number_format($item->unit_price,2) }}</td>
                    <td class="rc-td-amt">₦{{ number_format($item->subtotal,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="rc-divider"></div>
        <div class="rc-totals">
            <div class="rc-tot-row">
                <span>Total</span><span class="rc-tot-main">₦{{ number_format($order->total_amount,2) }}</span>
            </div>
            <div class="rc-tot-row">
                <span>Amount Paid</span><span>₦{{ number_format($order->amount_paid,2) }}</span>
            </div>
            @if($order->amount_paid >= $order->total_amount)
            <div class="rc-tot-row">
                <span>Change</span><span>₦{{ number_format($order->amount_paid - $order->total_amount,2) }}</span>
            </div>
            @else
            <div class="rc-tot-row rc-balance">
                <span>Balance Due</span><span>₦{{ number_format($order->total_amount - $order->amount_paid,2) }}</span>
            </div>
            @endif
        </div>
        <div style="text-align:center; margin:1rem 0;">
            @php
                $sCls = match($order->payment_status) { 'paid' => 'rc-status-paid', 'partial' => 'rc-status-partial', default => 'rc-status-unpaid' };
                $sTxt = match($order->payment_status) { 'paid' => '✓ PAID IN FULL', 'partial' => '⚡ PARTIAL PAYMENT', default => '✗ UNPAID' };
            @endphp
            <span class="rc-status-badge {{ $sCls }}">{{ $sTxt }}</span>
        </div>
        @if($order->notes)
        <div class="rc-notes">Note: {{ $order->notes }}</div>
        @endif
        <div class="rc-divider"></div>
        <div class="rc-footer">
            <div>Thank you for choosing Styledinee.</div>
            <div>Questions? Call us or visit our studio in Uyo.</div>
        </div>
    </div>
</div>

@else
{{-- ═══════════════ MAIN POS ═══════════════ --}}
<div class="pos-shell">

    {{-- ── LEFT: Product Grid ── --}}
    <div class="pos-left">

        {{-- Order Type --}}
        <div class="pcard" style="margin-bottom:.75rem;">
            <span class="plbl">Order Type</span>
            <div class="otype-wrap">
                @foreach (['tailoring'=>['✦','Tailoring'],'dry_cleaning'=>['◈','Dry Cleaning'],'alteration'=>['⌖','Alteration'],'pickup_delivery'=>['⟳','Pickup & Del.']] as $v=>[$ico,$lbl])
                <button wire:click="$set('orderType','{{ $v }}')" class="otype-btn {{ $orderType===$v?'active':'' }}">
                    <span>{{ $ico }}</span><span>{{ $lbl }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Product Search --}}
        <div class="pos-search-wrap">
            <span class="pos-search-icon">⌕</span>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search products by name or category…">
        </div>

        {{-- Products --}}
        @php $products = $this->getProducts(); @endphp
        @if($products->isEmpty())
            <p class="no-results">No products found.</p>
        @else
        <div class="prod-grid" style="margin-bottom:1rem;">
            @foreach ($products as $product)
            <div class="prod-card" wire:click="selectProduct({{ $product->id }})" wire:key="prod-{{ $product->id }}">
                @php
                    $icon = match(true) {
                        $product->production_type === 'production'  => ['🪡', 'Bespoke'],
                        $product->category === 'accessory'          => ['👜', 'Accessory'],
                        $product->category === 'fabric'             => ['🧵', 'Fabric'],
                        $product->category === 'ready_made'         => ['👗', 'Ready-made'],
                        default                                     => ['🛍', 'Product'],
                    };
                @endphp
                <div class="prod-img-wrap {{ $product->image ? 'is-loading' : '' }}">
                    {{-- Placeholder always underneath --}}
                    <div class="prod-img-ph">
                        <span>{{ $icon[0] }}</span>
                        <span class="prod-img-ph-label">{{ $icon[1] }}</span>
                    </div>
                    {{-- Image overlays on load; disappears on error --}}
                    @if($product->image)
                    <img
                        src="{{ Storage::url($product->image) }}"
                        alt="{{ $product->name }}"
                        class="prod-img"
                        loading="lazy"
                        decoding="async"
                        onload="this.closest('.prod-img-wrap').classList.remove('is-loading')"
                        onerror="this.style.opacity=0;this.closest('.prod-img-wrap').classList.remove('is-loading')"
                    >
                    @endif
                </div>
                <div class="prod-info">
                    <div class="prod-badge {{ $product->production_type==='production'?'production':'ready' }}">
                        {{ $product->production_type==='production'?'Bespoke':'Ready' }}
                    </div>
                    <div class="prod-name">{{ $product->name }}</div>
                    <div class="prod-price">₦{{ number_format($product->price,0) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Services Quick-Add --}}
        @if ($this->getServices()->isNotEmpty())
        <div class="pcard">
            <span class="plbl">Quick-Add Services</span>
            <div class="svc-pills">
                @foreach ($this->getServices() as $svc)
                <button wire:click="addServiceItem({{ $svc->id }})" class="svc-pill">
                    + {{ $svc->name }}
                    @if($svc->base_price)<span class="svc-pill-price">₦{{ number_format($svc->base_price,0) }}</span>@endif
                </button>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="pos-right">

        @if ($posStep === 'order')
        {{-- ════════ STEP 1: ORDER ════════ --}}

        <div class="cart-scroll">

            <div class="step-breadcrumb">
                <span class="step-bc-active">Order</span>
                <span class="step-bc-sep">→</span>
                <span class="step-bc-inactive">Payment</span>
            </div>

            {{-- Cart items --}}
            @foreach ($items as $i => $item)
                @if(!empty($item['product_id']) && !empty(trim($item['description']??'')))
                @php $pathKey = $item['production_path_key'] ?? 'none'; @endphp
                <div class="cart-item" wire:key="ci-{{ $i }}">
                    <div class="item-top">
                        <div class="item-name-col">
                            <div class="ci-name">{{ $item['description'] }}</div>
                            @if(($item['production_type']??'ready_made')==='production')
                            <span class="ci-badge-prod">Bespoke</span>
                            @if(!empty($item['_customer_name']))<span class="ci-meta"> · {{ $item['_customer_name'] }}</span>@endif
                            @endif
                        </div>
                        <button wire:click="removeItem({{ $i }})" class="ci-rm">✕</button>
                    </div>
                    <div class="item-bot">
                        <select wire:model.live="items.{{ $i }}.production_path_key"
                            class="path-select {{ $pathKey !== 'none' ? 'has-path' : '' }}" style="flex:1;min-width:0;">
                            <option value="none">Ready-made</option>
                            <option value="sewing_only">Sewing → Finishing</option>
                            <option value="sewing_embroidery">Sewing → Embroidery → Finishing</option>
                            <option value="sewing_printing">Sewing → Printing → Finishing</option>
                            <option value="sewing_embroidery_printing">Sewing → Embroidery → Printing → Finishing</option>
                            <option value="embroidery_only">Embroidery → Finishing</option>
                            <option value="printing_only">Printing only</option>
                            <option value="embroidery_printing">Embroidery → Printing → Finishing</option>
                        </select>
                        <input wire:model.live="items.{{ $i }}.qty" type="number" min="1" class="ci-input">
                        <div class="ci-price">₦{{ number_format((float)($item['subtotal']??0),0) }}</div>
                    </div>
                </div>
                @else
                @php $pathKey = $item['production_path_key'] ?? 'none'; @endphp
                <div class="manual-row" wire:key="mr-{{ $i }}">
                    <div class="item-top">
                        <input wire:model.live="items.{{ $i }}.description" type="text" placeholder="Description…" class="mi mi-desc">
                        <button wire:click="removeItem({{ $i }})" class="ci-rm" title="Remove">✕</button>
                    </div>
                    <div class="item-bot">
                        <select wire:model.live="items.{{ $i }}.production_path_key"
                            class="path-select {{ $pathKey !== 'none' ? 'has-path' : '' }}" style="flex:1;min-width:0;">
                            <option value="none">Ready-made</option>
                            <option value="sewing_only">Sewing → Finishing</option>
                            <option value="sewing_embroidery">Sewing → Embroidery → Finishing</option>
                            <option value="sewing_printing">Sewing → Printing → Finishing</option>
                            <option value="sewing_embroidery_printing">Sewing → Embroidery → Printing → Finishing</option>
                            <option value="embroidery_only">Embroidery → Finishing</option>
                            <option value="printing_only">Printing only</option>
                            <option value="embroidery_printing">Embroidery → Printing → Finishing</option>
                        </select>
                        <input wire:model.live="items.{{ $i }}.qty" type="number" min="1" step="1" class="mi mi-qty">
                        <input wire:model.live="items.{{ $i }}.unit_price" type="number" min="0" step="0.01" placeholder="₦ Price" class="mi mi-price">
                    </div>
                </div>
                @endif
            @endforeach

            <button wire:click="addItem" class="add-row-btn">+ Add Line</button>

            {{-- Customer strip --}}
            @if($customerName)
            <div class="cust-strip" wire:click="openCustomerModal">
                <div class="cust-strip-info">
                    <span class="cust-strip-icon">👤</span>
                    <div>
                        <span class="cust-strip-name">{{ $customerName }}</span>
                        @if($customerPhone)<span class="cust-strip-phone"> · {{ $customerPhone }}</span>@endif
                        @if($estimatedCompletionDate)<span class="cust-strip-date"> · Ready {{ \Carbon\Carbon::parse($estimatedCompletionDate)->format('d M') }}</span>@endif
                    </div>
                </div>
                <button wire:click.stop="openCustomerModal" class="cust-strip-edit">Edit ✎</button>
            </div>
            @else
            <button wire:click="openCustomerModal" class="cust-add-btn">
                <span>+ Add Customer</span>
                <span class="cust-add-hint">required</span>
            </button>
            @endif

            <div class="pf" style="margin-bottom:.5rem;">
                <label class="plbl" style="margin-bottom:.3rem; display:block;">Notes</label>
                <input wire:model.live="notes" type="text" placeholder="Special instructions, fabric preference…" class="notes-input">
            </div>

        </div>{{-- /cart-scroll --}}

        <div class="cart-foot">
            <div class="total-row" style="margin-bottom:0;">
                <span class="total-lbl">TOTAL</span>
                <span class="total-amt">₦{{ number_format($this->getTotal(),0) }}</span>
            </div>
            <button wire:click="processOrder" class="complete-btn" style="margin-top:.75rem;">
                Process →
            </button>
        </div>

        @else
        {{-- ════════ STEP 2: PAYMENT ════════ --}}

        <div class="cart-scroll">

            <div class="step-bc-between">
                <div class="step-bc-row">
                    <span class="step-bc-inactive">Order</span>
                    <span class="step-bc-sep">→</span>
                    <span class="step-bc-active">Payment</span>
                </div>
                <button wire:click="backToOrder" class="step-bc-back">← Edit Order</button>
            </div>

            {{-- Read-only order summary --}}
            <div class="order-summary">
                <div class="ord-sum-hdr">
                    <span class="plbl">Order Summary</span>
                    <button wire:click="$toggle('orderSummaryCollapsed')" class="ord-sum-toggle">
                        {{ $orderSummaryCollapsed ? '▼ Show' : '▲ Hide' }}
                    </button>
                </div>
                @if(! $orderSummaryCollapsed)
                @foreach(collect($items)->filter(fn($i) => !empty(trim($i['description']??''))) as $item)
                <div class="ord-sum-item">
                    <span class="ord-sum-iname">
                        {{ $item['description'] }}
                        @if((int)($item['qty']??1) > 1)<span class="ord-sum-imeta"> ×{{ $item['qty'] }}</span>@endif
                    </span>
                    <span class="ord-sum-iamt">₦{{ number_format((float)($item['subtotal']??0),0) }}</span>
                </div>
                @endforeach
                @endif
                <div class="ord-sum-total">
                    <span class="ord-sum-tlbl">TOTAL</span>
                    <span class="ord-sum-tamt">₦{{ number_format($this->getTotal(),0) }}</span>
                </div>
            </div>

            {{-- Customer quick-view --}}
            <div class="cust-qv">
                <span style="font-size:1rem;">👤</span>
                <span><strong>{{ $customerName }}</strong> · {{ $customerPhone }}</span>
            </div>
            @if($estimatedCompletionDate)
            <div class="cust-qv" style="margin-top:-.5rem;">
                <span style="font-size:1rem;">📅</span>
                <span>Ready by <strong>{{ \Carbon\Carbon::parse($estimatedCompletionDate)->format('D, d M Y') }}</strong></span>
            </div>
            @endif

        </div>{{-- /cart-scroll --}}

        <div class="cart-foot">

            <div class="pmethod-grid">
                @foreach (['cash'=>'Cash','transfer'=>'Transfer','pos'=>'POS Terminal'] as $m=>$ml)
                <button wire:click="$set('paymentMethod','{{ $m }}')" class="pm-btn {{ $paymentMethod===$m?'active':'' }}">{{ $ml }}</button>
                @endforeach
            </div>

            <div class="amount-wrap" style="margin-bottom:.4rem; margin-top:.5rem;">
                <span class="amount-prefix">₦</span>
                <input wire:model.live="amountPaid" type="number" min="0" step="0.01" placeholder="0.00" class="amount-input" autofocus>
            </div>

            @if ($this->getTotal() > 0)
            @php $shown = 0; @endphp
            <div class="quick-amts">
                <button wire:click="$set('amountPaid', {{ $this->getTotal() }})" class="q-btn">Exact</button>
                @foreach ([500,1000,2000,5000,10000,20000,50000,100000,200000,500000] as $q)
                    @if ($q > $this->getTotal() && $shown < 2)
                    <button wire:click="$set('amountPaid', {{ $q }})" class="q-btn">₦{{ number_format($q,0) }}</button>
                    @php $shown++ @endphp
                    @endif
                @endforeach
            </div>
            @endif

            @if ($amountPaid !== '' && (float)$amountPaid > 0)
                @if ($this->getChange() > 0)
                <div class="change-box change">
                    <span class="chg-lbl">Change Due</span>
                    <span class="chg-amt">₦{{ number_format($this->getChange(),2) }}</span>
                </div>
                @elseif ($this->getBalance() > 0)
                <div class="change-box balance">
                    <span class="chg-lbl">Balance Remaining</span>
                    <span class="chg-amt">₦{{ number_format($this->getBalance(),2) }}</span>
                </div>
                @else
                <div class="change-box exact">
                    <span class="chg-lbl">✓ Exact amount</span>
                </div>
                @endif
            @endif

            <button wire:click="completeSale" wire:loading.attr="disabled" class="complete-btn">
                <span wire:loading.remove wire:target="completeSale">✓ Complete Sale</span>
                <span wire:loading wire:target="completeSale" class="btn-loading">
                    <span class="pos-spin"></span> Processing…
                </span>
            </button>

        </div>{{-- /cart-foot --}}

        @endif{{-- posStep --}}

    </div>{{-- /pos-right --}}

</div>{{-- /pos-shell --}}

{{-- ═══════════════ CUSTOMER MODAL ═══════════════ --}}
@if($showCustomerModal)
<div class="modal-backdrop" wire:click.self="closeCustomerModal">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-head">
            <span class="modal-title">👤 Customer Details</span>
            <button wire:click="closeCustomerModal" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
            <div class="cust-search-wrap">
                <input wire:model.live.debounce.250ms="customerSearch" type="search"
                    placeholder="Search existing customer by name or phone…" autocomplete="off">
                @if($this->getSearchCustomers()->isNotEmpty())
                <div class="cust-dropdown">
                    @foreach($this->getSearchCustomers() as $c)
                    <div class="cust-opt" wire:click="selectCustomer({{ $c->id }})">
                        <strong>{{ $c->name }}</strong> · {{ $c->phone }}
                        @if($c->email)<span class="cust-opt-sub"> · {{ $c->email }}</span>@endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.55rem;margin-bottom:.55rem;">
                <div class="pf">
                    <label>Name *</label>
                    <input wire:model.live="customerName" type="text" placeholder="Full name" autofocus>
                    @error('customerName')<span class="ferr">{{ $message }}</span>@enderror
                </div>
                <div class="pf">
                    <label>Phone *</label>
                    <input wire:model.live="customerPhone" type="tel" placeholder="+234 800 000 0000">
                    @error('customerPhone')<span class="ferr">{{ $message }}</span>@enderror
                </div>
                <div class="pf">
                    <label>Email</label>
                    <input wire:model.live="customerEmail" type="email" placeholder="optional">
                </div>
                <div class="pf">
                    <label>Delivery</label>
                    <select wire:model.live="deliveryType">
                        <option value="pickup">Walk-in pickup</option>
                        <option value="delivery">Home delivery</option>
                    </select>
                </div>
                <div class="pf" style="grid-column:1/-1;">
                    <label>Est. Completion Date</label>
                    <input wire:model.live="estimatedCompletionDate" type="date"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
            </div>
            @if($deliveryType==='delivery')
            <div class="pf" style="margin-bottom:.55rem;">
                <label>Delivery Address</label>
                <input wire:model.live="customerAddress" type="text" placeholder="Full delivery address">
            </div>
            @endif
        </div>
        <div class="modal-foot">
            <button wire:click="closeCustomerModal" class="mbtn mbtn-secondary">Cancel</button>
            <button wire:click="saveCustomerFromModal" class="mbtn mbtn-primary">Save →</button>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════ PRODUCTION MODAL ═══════════════ --}}
@if ($showProductModal && $this->getModalProduct())
@php $mprod = $this->getModalProduct(); @endphp
<div class="modal-backdrop" wire:click.self="closeProductionModal">
    <div class="modal-box">

        <div class="modal-head">
            <span class="modal-title">🪡 {{ $mprod->name }} — Bespoke Order</span>
            <button wire:click="closeProductionModal" class="modal-close">✕</button>
        </div>

        <div class="modal-body">

            <div class="step-indicator">
                @php $hasFields = $mprod->measurementTemplate && !empty($mprod->measurementTemplate->fields); @endphp
                {{-- 1: Customer --}}
                <div class="step-dot {{ $modalStep>=1?($modalStep>1?'done':'active'):'' }}">{{ $modalStep>1?'✓':'1' }}</div>
                <div class="step-line {{ $modalStep>1?'done':'' }}"></div>
                {{-- 2: Design --}}
                <div class="step-dot {{ $modalStep>=2?($modalStep>2?'done':'active'):'' }}">{{ $modalStep>2?'✓':'2' }}</div>
                <div class="step-line {{ $modalStep>2?'done':'' }}"></div>
                {{-- 3: Measurements (conditional) --}}
                @if($hasFields)
                <div class="step-dot {{ $modalStep>=3?($modalStep>3?'done':'active'):'' }}">{{ $modalStep>3?'✓':'3' }}</div>
                <div class="step-line {{ $modalStep>3?'done':'' }}"></div>
                @endif
                {{-- 4: Confirm --}}
                <div class="step-dot {{ $modalStep===4?'active':'' }}">{{ $hasFields?'4':'3' }}</div>
            </div>

            {{-- STEP 1: Customer --}}
            @if ($modalStep === 1)
            @if($mprod->variants->isNotEmpty())
            <div class="mfield" style="margin-bottom:.85rem;">
                <label style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);">Variant</label>
                <select wire:model.live="modalVariantId" class="path-select" style="width:100%;margin-top:.3rem;">
                    <option value="">— No specific variant —</option>
                    @foreach($mprod->variants as $variant)
                    <option value="{{ $variant->id }}">
                        {{ ucfirst($variant->variant_type) }}: {{ $variant->variant_value }}
                        @if($variant->price_adjustment != 0) (+₦{{ number_format($variant->price_adjustment, 0) }}) @endif
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <p class="modal-desc">Search for an existing customer or enter new details.</p>
            <div class="cust-search-wrap">
                <input wire:model.live.debounce.250ms="modalCustomerSearch" type="search" placeholder="Search by name or phone…">
            </div>
            @if($this->getModalCustomers()->isNotEmpty())
            <div class="cust-dropdown">
                @foreach($this->getModalCustomers() as $c)
                <div class="cust-opt" wire:click="selectModalCustomer({{ $c->id }})">
                    <strong>{{ $c->name }}</strong> · {{ $c->phone }}
                    @if($c->email)<span class="cust-opt-sub"> · {{ $c->email }}</span>@endif
                </div>
                @endforeach
            </div>
            @endif
            <div class="cust-grid">
                <div class="mfield">
                    <label>Full Name *</label>
                    <input wire:model.live="modalCustomerName" type="text" placeholder="Customer name">
                </div>
                <div class="mfield">
                    <label>Phone *</label>
                    <input wire:model.live="modalCustomerPhone" type="tel" placeholder="+234 …">
                </div>
                <div class="mfield">
                    <label>Email</label>
                    <input wire:model.live="modalCustomerEmail" type="email" placeholder="optional">
                </div>
            </div>
            @if($modalCustomerId)
            <p class="modal-ok" style="font-size:.72rem; margin-top:.5rem;">✓ Existing customer selected. Saved measurements loaded (if any).</p>
            @endif
            @endif

            {{-- STEP 2: Design --}}
            @if ($modalStep === 2)
            @php
                $pathHint = match(true) {
                    str_contains($mprod->product_type ?? '', 'embroidery') => 'embroidery',
                    str_contains($mprod->product_type ?? '', 'printing')   => 'printing',
                    default => 'sewing',
                };
            @endphp
            <p class="modal-desc">
                Capture the customer's design reference. Leave blank if none —
                @if($pathHint === 'embroidery') <strong style="color:#a855f7;">embroidery pattern required</strong>
                @elseif($pathHint === 'printing') <strong style="color:#ea580c;">print artwork required</strong>
                @else design notes are optional @endif.
            </p>

            <div class="mfield" style="margin-bottom:.85rem;">
                <label style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);">
                    Design Notes / Instructions
                </label>
                <textarea
                    wire:model.live="modalDesignNotes"
                    rows="3"
                    placeholder="E.g. Blue &amp; white, chest placement, 10cm wide. Or paste a description of the pattern…"
                    style="width:100%;margin-top:.3rem;padding:.45rem .6rem;border:1px solid var(--border2);border-radius:7px;background:var(--bg2);font-size:.83rem;color:var(--text);font-family:inherit;resize:vertical;outline:none;"></textarea>
            </div>

            <div class="mfield">
                <label style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);">
                    Design File
                    <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);"> (image, PDF — optional)</span>
                </label>
                <input
                    wire:model="modalDesignFile"
                    type="file"
                    accept="image/*,.pdf,.eps,.ai,.svg"
                    style="margin-top:.3rem;width:100%;font-size:.8rem;color:var(--text3);">
                @error('modalDesignFile')
                <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                @enderror
                @if($modalDesignFile)
                <div style="margin-top:.5rem;display:flex;align-items:center;gap:.5rem;">
                    @php $mime = $modalDesignFile->getMimeType(); @endphp
                    @if(str_starts_with($mime, 'image/'))
                    <img src="{{ $modalDesignFile->temporaryUrl() }}"
                         alt="Design preview"
                         style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                    @endif
                    <span style="font-size:.75rem;color:var(--text3);">
                        📎 {{ $modalDesignFile->getClientOriginalName() }}
                        ({{ round($modalDesignFile->getSize() / 1024) }} KB)
                    </span>
                </div>
                @endif
            </div>
            @endif

            {{-- STEP 3: Measurements --}}
            @if ($modalStep === 3 && $mprod->measurementTemplate && !empty($mprod->measurementTemplate->fields))
            @php
                $fieldIds   = $mprod->measurementTemplate->fields ?? [];
                $measFields = \App\Models\MeasurementField::whereIn('id', $fieldIds)->orderBy('label')->get()->keyBy('id');
            @endphp
            <p class="modal-desc">
                Enter measurements for <strong>{{ $modalCustomerName }}</strong>.
            </p>
            <div class="meas-grid">
                @foreach($fieldIds as $fieldId)
                @php $mf = $measFields[$fieldId] ?? null; @endphp
                @if($mf)
                <div class="mfield">
                    <label>{{ $mf->label }}</label>
                    <input wire:model.live="modalMeasurements.{{ $mf->id }}" type="number" step="0.1" placeholder="0">
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- STEP 4: Confirm --}}
            @if ($modalStep === 4)
            <p class="modal-desc">Review the BOM and washing requirements before adding to cart.</p>

            @if(count($modalBom) > 0)
            <span class="plbl" style="margin-bottom:.4rem; display:block;">Materials Needed (BOM)</span>
            @foreach($modalBom as $bi => $bm)
            <div class="bom-item">
                <span class="bom-name">{{ $bm['name'] }}</span>
                <input wire:model.live="modalBom.{{ $bi }}.quantity" type="number" step="0.1" min="0" class="bom-qty-input">
                <span class="bom-unit">{{ $bm['unit'] }}</span>
            </div>
            @endforeach
            @else
            <p class="modal-desc" style="margin-bottom:.75rem;">No BOM defined for this product.</p>
            @endif

            <div class="wash-section">
                <label class="wash-lbl">
                    <input wire:model.live="modalWashingRequired" type="checkbox">
                    Washing / finishing required
                </label>
                @if(!$modalWashingRequired)
                <div class="mfield" style="margin-top:.6rem;">
                    <label>Reason for skipping washing</label>
                    <input wire:model.live="modalWashingSkipReason" type="text" placeholder="e.g. Dry-clean only fabric">
                </div>
                @endif
            </div>

            <div class="mfield" style="margin-top:.75rem;">
                <label>Item Notes</label>
                <input wire:model.live="modalNotes" type="text" placeholder="Special instructions for this item…">
            </div>
            @endif

        </div>{{-- /modal-body --}}

        <div class="modal-foot">
            @if($modalStep > 1)
            <button wire:click="modalPrev" class="mbtn mbtn-secondary">← Back</button>
            @endif
            @if($modalStep < 4)
            <button wire:click="modalNext" class="mbtn mbtn-primary">Next →</button>
            @else
            <button wire:click="confirmProductionItem" class="mbtn mbtn-primary">+ Add to Cart</button>
            @endif
        </div>

    </div>
</div>
@endif

@endif{{-- /showReceipt --}}

</x-filament-panels::page>
