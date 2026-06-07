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

/* ── Order Category Tabs ── */
.otype-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.5rem; }
.otype-see-all {
    font-size:.65rem; font-weight:600; color:var(--gold); background:none; border:none;
    cursor:pointer; font-family:inherit; letter-spacing:.04em; padding:0;
    text-decoration:underline; text-underline-offset:2px; transition:opacity .15s;
}
.otype-see-all:hover { opacity:.7; }
.otype-wrap { display:grid; grid-template-columns:repeat(4,1fr); gap:.4rem; }
@media(max-width:600px){ .otype-wrap{grid-template-columns:repeat(2,1fr);} }
.otype-btn {
    padding:.45rem .25rem; border:2px solid var(--border); border-radius:7px; background:transparent;
    font-size:.68rem; font-weight:600; color:var(--text3); cursor:pointer; transition:all .15s;
    font-family:inherit; display:flex; flex-direction:column; align-items:center; gap:.2rem; line-height:1.2;
    text-align:center;
}
.otype-btn:hover { border-color:var(--gold); color:var(--gold); }
.otype-btn.active { border-color:var(--gold); background:var(--gold-light); color:#92740a; }
.dark .otype-btn.active { color:var(--gold); }
.otype-btn .otype-parent { font-size:.55rem; color:var(--muted); font-weight:400; }

/* ── Category Modal ── */
.cat-modal-backdrop {
    position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,.55); backdrop-filter:blur(2px);
    display:flex; align-items:center; justify-content:center; padding:1rem;
}
.cat-modal {
    background:var(--bg); border:1px solid var(--border2); border-radius:14px;
    width:100%; max-width:520px; max-height:80vh;
    display:flex; flex-direction:column; overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
}
.cat-modal-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:.85rem 1rem .65rem; border-bottom:1px solid var(--border); flex-shrink:0;
}
.cat-modal-title { font-size:.9rem; font-weight:700; color:var(--text); }
.cat-modal-close {
    background:none; border:none; cursor:pointer; font-size:1.1rem;
    color:var(--muted); padding:.2rem .4rem; border-radius:5px; transition:color .15s;
}
.cat-modal-close:hover { color:var(--text); }
.cat-modal-search {
    padding:.65rem 1rem; border-bottom:1px solid var(--border); flex-shrink:0;
}
.cat-modal-search input {
    width:100%; padding:.5rem .75rem .5rem 2rem; border:1px solid var(--border2);
    border-radius:8px; background:var(--bg2); font-size:.875rem; outline:none;
    font-family:inherit; color:var(--text); box-sizing:border-box;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:.55rem center; background-size:1rem;
    transition:border-color .15s;
}
.cat-modal-search input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.cat-modal-body { flex:1; overflow-y:auto; padding:.65rem 1rem 1rem; }
.cat-modal-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.45rem; }
@media(max-width:400px){ .cat-modal-grid{grid-template-columns:repeat(2,1fr);} }
.cat-modal-empty { text-align:center; color:var(--muted); font-size:.85rem; padding:2rem 0; }

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

/* ── BOM in cart item ── */
.ci-bom { padding:.35rem .75rem .5rem; border-top:1px dashed var(--border); display:flex; flex-direction:column; gap:.2rem; }
.ci-bom-row { display:flex; align-items:center; gap:.5rem; font-size:.72rem; }
.ci-bom-name { flex:1; color:var(--muted); }
.ci-bom-qty { font-weight:500; }
.ci-bom-price { font-weight:600; color:var(--text); white-space:nowrap; }
.ci-bom-rm { background:none; border:none; cursor:pointer; color:var(--muted); font-size:.7rem; padding:0 .1rem; line-height:1; transition:color .15s; }
.ci-bom-rm:hover { color:#dc2626; }
.ci-bom-total { font-size:.7rem; font-weight:700; color:var(--gold); padding-top:.25rem; border-top:1px solid var(--border); text-align:right; margin-top:.1rem; }
.ci-bom-removed { display:flex; flex-wrap:wrap; align-items:baseline; gap:.25rem .4rem; font-size:.68rem; padding:.3rem .4rem; margin-top:.15rem; background:rgba(239,68,68,.08); border:1px dashed rgba(239,68,68,.3); border-radius:6px; color:var(--muted); }
.ci-bom-removed-label { font-weight:700; color:#ef4444; white-space:nowrap; }
.ci-bom-removed-name { text-decoration:line-through; color:var(--text); }
.ci-bom-removed-reason { font-style:italic; }
.ci-bom-removed-meta { opacity:.65; white-space:nowrap; margin-left:auto; }
/* ── Add BOM inline ── */
.ci-bom-add-btn { width:100%; margin-top:.35rem; padding:.3rem; background:none; border:1px dashed var(--border); border-radius:6px; font-size:.7rem; font-weight:600; color:var(--gold); cursor:pointer; transition:all .15s; text-align:center; }
.ci-bom-add-btn:hover { background:rgba(201,168,76,.08); border-color:var(--gold); }
.ci-bom-add-form { margin-top:.35rem; padding:.5rem; background:var(--bg2); border:1px solid var(--border); border-radius:8px; display:flex; flex-direction:column; gap:.4rem; }
.bom-add-search { position:relative; }
.bom-add-input { width:100%; padding:.35rem .5rem; border:1px solid var(--border); border-radius:6px; background:var(--bg); color:var(--text); font-size:.75rem; outline:none; box-sizing:border-box; font-family:inherit; }
.bom-add-input:focus { border-color:var(--gold); }
.bom-add-dropdown { position:absolute; z-index:50; left:0; right:0; top:100%; margin-top:2px; background:var(--bg); border:1px solid var(--border); border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,.12); overflow:hidden; }
.bom-add-result { width:100%; padding:.4rem .6rem; text-align:left; font-size:.73rem; background:none; border:none; cursor:pointer; color:var(--text); transition:background .1s; display:flex; justify-content:space-between; align-items:center; gap:.4rem; font-family:inherit; }
.bom-add-result:hover { background:var(--bg2); }
.bom-add-result-price { font-size:.68rem; color:var(--muted); white-space:nowrap; }
.bom-add-fields { display:grid; grid-template-columns:2fr 1.5fr 2fr; gap:.3rem; }
.bom-add-field { padding:.35rem .5rem; border:1px solid var(--border); border-radius:6px; background:var(--bg); color:var(--text); font-size:.75rem; outline:none; width:100%; box-sizing:border-box; font-family:inherit; }
.bom-add-field:focus { border-color:var(--gold); }
.bom-add-actions { display:flex; gap:.4rem; justify-content:flex-end; }
.bom-add-confirm { padding:.3rem .75rem; background:var(--gold); color:#111; border:none; border-radius:6px; font-size:.72rem; font-weight:700; cursor:pointer; font-family:inherit; }
.bom-add-confirm:hover { background:var(--gold-h); }
.bom-add-cancel { padding:.3rem .6rem; background:none; color:var(--muted); border:1px solid var(--border); border-radius:6px; font-size:.72rem; cursor:pointer; font-family:inherit; }
.bom-add-cancel:hover { background:var(--bg3); }
.bom-add-err { font-size:.68rem; color:#dc2626; margin:0; }
/* ── Remove BOM reason modal ── */
.rm-bom-field { display:flex; flex-direction:column; gap:.4rem; }
.rm-bom-field label { font-size:.8rem; font-weight:600; color:var(--text); }
.rm-bom-field textarea { width:100%; background:var(--bg2); border:1px solid var(--border); border-radius:8px; color:var(--text); padding:.55rem .7rem; font-size:.83rem; resize:vertical; min-height:72px; outline:none; transition:border-color .15s; }
.rm-bom-field textarea:focus { border-color:var(--accent); }
.rm-bom-material { font-size:.8rem; color:var(--muted); margin-bottom:.9rem; background:var(--bg2); padding:.5rem .75rem; border-radius:8px; }

/* ── Production path badge (replaces old path-select) ── */
.path-badge {
    font-size:.58rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
    padding:.1rem .4rem; border-radius:4px; flex-shrink:0;
    background:rgba(201,168,76,.12); color:var(--gold); border:1px solid rgba(201,168,76,.3);
}
.dark .path-badge { background:rgba(201,168,76,.1); }

/* ── Production path select (kept for legacy, hidden in POS) ── */
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
.clear-cart-btn { padding:.75rem 1rem; border-radius:8px; background:var(--bg2); color:var(--text3); font-size:.88rem; font-weight:700; border:1.5px solid var(--border); cursor:pointer; transition:all .15s; font-family:inherit; flex-shrink:0; }
.clear-cart-btn:hover { background:#fef2f2; color:#dc2626; border-color:#fca5a5; }
.dark .clear-cart-btn:hover { background:#450a0a; color:#f87171; border-color:#7f1d1d; }
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
.bom-item { display:grid; grid-template-columns:1fr 80px 60px auto; gap:.5rem; align-items:center; padding:.4rem 0; border-bottom:1px solid var(--bg3); }
.dark .bom-item { border-color:var(--border); }
.bom-item--pending { opacity:.5; pointer-events:none; }
.bom-name { font-size:.82rem; color:var(--text2); }
.bom-unit { font-size:.72rem; color:var(--muted); }
.bom-qty-input { width:70px; padding:.3rem .4rem; border:1px solid var(--border2); border-radius:5px; font-size:.8rem; text-align:center; font-family:inherit; background:var(--bg2); outline:none; color:var(--text); }
.bom-qty-input:focus { border-color:var(--gold); }
.bom-rm-btn { background:none; border:none; cursor:pointer; color:var(--muted); font-size:.75rem; line-height:1; padding:.15rem .25rem; border-radius:4px; transition:color .15s,background .15s; }
.bom-rm-btn:hover { color:#dc2626; background:rgba(220,38,38,.1); }
.bom-remove-inline { margin:.5rem 0 .35rem; padding:.65rem .75rem; background:rgba(239,68,68,.07); border:1px dashed rgba(239,68,68,.35); border-radius:8px; display:flex; flex-direction:column; gap:.4rem; }
.bom-remove-inline-title { font-size:.78rem; color:var(--text); margin:0; }
.bom-remove-inline-input { width:100%; background:var(--bg2); border:1px solid var(--border); border-radius:6px; color:var(--text); padding:.4rem .6rem; font-size:.78rem; resize:none; outline:none; font-family:inherit; }
.bom-remove-inline-input:focus { border-color:#dc2626; }
.bom-remove-inline-err { font-size:.72rem; color:#ef4444; }
.bom-remove-inline-actions { display:flex; justify-content:flex-end; gap:.4rem; }
.bom-removed-note { display:flex; flex-wrap:wrap; align-items:baseline; gap:.25rem .4rem; font-size:.72rem; padding:.3rem .5rem; margin-top:.2rem; background:rgba(239,68,68,.07); border:1px dashed rgba(239,68,68,.25); border-radius:6px; color:var(--muted); }
.bom-removed-note-icon { color:#ef4444; font-weight:700; }
.bom-removed-note-name { text-decoration:line-through; color:var(--text2); }
.bom-removed-note-reason { font-style:italic; }
.bom-removed-note-meta { opacity:.6; margin-left:auto; white-space:nowrap; font-size:.68rem; }

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

/* ── BOM toggle chip ── */
.ci-bom-toggle {
    width:100%; margin-top:.3rem; padding:.22rem .4rem;
    background:none; border:none; cursor:pointer; font-family:inherit;
    display:flex; align-items:center; gap:.4rem; font-size:.7rem; color:var(--text3);
    text-align:left; border-radius:4px; transition:background .1s;
}
.ci-bom-toggle:hover { background:var(--bg3); }
.ci-bom-toggle-cost { margin-left:auto; font-weight:600; color:var(--gold); font-size:.68rem; flex-shrink:0; }
.ci-bom-toggle-arrow { color:var(--muted); font-size:.6rem; flex-shrink:0; }
.ci-bom-removed-chip { font-size:.6rem; font-weight:700; color:#dc2626; background:rgba(220,38,38,.08); padding:.1rem .3rem; border-radius:3px; flex-shrink:0; }

/* ── Order details section separator ── */
.cart-meta-sep { height:1px; background:var(--border); margin:.65rem 0 .7rem; }

/* ── Delivery type buttons ── */
.dtype-wrap { display:flex; gap:.5rem; margin-bottom:.5rem; }
.dtype-btn {
    flex:1; padding:.45rem; border-radius:.4rem; font-size:.8rem; font-weight:600;
    cursor:pointer; font-family:inherit;
    border:2px solid var(--border2); background:var(--bg2); color:var(--text3);
    transition:all .15s;
}
.dtype-btn.active { border-color:var(--gold); background:#fef9ee; color:#92740a; }
.dark .dtype-btn.active { background:rgba(201,168,76,.1); color:var(--gold); }

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
@php $bomMode = \App\Models\AppSetting::get('bom_mode', 'full'); @endphp
<div class="pos-shell">

    {{-- ── LEFT: Product Grid ── --}}
    <div class="pos-left">

        {{-- Category grid (all categories flat, max 8 shown + See All modal) --}}
        @php
            $allCats     = $this->getCategories();
            $visibleCats = $allCats->take(8);
            $totalCats   = $allCats->count();
        @endphp

        <div class="pcard" style="margin-bottom:.75rem;">
            <div class="otype-header">
                <span class="plbl" style="margin-bottom:0">Category</span>
                <button class="otype-see-all" wire:click="openCategoryModal">
                    See all ({{ $totalCats }})
                </button>
            </div>

            <div class="otype-wrap">
                <button wire:click="selectCategory(0, '')"
                    class="otype-btn {{ $categoryFilter === null ? 'active' : '' }}">
                    <span>🗂</span><span>All</span>
                </button>
                @foreach($visibleCats as $cat)
                    <button wire:click="selectCategory({{ $cat->id }}, '{{ $cat->slug }}')"
                        class="otype-btn {{ $categoryFilter === $cat->id ? 'active' : '' }}">
                        @if($cat->icon)<span>{{ $cat->icon }}</span>@endif
                        <span>{{ $cat->name }}</span>
                        @if($cat->parent_id)
                            <span class="otype-parent">{{ $cat->parent?->name }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Category "See All" Modal ── --}}
        @if($showCategoryModal)
        <div class="cat-modal-backdrop" wire:click.self="closeCategoryModal">
            <div class="cat-modal">
                <div class="cat-modal-head">
                    <span class="cat-modal-title">All Categories</span>
                    <button class="cat-modal-close" wire:click="closeCategoryModal">✕</button>
                </div>
                <div class="cat-modal-search">
                    <input
                        wire:model.live.debounce.200ms="categoryModalSearch"
                        type="search"
                        placeholder="Search categories…"
                        autofocus>
                </div>
                <div class="cat-modal-body">
                    @php $modalCats = $this->getModalCategories(); @endphp
                    @if($modalCats->isEmpty())
                        <p class="cat-modal-empty">No categories match "{{ $categoryModalSearch }}"</p>
                    @else
                        <div class="cat-modal-grid">
                            <button wire:click="selectCategory(0, '')"
                                class="otype-btn {{ $categoryFilter === null ? 'active' : '' }}">
                                <span>🗂</span><span>All</span>
                            </button>
                            @foreach($modalCats as $cat)
                                <button wire:click="selectCategory({{ $cat->id }}, '{{ $cat->slug }}')"
                                    class="otype-btn {{ $categoryFilter === $cat->id ? 'active' : '' }}">
                                    @if($cat->icon)<span>{{ $cat->icon }}</span>@endif
                                    <span>{{ $cat->name }}</span>
                                    @if($cat->parent_id)
                                        <span class="otype-parent">{{ $cat->parent?->name }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Product Search --}}
        <div class="pos-search-wrap">
            <span class="pos-search-icon">⌕</span>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search products…">
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
                    $catName = $product->orderType?->name ?? 'Product';
                    $catIcon = $product->orderType?->icon
                        ?? ($product->production_type === 'production' ? '🪡' : '🛍');
                    $icon = [$catIcon, $catName];
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
                        {{ $catName }}
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
        <livewire:pos.order-sidebar
            :items="$items"
            :customer-id="$customerId"
            :customer-name="$customerName"
            :customer-phone="$customerPhone"
            :customer-address="$customerAddress"
            :delivery-type="$deliveryType"
            :estimated-completion-date="$estimatedCompletionDate"
            :notes="$notes"
        />
    </div>{{-- /pos-right --}}

    {{-- REMOVED old right panel steps (now in OrderSidebar Livewire component) --}}
    {{-- Keep this comment block to mark where old content was (lines replaced by sidebar) --}}
    @if(false) {{-- BEGIN old right panel (disabled) --}}
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
                        @php $pathKey = $item['production_path_key'] ?? 'none'; @endphp
                        @if($pathKey !== 'none')
                        <span class="path-badge">{{ \App\Models\OrderItem::PATH_LABELS[$pathKey] ?? $pathKey }}</span>
                        @endif
                        <input wire:model.live="items.{{ $i }}.qty" type="number" min="1" class="ci-input">
                        <div class="ci-price">₦{{ number_format((float)($item['subtotal']??0),0) }}</div>
                    </div>

                    {{-- BOM breakdown (collapsible) --}}
                    @if($bomMode !== 'disabled' && (!empty($item['bom']) || !empty($item['bom_removals']) || ($item['production_type'] ?? '') === 'production'))
                    @php
                        $bomCount  = count($item['bom'] ?? []);
                        $remCount  = count($item['bom_removals'] ?? []);
                        $bomTotal  = collect($item['bom'] ?? [])->sum('line_total');
                        $bomInitOpen = ($addBomItemIndex === $i) ? 'true' : 'false';
                    @endphp
                    <div x-data="{ open: {{ $bomInitOpen }} }">
                        {{-- Toggle chip --}}
                        <button type="button" @click="open = !open" class="ci-bom-toggle">
                            <span>{{ $bomCount > 0 ? $bomCount.' material'.($bomCount>1?'s':'') : 'Materials' }}</span>
                            @if($remCount > 0)<span class="ci-bom-removed-chip">{{ $remCount }} removed</span>@endif
                            @if($bomTotal > 0)<span class="ci-bom-toggle-cost">₦{{ number_format($bomTotal,0) }}</span>@endif
                            <span x-text="open ? '▲' : '▼'" class="ci-bom-toggle-arrow"></span>
                        </button>
                        {{-- Expanded content --}}
                        <div x-show="open" x-transition.duration.150ms class="ci-bom">
                            @foreach($item['bom'] as $bi => $bline)
                            <div class="ci-bom-row">
                                <span class="ci-bom-name">{{ $bline['name'] }}
                                    <span class="ci-bom-qty">× {{ $bline['quantity'] }}{{ $bline['unit'] ? ' '.$bline['unit'] : '' }}</span>
                                </span>
                                <span class="ci-bom-price">
                                    @if($bline['line_total'] > 0)₦{{ number_format($bline['line_total'], 0) }}@else—@endif
                                </span>
                                @if($bomMode === 'full' || $bomMode === 'remove_only')
                                <button type="button"
                                    wire:click="openRemoveBomModal({{ $i }}, {{ $bi }})"
                                    class="ci-bom-rm" title="Remove material">✕</button>
                                @endif
                            </div>
                            @endforeach
                            @if($bomTotal > 0)
                            <div class="ci-bom-total">Material cost: ₦{{ number_format($bomTotal, 0) }}</div>
                            @endif
                            @foreach($item['bom_removals'] ?? [] as $removal)
                            <div class="ci-bom-removed">
                                <span class="ci-bom-removed-label">⊗ Removed:</span>
                                <span class="ci-bom-removed-name">{{ $removal['name'] }} × {{ $removal['quantity'] }}{{ $removal['unit'] ? ' '.$removal['unit'] : '' }}</span>
                                <span class="ci-bom-removed-reason">Reason: {{ $removal['reason'] }}</span>
                                <span class="ci-bom-removed-meta">— {{ $removal['removed_by'] }}, {{ $removal['removed_at'] }}</span>
                            </div>
                            @endforeach

                            {{-- Add material inline --}}
                            @if($bomMode === 'full')
                            @if($addBomItemIndex === $i)
                            <div class="ci-bom-add-form">
                                <div class="bom-add-search">
                                    <input wire:model.live="addBomSearch"
                                           type="text"
                                           class="bom-add-input"
                                           placeholder="Search or type material name…"
                                           autocomplete="off">
                                    @if(count($addBomResults) > 0)
                                    <div class="bom-add-dropdown">
                                        @foreach($addBomResults as $r)
                                        <button type="button"
                                                wire:click="selectBomResult({{ $r['id'] }})"
                                                class="bom-add-result">
                                            <span>{{ $r['name'] }}</span>
                                            @if($r['unit_price'] > 0)
                                            <span class="bom-add-result-price">₦{{ number_format($r['unit_price'], 0) }}{{ $r['unit'] ? '/'.$r['unit'] : '' }}</span>
                                            @endif
                                        </button>
                                        @endforeach
                                    </div>
                                    @endif
                                    @error('addBomSearch')<p class="bom-add-err">{{ $message }}</p>@enderror
                                </div>
                                <div class="bom-add-fields">
                                    <input wire:model="addBomQty"
                                           type="number" min="0.001" step="0.001"
                                           class="bom-add-field" placeholder="Qty">
                                    <input wire:model="addBomUnit"
                                           type="text"
                                           class="bom-add-field" placeholder="Unit">
                                    <input wire:model="addBomUnitPrice"
                                           type="number" min="0" step="0.01"
                                           class="bom-add-field" placeholder="₦ Price">
                                </div>
                                @error('addBomQty')<p class="bom-add-err">{{ $message }}</p>@enderror
                                @error('addBomUnitPrice')<p class="bom-add-err">{{ $message }}</p>@enderror
                                <div class="bom-add-actions">
                                    <button type="button" wire:click="cancelAddBom" class="bom-add-cancel">Cancel</button>
                                    <button type="button" wire:click="confirmAddBomLine" class="bom-add-confirm">+ Add</button>
                                </div>
                            </div>
                            @else
                            <button type="button" wire:click="toggleAddBom({{ $i }})" class="ci-bom-add-btn">
                                + Add material
                            </button>
                            @endif
                            @endif {{-- bomMode === 'full' --}}
                        </div>
                    </div>
                    @endif
                </div>
                @else
                @php $pathKey = $item['production_path_key'] ?? 'none'; @endphp
                <div class="manual-row" wire:key="mr-{{ $i }}">
                    <div class="item-top">
                        <input wire:model.live="items.{{ $i }}.description" type="text" placeholder="Description…" class="mi mi-desc">
                        <button wire:click="removeItem({{ $i }})" class="ci-rm" title="Remove">✕</button>
                    </div>
                    <div class="item-bot">
                        <input wire:model.live="items.{{ $i }}.qty" type="number" min="1" step="1" class="mi mi-qty">
                        <input wire:model.live="items.{{ $i }}.unit_price" type="number" min="0" step="0.01" placeholder="₦ Price" class="mi mi-price">
                    </div>
                </div>
                @endif
            @endforeach

            <button wire:click="addItem" class="add-row-btn">+ Add Line</button>

            <div class="cart-meta-sep"></div>
            <span class="plbl">Order Details</span>

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

            {{-- Delivery / Pickup toggle --}}
            <div class="dtype-wrap">
                <button wire:click="$set('deliveryType','pickup')" class="dtype-btn {{ $deliveryType==='pickup'?'active':'' }}">
                    🏪 Walk-in Pickup
                </button>
                <button wire:click="$set('deliveryType','delivery')" class="dtype-btn {{ $deliveryType==='delivery'?'active':'' }}">
                    🚚 Home Delivery
                </button>
            </div>
            @if($deliveryType === 'delivery')
            <div class="pf" style="margin-bottom:.5rem;">
                <input wire:model.live="customerAddress" type="text" placeholder="Delivery address…" class="notes-input">
            </div>
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
            <div style="display:flex;gap:.5rem;margin-top:.75rem;">
                <button wire:click="clearCart"
                        wire:confirm="Clear the entire cart and start over?"
                        class="clear-cart-btn"
                        title="Clear cart">
                    🗑
                </button>
                <button wire:click="processOrder" class="complete-btn" style="margin-top:0;flex:1;">
                    Process →
                </button>
            </div>
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
                <span><strong>{{ $customerName }}</strong>@if($customerPhone) · {{ $customerPhone }}@endif</span>
            </div>
            @if($estimatedCompletionDate)
            <div class="cust-qv" style="margin-top:-.5rem;">
                <span style="font-size:1rem;">📅</span>
                <span>Ready by <strong>{{ \Carbon\Carbon::parse($estimatedCompletionDate)->format('D, d M Y') }}</strong></span>
            </div>
            @endif

            {{-- Quick amounts (in scroll area, not footer) --}}
            @php $remaining = $this->getBalance(); $total = $this->getTotal(); @endphp
            @if($total > 0 && $remaining > 0)
            @php $shown = 0; @endphp
            <div class="quick-amts" style="margin-top:.5rem;padding-bottom:.5rem;">
                <button wire:click="fillRemaining({{ count($splits)-1 }})" class="q-btn">Exact</button>
                @foreach([500,1000,2000,5000,10000,20000,50000,100000,200000,500000] as $q)
                    @if($q > $this->getSplitTotal() && $shown < 2)
                    <button wire:click="$set('splits.{{ count($splits)-1 }}.amount','{{ $q }}')" class="q-btn">₦{{ number_format($q,0) }}</button>
                    @php $shown++ @endphp
                    @endif
                @endforeach
            </div>
            @endif

        </div>{{-- /cart-scroll --}}

        <div class="cart-foot">

            {{-- ── Split payment rows ── --}}
            <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:.5rem;">
                @foreach($splits as $i => $split)
                <div style="display:flex;align-items:center;gap:.4rem;">

                    {{-- Method pills --}}
                    <div style="display:flex;gap:.25rem;flex-shrink:0;">
                        @foreach(['cash'=>'Cash','transfer'=>'Transfer','pos'=>'POS'] as $m=>$ml)
                        <button wire:click="$set('splits.{{ $i }}.method','{{ $m }}')"
                            class="pm-btn {{ ($split['method']===$m)?'active':'' }}"
                            style="padding:.3rem .45rem;font-size:.65rem;">{{ $ml }}</button>
                        @endforeach
                    </div>

                    {{-- Amount --}}
                    <div class="amount-wrap" style="flex:1;margin:0;">
                        <span class="amount-prefix">₦</span>
                        <input wire:model.live="splits.{{ $i }}.amount"
                               type="number" min="0" step="0.01" placeholder="0.00"
                               class="amount-input" style="font-size:.95rem;"
                               {{ $i === 0 ? 'autofocus' : '' }}>
                    </div>

                    {{-- Fill remaining --}}
                    @if($this->getBalance() > 0)
                    <button wire:click="fillRemaining({{ $i }})"
                        title="Fill remaining ₦{{ number_format($this->getBalance(),0) }}"
                        style="flex-shrink:0;padding:.35rem .5rem;border:1px solid var(--border);border-radius:6px;background:transparent;cursor:pointer;font-size:.7rem;color:var(--text3);">
                        +Rem
                    </button>
                    @endif

                    {{-- Remove row --}}
                    @if(count($splits) > 1)
                    <button wire:click="removeSplit({{ $i }})"
                        style="flex-shrink:0;width:24px;height:24px;border-radius:50%;border:1px solid var(--border);background:transparent;cursor:pointer;color:var(--text3);font-size:.85rem;display:flex;align-items:center;justify-content:center;">×</button>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Add split row --}}
            @if(count($splits) < 3)
            <button wire:click="addSplit"
                style="width:100%;padding:.35rem;border:1px dashed var(--border);border-radius:6px;background:transparent;cursor:pointer;font-size:.75rem;color:var(--text3);margin-bottom:.5rem;">
                + Add another payment method
            </button>
            @endif

            {{-- Summary / change --}}
            @if($this->getSplitTotal() > 0)
                @if(count($splits) > 1)
                <div style="font-size:.72rem;color:var(--text3);text-align:right;margin-bottom:.25rem;">
                    Total tendered: <strong>₦{{ number_format($this->getSplitTotal(),0) }}</strong>
                </div>
                @endif
                @if($this->getChange() > 0)
                <div class="change-box change">
                    <span class="chg-lbl">Change Due</span>
                    <span class="chg-amt">₦{{ number_format($this->getChange(),2) }}</span>
                </div>
                @elseif($this->getBalance() > 0)
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

            {{-- Deposit policy hint --}}
            @php
                $posPolicy  = \App\Models\AppSetting::get('payment_policy', 'half_upfront');
                $posPct     = max(1, (int) \App\Models\AppSetting::get('deposit_percent', 50));
                $posMin     = $this->getTotal() * ($posPct / 100);
                $posPaid    = $this->getSplitTotal();
                $posShortfall = max(0, $posMin - $posPaid);
            @endphp
            @if($posPolicy === 'half_upfront')
                @if($posShortfall > 0)
                <div style="font-size:.72rem;color:#b45309;background:#fef3c7;border:1px solid #fde68a;
                            border-radius:6px;padding:.4rem .65rem;margin-bottom:.4rem;text-align:center;">
                    Min. deposit: ₦{{ number_format($posMin, 0) }} ({{ $posPct }}%) — still need ₦{{ number_format($posShortfall, 0) }}
                </div>
                @else
                <div style="font-size:.72rem;color:#166534;background:#f0fdf4;border:1px solid #bbf7d0;
                            border-radius:6px;padding:.4rem .65rem;margin-bottom:.4rem;text-align:center;">
                    ✓ Deposit met ({{ $posPct }}% minimum)
                </div>
                @endif
            @else
            <div style="font-size:.72rem;color:var(--text3);text-align:center;margin-bottom:.4rem;">
                Pay later policy — no deposit required
            </div>
            @endif

            <button wire:click="completeSale" wire:loading.attr="disabled" class="complete-btn">
                <span wire:loading.remove wire:target="completeSale">✓ Complete Sale</span>
                <span wire:loading wire:target="completeSale" class="btn-loading">
                    <span class="pos-spin"></span> Processing…
                </span>
            </button>

        </div>{{-- /cart-foot --}}

        @endif{{-- posStep --}}

    @endif {{-- END old right panel (disabled) --}}

</div>{{-- /pos-shell --}}

{{-- ═══════════════ VARIANT PICKER MODAL ═══════════════ --}}
@if($showVariantModal)
@php $vprod = \App\Models\Product::with(['variants' => fn($q) => $q->where('is_active', true)->orderBy('variant_type')])->find($variantModalProductId); @endphp
@if($vprod)
<div class="modal-backdrop" wire:click.self="closeVariantModal">
    <div class="modal-box" style="max-width:380px;">
        <div class="modal-head">
            <span class="modal-title">{{ $vprod->name }}</span>
            <button wire:click="closeVariantModal" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
            <p style="font-size:.8rem;color:var(--muted);margin-bottom:.85rem;">Select a variant to add to the order.</p>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach($vprod->variants as $v)
                <label style="display:flex;align-items:center;gap:.65rem;padding:.6rem .75rem;border:1px solid var(--border);border-radius:8px;cursor:pointer;{{ $variantModalSelectedId == $v->id ? 'border-color:var(--gold);background:rgba(201,168,76,.08);' : '' }}">
                    <input type="radio" wire:model.live="variantModalSelectedId" value="{{ $v->id }}" style="accent-color:var(--gold);">
                    @if($v->image)
                    <img src="{{ asset('storage/' . $v->image) }}"
                         alt="{{ $v->variant_value }}"
                         title="Click to expand"
                         onclick="event.preventDefault();showVariantImage('{{ asset('storage/' . $v->image) }}','{{ addslashes(ucfirst($v->variant_type).': '.$v->variant_value) }}')"
                         style="width:36px;height:36px;object-fit:cover;border-radius:5px;flex-shrink:0;border:1px solid var(--border);cursor:zoom-in;">
                    @endif
                    <span style="flex:1;font-size:.88rem;font-weight:600;color:var(--text);">
                        {{ ucfirst($v->variant_type) }}: {{ $v->variant_value }}
                    </span>
                    @if($v->price_adjustment != 0)
                    <span style="font-size:.8rem;font-weight:700;color:{{ $v->price_adjustment > 0 ? '#059669' : '#dc2626' }};">
                        {{ $v->price_adjustment > 0 ? '+' : '' }}₦{{ number_format($v->price_adjustment, 0) }}
                    </span>
                    @endif
                </label>
                @endforeach
            </div>
        </div>
        <div class="modal-foot">
            <button wire:click="closeVariantModal" class="mbtn mbtn-ghost">Cancel</button>
            <button wire:click="confirmVariantSelection" class="mbtn mbtn-primary" {{ ! $variantModalSelectedId ? 'disabled' : '' }}>
                Add to Order
            </button>
        </div>
    </div>
</div>
@endif
@endif

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
            </div>
            @if($deliveryType==='delivery')
            <div class="pf" style="margin-bottom:.55rem;">
                <label>Delivery Address</label>
                <input wire:model.live="customerAddress" type="text" placeholder="Full delivery address">
            </div>
            @endif

            @if($this->hasProductionItems())
            <div class="pf" style="margin-bottom:.55rem;">
                <label style="display:flex;align-items:center;justify-content:space-between;">
                    <span>Est. Completion Date</span>
                    <span style="font-size:.65rem;font-weight:400;color:var(--muted);">Prefilled from product — override if needed</span>
                </label>
                <input wire:model.live="estimatedCompletionDate" type="date"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    style="width:100%;padding:.45rem .65rem;border:1px solid var(--border);border-radius:7px;background:var(--bg2);color:var(--text);font-size:.88rem;">
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
                {{-- 2: Measurements (conditional) --}}
                @if($hasFields)
                <div class="step-dot {{ $modalStep>=2?($modalStep>2?'done':'active'):'' }}">{{ $modalStep>2?'✓':'2' }}</div>
                <div class="step-line {{ $modalStep>2?'done':'' }}"></div>
                @endif
                {{-- 3: Design --}}
                <div class="step-dot {{ $modalStep>=3?($modalStep>3?'done':'active'):'' }}">{{ $modalStep>3?'✓':($hasFields?'3':'2') }}</div>
                <div class="step-line {{ $modalStep>3?'done':'' }}"></div>
                {{-- 4: Confirm --}}
                <div class="step-dot {{ $modalStep===4?'active':'' }}">{{ $hasFields?'4':'3' }}</div>
            </div>

            {{-- STEP 1: Customer --}}
            @if ($modalStep === 1)
            @if($mprod->variants->isNotEmpty())
            <div class="mfield" style="margin-bottom:.85rem;">
                <label style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);">Variant</label>
                <div style="display:flex;flex-direction:column;gap:.4rem;margin-top:.4rem;">
                    <label style="display:flex;align-items:center;gap:.6rem;padding:.5rem .65rem;border:1px solid {{ $modalVariantId == '' ? 'var(--gold)' : 'var(--border)' }};border-radius:7px;cursor:pointer;{{ $modalVariantId == '' ? 'background:rgba(201,168,76,.06);' : '' }}">
                        <input type="radio" wire:model.live="modalVariantId" value="" style="accent-color:var(--gold);">
                        <span style="font-size:.85rem;color:var(--muted);">No specific variant</span>
                    </label>
                    @foreach($mprod->variants->where('is_active', true) as $variant)
                    <label style="display:flex;align-items:center;gap:.6rem;padding:.5rem .65rem;border:1px solid {{ $modalVariantId == $variant->id ? 'var(--gold)' : 'var(--border)' }};border-radius:7px;cursor:pointer;{{ $modalVariantId == $variant->id ? 'background:rgba(201,168,76,.06);' : '' }}">
                        <input type="radio" wire:model.live="modalVariantId" value="{{ $variant->id }}" style="accent-color:var(--gold);">
                        @if($variant->image)
                        <img src="{{ asset('storage/' . $variant->image) }}"
                             alt="{{ $variant->variant_value }}"
                             title="Click to expand"
                             onclick="event.preventDefault();showVariantImage('{{ asset('storage/' . $variant->image) }}','{{ addslashes(ucfirst($variant->variant_type).': '.$variant->variant_value) }}')"
                             style="width:32px;height:32px;object-fit:cover;border-radius:4px;flex-shrink:0;border:1px solid var(--border);cursor:zoom-in;">
                        @endif
                        <span style="flex:1;font-size:.85rem;font-weight:600;color:var(--text);">
                            {{ ucfirst($variant->variant_type) }}: {{ $variant->variant_value }}
                        </span>
                        @if($variant->price_adjustment != 0)
                        <span style="font-size:.78rem;font-weight:700;color:{{ $variant->price_adjustment > 0 ? '#059669' : '#dc2626' }};">
                            {{ $variant->price_adjustment > 0 ? '+' : '' }}₦{{ number_format($variant->price_adjustment, 0) }}
                        </span>
                        @endif
                    </label>
                    @endforeach
                </div>
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

            {{-- STEP 2: Measurements --}}
            @if ($modalStep === 2 && $mprod->measurementTemplate && !empty($mprod->measurementTemplate->fields))
            @php
                $fieldIds    = $mprod->measurementTemplate->fields ?? [];
                $measFields  = \App\Models\MeasurementField::whereIn('id', $fieldIds)->orderBy('label')->get()->keyBy('id');
                $savedSets   = $this->getCustomerSavedMeasurements();
            @endphp

            @php
                $bodyMeasure = $this->getCustomerBodyMeasurement();
                $hasAnyMeasurements = $savedSets->isNotEmpty() || $bodyMeasure;
            @endphp
            @if($hasAnyMeasurements)
            <div x-data="{ open: false, search: '' }" style="position:relative;margin-bottom:1rem;">
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.35rem;">
                    Load from saved measurements
                </div>
                {{-- Trigger --}}
                <div @click="open=!open" style="display:flex;align-items:center;justify-content:space-between;padding:.45rem .7rem;border:1px solid var(--border);border-radius:7px;background:var(--bg2);cursor:pointer;user-select:none;">
                    <span style="font-size:.82rem;color:var(--text3);">Select a measurement set…</span>
                    <span :style="open ? 'transform:rotate(180deg);display:inline-block' : 'display:inline-block'" style="font-size:.75rem;color:var(--text3);line-height:1;transition:transform .15s;flex-shrink:0;">&#x25BE;</span>
                </div>
                {{-- Dropdown --}}
                <div x-show="open" @click.outside="open=false" x-transition
                    style="position:absolute;z-index:50;left:0;right:0;top:calc(100% + 4px);background:var(--bg);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.15);overflow:hidden;">
                    {{-- Search --}}
                    <div style="padding:.5rem .6rem;border-bottom:1px solid var(--border);">
                        <input x-model="search" type="text" placeholder="Search…" autofocus
                            style="width:100%;padding:.38rem .55rem;border:1px solid var(--border);border-radius:6px;background:var(--bg2);font-size:.82rem;color:var(--text);font-family:inherit;outline:none;">
                    </div>
                    <div style="max-height:220px;overflow-y:auto;">
                        {{-- Default body measurement profile (pinned at top) --}}
                        @if($bodyMeasure)
                        <div
                            x-show="search==='' || 'default measurements'.includes(search.toLowerCase())"
                            wire:click="loadFromBodyMeasurement"
                            @click="open=false; search=''"
                            style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;cursor:pointer;font-size:.83rem;color:var(--text);gap:.5rem;border-bottom:1px solid var(--border);"
                            onmouseover="this.style.background='var(--bg2)'" onmouseout="this.style.background=''">
                            <span style="display:flex;align-items:center;gap:.35rem;font-weight:700;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <span>⭐</span> Default Measurements
                            </span>
                            <span style="font-size:.72rem;color:var(--muted);white-space:nowrap;flex-shrink:0;">
                                {{ count($bodyMeasure->measurements ?? []) }} fields · {{ $bodyMeasure->updated_at->format('d M Y') }}
                            </span>
                        </div>
                        @endif
                        {{-- Product-specific saved sets --}}
                        @foreach($savedSets as $ss)
                        @php $productName = $ss->product?->name ?? 'Product #'.$ss->product_id; @endphp
                        <div
                            x-show="search==='' || '{{ strtolower($productName) }}'.includes(search.toLowerCase())"
                            wire:click="loadFromSavedMeasurement({{ $ss->id }})"
                            @click="open=false; search=''"
                            style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;cursor:pointer;font-size:.83rem;color:var(--text);gap:.5rem;"
                            onmouseover="this.style.background='var(--bg2)'" onmouseout="this.style.background=''">
                            <span style="font-weight:600;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $productName }}</span>
                            <span style="font-size:.72rem;color:var(--muted);white-space:nowrap;flex-shrink:0;">{{ count($ss->measurements) }} fields · {{ $ss->updated_at->format('d M Y') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <p class="modal-desc">Enter measurements for <strong>{{ $modalCustomerName }}</strong>.</p>
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

            {{-- STEP 3: Design --}}
            @if ($modalStep === 3)
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
                    placeholder="E.g. Blue &amp; white, chest placement, 10cm wide…"
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

            {{-- STEP 4: Confirm --}}
            @if ($modalStep === 4)
            <p class="modal-desc">
                @if($bomMode !== 'disabled')Review materials and washing requirements before adding to cart.
                @else Review washing requirements before adding to cart.
                @endif
            </p>

            @if($bomMode !== 'disabled')
            @if(count($modalBom) > 0 || count($modalBomRemovals) > 0)
            <span class="plbl" style="margin-bottom:.4rem; display:block;">Materials Needed (BOM)</span>
            @foreach($modalBom as $bi => $bm)
            <div class="bom-item {{ ($showModalBomRemove && $modalBomRemoveIndex === $bi) ? 'bom-item--pending' : '' }}">
                <span class="bom-name">{{ $bm['name'] }}</span>
                <input wire:model.live="modalBom.{{ $bi }}.quantity" type="number" step="0.1" min="0" class="bom-qty-input"
                    {{ (($bomMode !== 'full' && $bomMode !== 'remove_only') || ($showModalBomRemove && $modalBomRemoveIndex === $bi)) ? 'disabled' : '' }}>
                <span class="bom-unit">{{ $bm['unit'] }}</span>
                @if($bomMode === 'full' || $bomMode === 'remove_only')
                <button type="button" wire:click="openModalBomRemove({{ $bi }})"
                    class="bom-rm-btn" title="Remove material">✕</button>
                @endif
            </div>
            @endforeach

            {{-- Inline reason form (full mode only) --}}
            @if(($bomMode === 'full' || $bomMode === 'remove_only') && $showModalBomRemove && isset($modalBom[$modalBomRemoveIndex]))
            @php $pendingBm = $modalBom[$modalBomRemoveIndex]; @endphp
            <div class="bom-remove-inline">
                <p class="bom-remove-inline-title">Removing <strong>{{ $pendingBm['name'] }}</strong> — enter a reason:</p>
                <textarea wire:model.live="modalBomRemoveReason"
                    placeholder="e.g. Customer requested substitution, out of stock…"
                    rows="2" class="bom-remove-inline-input"></textarea>
                @error('modalBomRemoveReason')<span class="bom-remove-inline-err">{{ $message }}</span>@enderror
                <div class="bom-remove-inline-actions">
                    <button type="button" wire:click="cancelModalBomRemove" class="mbtn mbtn-ghost" style="font-size:.75rem;padding:.3rem .7rem;">Cancel</button>
                    <button type="button" wire:click="confirmModalBomRemove" class="mbtn" style="font-size:.75rem;padding:.3rem .7rem;background:#dc2626;color:#fff;">Confirm Remove</button>
                </div>
            </div>
            @endif

            @foreach($modalBomRemovals as $removal)
            <div class="bom-removed-note">
                <span class="bom-removed-note-icon">⊗</span>
                <span class="bom-removed-note-name">{{ $removal['name'] }} × {{ $removal['quantity'] }}{{ $removal['unit'] ? ' '.$removal['unit'] : '' }}</span>
                <span class="bom-removed-note-reason">Reason: {{ $removal['reason'] }}</span>
                <span class="bom-removed-note-meta">{{ $removal['removed_by'] }}, {{ $removal['removed_at'] }}</span>
            </div>
            @endforeach
            @else
            <p class="modal-desc" style="margin-bottom:.75rem;">No BOM defined for this product.</p>
            @endif

            {{-- Add material to modal BOM (full mode only) --}}
            @if($bomMode === 'full')
            @if($modalAddBomOpen)
            <div class="bom-add-form" style="margin-top:.5rem;">
                <div class="bom-add-search">
                    <input wire:model.live="modalAddBomSearch" type="text"
                        placeholder="Search materials…" class="bom-add-input" autocomplete="off">
                    @if(count($modalAddBomResults))
                    <div class="bom-add-dropdown">
                        @foreach($modalAddBomResults as $r)
                        <button type="button" wire:click="selectModalBomResult({{ $r['id'] }})" class="bom-add-result">
                            <span>{{ $r['name'] }}</span>
                            <span class="bom-add-result-price">{{ $r['unit'] ? $r['unit'].' · ' : '' }}₦{{ number_format($r['price'],0) }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="bom-add-fields">
                    <input wire:model.live="modalAddBomQty" type="number" step="0.01" min="0"
                        placeholder="Qty" class="bom-add-field">
                    <input wire:model.live="modalAddBomUnit" type="text"
                        placeholder="Unit" class="bom-add-field">
                    <input wire:model.live="modalAddBomUnitPrice" type="number" step="0.01" min="0"
                        placeholder="Unit price ₦" class="bom-add-field">
                </div>
                @error('modalAddBomSearch')<p class="bom-add-err">{{ $message }}</p>@enderror
                @error('modalAddBomQty')<p class="bom-add-err">{{ $message }}</p>@enderror
                @error('modalAddBomUnitPrice')<p class="bom-add-err">{{ $message }}</p>@enderror
                <div class="bom-add-actions">
                    <button type="button" wire:click="cancelModalAddBom" class="bom-add-cancel">Cancel</button>
                    <button type="button" wire:click="confirmModalAddBomLine" class="bom-add-confirm">+ Add</button>
                </div>
            </div>
            @else
            <button type="button" wire:click="toggleModalAddBom" class="ci-bom-add-btn" style="margin-top:.4rem;">+ Add material</button>
            @endif
            @endif {{-- bomMode === 'full' --}}
            @endif {{-- bomMode !== 'disabled' --}}

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

            {{-- Fulfillment --}}
            <div class="wash-section">
                <div class="wash-lbl" style="cursor:default;margin-bottom:.6rem;">Fulfillment</div>
                <div style="display:flex;gap:6px;">
                    <button type="button"
                            wire:click="$set('modalDeliveryType','pickup')"
                            style="flex:1;padding:7px 4px;border-radius:6px;font-size:.78rem;cursor:pointer;transition:all .15s;
                                   {{ $modalDeliveryType==='pickup' ? 'background:#1a1a18;color:#fff;border:1px solid #1a1a18;font-weight:500;' : 'background:transparent;color:var(--muted);border:1px solid var(--border);' }}">
                        Pickup
                    </button>
                    <button type="button"
                            wire:click="setModalDeliveryType('delivery')"
                            style="flex:1;padding:7px 4px;border-radius:6px;font-size:.78rem;cursor:pointer;transition:all .15s;
                                   {{ $modalDeliveryType==='delivery' ? 'background:#1a1a18;color:#fff;border:1px solid #1a1a18;font-weight:500;' : 'background:transparent;color:var(--muted);border:1px solid var(--border);' }}">
                        Home Delivery
                    </button>
                </div>
                @if($modalDeliveryType === 'delivery')
                <div class="mfield" style="margin-top:.75rem;">
                    <label>Delivery Address</label>
                    <input wire:model.live="modalDeliveryAddress"
                           type="text"
                           placeholder="Enter delivery address…"
                           style="width:100%;">
                </div>
                @endif
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

{{-- ═══════════════ VARIANT IMAGE LIGHTBOX ═══════════════ --}}
<div id="varImgOverlay"
     onclick="closeVariantImage()"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.82);
            align-items:center;justify-content:center;flex-direction:column;gap:1rem;padding:1.5rem;">
    <img id="varImgFull" src="" alt=""
         style="max-width:90vw;max-height:78vh;object-fit:contain;border-radius:10px;box-shadow:0 8px 40px rgba(0,0,0,.6);">
    <div id="varImgLabel"
         style="color:#fff;font-size:1rem;font-weight:600;letter-spacing:.04em;text-shadow:0 1px 4px rgba(0,0,0,.5);"></div>
    <div style="color:rgba(255,255,255,.55);font-size:.78rem;">Tap anywhere to close</div>
</div>

<script>
function showVariantImage(src, label) {
    const overlay = document.getElementById('varImgOverlay');
    document.getElementById('varImgFull').src = src;
    document.getElementById('varImgLabel').textContent = label;
    overlay.style.display = 'flex';
    document.addEventListener('keydown', _varImgEsc);
}
function closeVariantImage() {
    document.getElementById('varImgOverlay').style.display = 'none';
    document.getElementById('varImgFull').src = '';
    document.removeEventListener('keydown', _varImgEsc);
}
function _varImgEsc(e) { if (e.key === 'Escape') closeVariantImage(); }
</script>

@if($showRemoveBomModal)
@php
    $rmItem = $items[$removeBomItemIndex] ?? null;
    $rmLine = $rmItem['bom'][$removeBomBomIndex] ?? null;
@endphp
<div class="modal-backdrop" wire:click.self="cancelRemoveBom">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-head">
            <span class="modal-title">Remove Material</span>
            <button wire:click="cancelRemoveBom" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
            @if($rmLine)
            <div class="rm-bom-material">
                Removing <strong>{{ $rmLine['name'] }}</strong>
                × {{ $rmLine['quantity'] }}{{ $rmLine['unit'] ? ' '.$rmLine['unit'] : '' }}
                from <em>{{ $rmItem['description'] ?? '' }}</em>
            </div>
            @endif
            <div class="rm-bom-field">
                <label for="rm-bom-reason">Reason for removal <span style="color:#ef4444">*</span></label>
                <textarea id="rm-bom-reason" wire:model.live="removeBomReason"
                    placeholder="e.g. Customer requested substitution, out of stock…"
                    rows="3"></textarea>
                @error('removeBomReason')<span style="font-size:.75rem;color:#ef4444">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="modal-foot">
            <button wire:click="cancelRemoveBom" class="mbtn mbtn-ghost">Cancel</button>
            <button wire:click="confirmRemoveBomLine" class="mbtn"
                style="background:#dc2626;color:#fff;">
                Remove Material
            </button>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>
