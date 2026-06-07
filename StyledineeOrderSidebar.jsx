import { useState } from "react";

const GOLD = "#C9A84C";
const DARK = "#1a1a18";
const MUTED = "#9a9690";
const BORDER = "#ece9e3";
const BORDER_LIGHT = "#f0ede8";
const SERIF = "'Cormorant Garamond', Georgia, serif";

const CUSTOMER = {
  name: "Osmund Peter",
  phone: "+2348158210767",
  initials: "OP",
  affiliate: "Sarah K.",
  balance: 3500,
};
const COUPONS = {
  "SAVE10":  { pct: 0.10, label: "10% off" },
  "WELCOME": { pct: 0.15, label: "15% off" },
};
const initItems = [
  { id: 1, name: "Pant",         qty: 1, unitPrice: 9000 },
  { id: 2, name: "Shoulder Pad", qty: 2, unitPrice: 700  },
  { id: 3, name: "Interfacing",  qty: 1, unitPrice: 800  },
];

// ── Shared style atoms ────────────────────────────────────────
const CHIP      = { fontSize: "10px", letterSpacing: "0.12em", color: MUTED, textTransform: "uppercase" };
const avatarSt  = { width: "26px", height: "26px", borderRadius: "50%", background: DARK, display: "flex", alignItems: "center", justifyContent: "center", fontSize: "9px", color: GOLD, fontWeight: 600, flexShrink: 0 };
const qtyBtnSt  = { width: "22px", height: "22px", borderRadius: "50%", border: "1px solid #d8d4cd", background: "none", cursor: "pointer", fontSize: "13px", color: "#7a7671", display: "flex", alignItems: "center", justifyContent: "center", lineHeight: 1, padding: 0, flexShrink: 0 };
const goldBtnSt = { width: "100%", padding: "13px", background: GOLD, border: "none", borderRadius: "8px", fontSize: "14px", fontWeight: 500, color: DARK, cursor: "pointer", letterSpacing: "0.02em" };
const ghostBtnSt = { padding: "13px 18px", background: "none", border: `1px solid ${BORDER}`, borderRadius: "8px", fontSize: "13px", color: MUTED, cursor: "pointer", whiteSpace: "nowrap" };
const amtInputSt = { flex: 1, border: "none", borderBottom: "1px solid #e0dbd3", padding: "6px 0", fontSize: "22px", fontWeight: 500, color: DARK, background: "transparent", outline: "none", width: "100%", fontFamily: SERIF };

// ── Layout shells ─────────────────────────────────────────────
function Shell({ children }) {
  return (
    <div style={{ width: "300px", background: "#fff", borderLeft: `1px solid ${BORDER}`, display: "flex", flexDirection: "column", height: "100vh", fontFamily: "'DM Sans', sans-serif", position: "relative" }}>
      {children}
    </div>
  );
}
function StepHeader({ onBack, title }) {
  return (
    <div style={{ padding: "16px 20px", display: "flex", alignItems: "center", justifyContent: "space-between", borderBottom: `1px solid ${BORDER}`, minHeight: "52px", boxSizing: "border-box", position: "relative" }}>
      {onBack
        ? <button onClick={onBack} style={{ border: "none", background: "none", cursor: "pointer", fontSize: "20px", color: DARK, padding: 0, lineHeight: 1 }}>←</button>
        : <span />}
      <span style={{ ...CHIP, position: "absolute", left: "50%", transform: "translateX(-50%)" }}>{title}</span>
      <span />
    </div>
  );
}
function Footer({ children }) {
  return <div style={{ padding: "14px 20px 22px", borderTop: `1px solid ${BORDER}` }}>{children}</div>;
}
function CustomerRow({ showAffiliate = true }) {
  return (
    <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
      <div style={avatarSt}>{CUSTOMER.initials}</div>
      <div>
        <div style={{ display: "flex", alignItems: "center", gap: "5px" }}>
          <span style={{ fontSize: "13px", color: DARK }}>{CUSTOMER.name}</span>
          {showAffiliate && CUSTOMER.affiliate && (
            <span style={{ fontSize: "10px", color: MUTED, background: "#f5f3ef", padding: "1px 5px", borderRadius: "4px" }}>
              via {CUSTOMER.affiliate}
            </span>
          )}
        </div>
      </div>
    </div>
  );
}

// ── Main component ────────────────────────────────────────────
export default function OrderSidebar() {
  const [items,         setItems]         = useState(initItems);
  const [step,          setStep]          = useState("order");
  const [orderTab,      setOrderTab]      = useState("items");   // "items" | "delivery"
  const [payMode,       setPayMode]       = useState("cash");
  const [cashAmt,       setCashAmt]       = useState("");
  const [transferAmt,   setTransferAmt]   = useState("");
  const [deliveryDate,  setDeliveryDate]  = useState("");
  const [couponInput,   setCouponInput]   = useState("");
  const [couponError,   setCouponError]   = useState("");
  const [appliedCoupon, setAppliedCoupon] = useState(null);

  const subtotal    = items.reduce((s, i) => s + i.qty * i.unitPrice, 0);
  const discountAmt = appliedCoupon ? Math.round(subtotal * appliedCoupon.pct) : 0;
  const total       = subtotal - discountAmt;

  const cashNum     = parseFloat(cashAmt)     || 0;
  const transferNum = parseFloat(transferAmt) || 0;
  const totalPaid   = payMode === "cash" ? cashNum : payMode === "transfer" ? total : cashNum + transferNum;
  const change      = Math.max(0, totalPaid - total);
  const remaining   = payMode === "split" ? Math.max(0, total - cashNum - transferNum) : 0;
  const isValid     = payMode === "cash" ? cashNum >= total : payMode === "transfer" ? true : cashNum + transferNum >= total;

  const changeQty  = (id, d) => setItems(p => p.map(i => i.id === id ? { ...i, qty: Math.max(1, i.qty + d) } : i));
  const removeItem = (id)    => setItems(p => p.filter(i => i.id !== id));
  const clearAll   = ()      => { setItems([]); setDeliveryDate(""); };

  const applyCoupon = () => {
    const code = couponInput.trim().toUpperCase();
    if (COUPONS[code]) { setAppliedCoupon({ code, ...COUPONS[code] }); setCouponError(""); }
    else setCouponError("Invalid code");
  };
  const removeCoupon = () => { setAppliedCoupon(null); setCouponInput(""); setCouponError(""); };

  const handleCashInput = (v) => {
    setCashAmt(v);
    if (payMode === "split") {
      const rem = Math.max(0, total - (parseFloat(v) || 0));
      setTransferAmt(rem ? String(rem) : "");
    }
  };
  const switchPayMode = (m) => { setPayMode(m); setCashAmt(""); setTransferAmt(""); };
  const resetAll = () => {
    setItems(initItems); setStep("order"); setOrderTab("items");
    setCashAmt(""); setTransferAmt(""); setPayMode("cash");
    setAppliedCoupon(null); setCouponInput(""); setCouponError(""); setDeliveryDate("");
  };

  // Tab button style helper
  const tabSt = (active) => ({
    flex: 1, padding: "10px 0", border: "none",
    borderBottom: active ? `2px solid ${DARK}` : "2px solid transparent",
    background: "none", fontSize: "12px",
    color: active ? DARK : MUTED, fontWeight: active ? 500 : 400,
    cursor: "pointer", transition: "color 0.15s",
  });

  // ── ORDER ────────────────────────────────────────────────────
  if (step === "order") return (
    <Shell>
      {/* Header */}
      <div style={{ padding: "16px 20px", display: "flex", justifyContent: "space-between", alignItems: "center", borderBottom: `1px solid ${BORDER}` }}>
        <span style={CHIP}>Order</span>
        <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
          <CustomerRow />
          <span style={{ fontSize: "11px", color: MUTED, cursor: "pointer", textDecoration: "underline", textUnderlineOffset: "3px" }}>edit</span>
        </div>
      </div>

      {/* Tabs */}
      <div style={{ display: "flex", borderBottom: `1px solid ${BORDER}` }}>
        <button style={tabSt(orderTab === "items")}    onClick={() => setOrderTab("items")}>Items</button>
        <button style={tabSt(orderTab === "delivery")} onClick={() => setOrderTab("delivery")}>
          Delivery {deliveryDate ? "·" : ""}
          {deliveryDate && <span style={{ color: GOLD, marginLeft: "3px" }}>●</span>}
        </button>
      </div>

      {/* Tab: Items */}
      {orderTab === "items" && (
        <div style={{ flex: 1, overflowY: "auto", paddingTop: "4px" }}>
          {items.length === 0
            ? <div style={{ padding: "40px 20px", textAlign: "center", color: "#b0aca6", fontSize: "13px" }}>No items yet</div>
            : items.map((item, i) => (
              <div key={item.id} style={{ display: "flex", alignItems: "center", padding: "11px 20px", gap: "10px", borderBottom: i < items.length - 1 ? `1px solid ${BORDER_LIGHT}` : "none" }}>
                <span style={{ flex: 1, fontSize: "13px", color: DARK }}>{item.name}</span>
                <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
                  <button style={qtyBtnSt} onClick={() => changeQty(item.id, -1)}>−</button>
                  <span style={{ fontSize: "13px", minWidth: "16px", textAlign: "center", color: DARK }}>{item.qty}</span>
                  <button style={qtyBtnSt} onClick={() => changeQty(item.id, 1)}>+</button>
                </div>
                <span style={{ fontSize: "13px", minWidth: "62px", textAlign: "right", color: DARK }}>
                  ₦{(item.qty * item.unitPrice).toLocaleString()}
                </span>
                <button onClick={() => removeItem(item.id)} aria-label={`Remove ${item.name}`}
                  style={{ border: "none", background: "none", cursor: "pointer", color: "#ccc", padding: 0, fontSize: "16px", lineHeight: 1, flexShrink: 0 }}>×</button>
              </div>
            ))
          }
          <div style={{ padding: "10px 20px" }}>
            <button style={{ border: "none", background: "none", cursor: "pointer", fontSize: "12px", color: MUTED, padding: 0 }}>+ Add line</button>
          </div>
        </div>
      )}

      {/* Tab: Delivery */}
      {orderTab === "delivery" && (
        <div style={{ flex: 1, padding: "24px 20px", display: "flex", flexDirection: "column", gap: "6px" }}>
          <div style={{ fontSize: "11px", color: MUTED, letterSpacing: "0.08em", marginBottom: "10px" }}>EXPECTED DELIVERY DATE</div>
          <input
            type="date"
            value={deliveryDate}
            onChange={e => setDeliveryDate(e.target.value)}
            style={{ border: "none", borderBottom: "1px solid #e0dbd3", padding: "8px 0", fontSize: "15px", color: DARK, background: "transparent", outline: "none", width: "100%", fontFamily: "inherit" }}
          />
          {deliveryDate && (
            <div style={{ marginTop: "14px", fontSize: "13px", color: DARK, fontFamily: SERIF, letterSpacing: "0.01em" }}>
              {new Date(deliveryDate + "T00:00").toLocaleDateString("en-NG", { weekday: "long", day: "numeric", month: "long", year: "numeric" })}
            </div>
          )}
        </div>
      )}

      {/* Footer */}
      <Footer>
        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "baseline", marginBottom: "14px" }}>
          <span style={{ fontSize: "12px", color: MUTED }}>Total</span>
          <span style={{ fontSize: "26px", fontWeight: 500, color: DARK, fontFamily: SERIF, letterSpacing: "-0.01em", lineHeight: 1 }}>
            ₦{subtotal.toLocaleString()}
          </span>
        </div>
        <div style={{ display: "flex", gap: "8px" }}>
          <button style={ghostBtnSt} onClick={clearAll}>Clear</button>
          <button style={{ ...goldBtnSt, flex: 1, width: "auto" }} onClick={() => setStep("summary")} disabled={items.length === 0}>
            Process →
          </button>
        </div>
      </Footer>
    </Shell>
  );

  // ── SUMMARY ──────────────────────────────────────────────────
  if (step === "summary") return (
    <Shell>
      <StepHeader onBack={() => setStep("order")} title="Summary" />

      <div style={{ flex: 1, overflowY: "auto" }}>
        {/* Customer */}
        <div style={{ padding: "14px 20px", borderBottom: `1px solid ${BORDER}`, display: "flex", alignItems: "center", gap: "10px" }}>
          <div style={avatarSt}>{CUSTOMER.initials}</div>
          <div>
            <div style={{ display: "flex", alignItems: "center", gap: "6px" }}>
              <span style={{ fontSize: "13px", fontWeight: 500, color: DARK }}>{CUSTOMER.name}</span>
              {CUSTOMER.affiliate && (
                <span style={{ fontSize: "10px", color: MUTED, background: "#f5f3ef", padding: "1px 6px", borderRadius: "4px" }}>via {CUSTOMER.affiliate}</span>
              )}
            </div>
            <span style={{ fontSize: "11px", color: MUTED }}>{CUSTOMER.phone}</span>
          </div>
        </div>

        {/* Delivery date */}
        {deliveryDate && (
          <div style={{ padding: "10px 20px", borderBottom: `1px solid ${BORDER_LIGHT}`, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
            <span style={{ fontSize: "12px", color: MUTED }}>Delivery date</span>
            <span style={{ fontSize: "12px", color: DARK }}>
              {new Date(deliveryDate + "T00:00").toLocaleDateString("en-NG", { day: "numeric", month: "short", year: "numeric" })}
            </span>
          </div>
        )}

        {/* Items */}
        <div style={{ padding: "6px 0" }}>
          {items.map((item, i) => (
            <div key={item.id} style={{ display: "flex", justifyContent: "space-between", alignItems: "center", padding: "10px 20px", borderBottom: i < items.length - 1 ? `1px solid ${BORDER_LIGHT}` : "none" }}>
              <div>
                <div style={{ fontSize: "13px", color: DARK }}>{item.name}</div>
                <div style={{ fontSize: "11px", color: MUTED }}>₦{item.unitPrice.toLocaleString()} × {item.qty}</div>
              </div>
              <span style={{ fontSize: "13px", color: DARK }}>₦{(item.qty * item.unitPrice).toLocaleString()}</span>
            </div>
          ))}
        </div>
      </div>

      <Footer>
        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "baseline", marginBottom: "14px" }}>
          <span style={{ fontSize: "12px", color: MUTED }}>Total</span>
          <span style={{ fontSize: "26px", fontWeight: 500, color: DARK, fontFamily: SERIF, letterSpacing: "-0.01em", lineHeight: 1 }}>
            ₦{subtotal.toLocaleString()}
          </span>
        </div>
        <button style={goldBtnSt} onClick={() => setStep("payment")}>Proceed to Payment →</button>
      </Footer>
    </Shell>
  );

  // ── PAYMENT ──────────────────────────────────────────────────
  if (step === "payment") return (
    <Shell>
      <StepHeader onBack={() => setStep("summary")} title="Payment" />

      <div style={{ flex: 1, padding: "22px 20px", display: "flex", flexDirection: "column", gap: "20px", overflowY: "auto" }}>

        {/* Total due — updates when coupon applied */}
        <div>
          <div style={{ fontSize: "10px", color: MUTED, letterSpacing: "0.1em", marginBottom: "4px" }}>TOTAL DUE</div>
          <div style={{ fontSize: "34px", fontWeight: 500, color: DARK, fontFamily: SERIF, letterSpacing: "-0.02em", lineHeight: 1 }}>
            ₦{total.toLocaleString()}
          </div>
          {appliedCoupon && (
            <div style={{ fontSize: "12px", color: MUTED, marginTop: "4px", textDecoration: "line-through" }}>
              ₦{subtotal.toLocaleString()}
            </div>
          )}
        </div>

        {/* Coupon */}
        {appliedCoupon ? (
          <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", background: "#edf7f1", borderRadius: "8px", padding: "10px 14px" }}>
            <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
              <span style={{ fontSize: "12px", fontWeight: 500, color: "#2a7a4b" }}>{appliedCoupon.code}</span>
              <span style={{ fontSize: "12px", color: "#2a7a4b" }}>−₦{discountAmt.toLocaleString()} ({appliedCoupon.label})</span>
            </div>
            <button onClick={removeCoupon} style={{ border: "none", background: "none", cursor: "pointer", fontSize: "14px", color: "#2a7a4b", padding: 0, lineHeight: 1 }}>×</button>
          </div>
        ) : (
          <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
            <input
              type="text"
              placeholder="Coupon code"
              value={couponInput}
              onChange={e => { setCouponInput(e.target.value); setCouponError(""); }}
              onKeyDown={e => e.key === "Enter" && applyCoupon()}
              style={{ flex: 1, border: "none", borderBottom: `1px solid ${couponError ? "#e0b0a0" : "#e0dbd3"}`, padding: "6px 0", fontSize: "13px", color: DARK, background: "transparent", outline: "none", textTransform: "uppercase" }}
            />
            {couponError
              ? <span style={{ fontSize: "11px", color: "#c0392b", flexShrink: 0 }}>{couponError}</span>
              : <button onClick={applyCoupon} style={{ border: "none", background: "none", cursor: "pointer", fontSize: "12px", color: MUTED, padding: 0, flexShrink: 0 }}>Apply</button>
            }
          </div>
        )}

        {/* Outstanding balance */}
        {CUSTOMER.balance > 0 && (
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", background: "#fdf0e8", borderRadius: "8px", padding: "10px 14px" }}>
            <span style={{ fontSize: "12px", color: "#7a3a18" }}>Outstanding balance</span>
            <span style={{ fontSize: "14px", fontWeight: 500, color: "#7a3a18", fontFamily: SERIF }}>₦{CUSTOMER.balance.toLocaleString()}</span>
          </div>
        )}

        {/* Pay mode */}
        <div style={{ display: "flex", background: "#f5f3ef", borderRadius: "8px", padding: "3px", gap: "2px" }}>
          {[["cash", "Cash"], ["transfer", "Transfer"], ["split", "Split"]].map(([mode, label]) => {
            const active = payMode === mode;
            return (
              <button key={mode} onClick={() => switchPayMode(mode)} style={{ flex: 1, padding: "7px 4px", border: active ? "0.5px solid #e0dbd3" : "none", borderRadius: "6px", fontSize: "12px", cursor: "pointer", background: active ? "#fff" : "transparent", color: active ? DARK : MUTED, fontWeight: active ? 500 : 400, boxShadow: active ? "0 0.5px 2px rgba(0,0,0,0.08)" : "none", transition: "all 0.15s" }}>
                {label}
              </button>
            );
          })}
        </div>

        {/* Cash input */}
        {(payMode === "cash" || payMode === "split") && (
          <div>
            <div style={{ fontSize: "10px", color: MUTED, letterSpacing: "0.1em", marginBottom: "8px" }}>
              {payMode === "split" ? "CASH" : "CASH TENDERED"}
            </div>
            <div style={{ display: "flex", alignItems: "baseline", gap: "6px" }}>
              <span style={{ fontSize: "18px", color: MUTED, fontFamily: SERIF }}>₦</span>
              <input style={amtInputSt} type="number" placeholder="0" value={cashAmt} onChange={e => handleCashInput(e.target.value)} />
            </div>
          </div>
        )}

        {/* Transfer display */}
        {payMode === "transfer" && (
          <div style={{ background: "#f5f3ef", borderRadius: "8px", padding: "14px 16px" }}>
            <div style={{ fontSize: "10px", color: MUTED, letterSpacing: "0.1em", marginBottom: "6px" }}>TRANSFER AMOUNT</div>
            <div style={{ fontSize: "24px", fontWeight: 500, color: DARK, fontFamily: SERIF }}>₦{total.toLocaleString()}</div>
          </div>
        )}

        {/* Transfer input (split) */}
        {payMode === "split" && (
          <div>
            <div style={{ fontSize: "10px", color: MUTED, letterSpacing: "0.1em", marginBottom: "8px" }}>TRANSFER</div>
            <div style={{ display: "flex", alignItems: "baseline", gap: "6px" }}>
              <span style={{ fontSize: "18px", color: MUTED, fontFamily: SERIF }}>₦</span>
              <input style={amtInputSt} type="number" placeholder="0" value={transferAmt} onChange={e => setTransferAmt(e.target.value)} />
            </div>
          </div>
        )}

        {/* Split breakdown */}
        {payMode === "split" && (cashNum > 0 || transferNum > 0) && (
          <div style={{ background: "#f5f3ef", borderRadius: "8px", padding: "12px 14px", display: "flex", flexDirection: "column", gap: "6px" }}>
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "12px", color: MUTED }}>
              <span>Cash</span><span>₦{cashNum.toLocaleString()}</span>
            </div>
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "12px", color: MUTED }}>
              <span>Transfer</span><span>₦{transferNum.toLocaleString()}</span>
            </div>
            <div style={{ borderTop: "0.5px solid #e0dbd3", paddingTop: "6px", display: "flex", justifyContent: "space-between", fontSize: "13px", fontWeight: 500, color: DARK }}>
              <span>Total paid</span><span>₦{(cashNum + transferNum).toLocaleString()}</span>
            </div>
          </div>
        )}

        {/* Change */}
        {change > 0 && (
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", background: "#edf7f1", borderRadius: "8px", padding: "12px 16px" }}>
            <span style={{ fontSize: "12px", color: "#2a7a4b" }}>Change</span>
            <span style={{ fontSize: "16px", fontWeight: 500, color: "#2a7a4b", fontFamily: SERIF }}>₦{change.toLocaleString()}</span>
          </div>
        )}

        {/* Remaining (split) */}
        {payMode === "split" && remaining > 0 && (cashNum > 0 || transferNum > 0) && (
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", background: "#fff5f5", borderRadius: "8px", padding: "12px 16px" }}>
            <span style={{ fontSize: "12px", color: "#c0392b" }}>Remaining</span>
            <span style={{ fontSize: "16px", fontWeight: 500, color: "#c0392b", fontFamily: SERIF }}>₦{remaining.toLocaleString()}</span>
          </div>
        )}
      </div>

      <Footer>
        <button
          style={{ ...goldBtnSt, opacity: isValid ? 1 : 0.45, cursor: isValid ? "pointer" : "not-allowed" }}
          disabled={!isValid}
          onClick={() => setStep("done")}
        >
          Complete Order →
        </button>
      </Footer>
    </Shell>
  );

  // ── DONE ─────────────────────────────────────────────────────
  return (
    <Shell>
      <div style={{ flex: 1, display: "flex", flexDirection: "column", alignItems: "center", justifyContent: "center", gap: "10px" }}>
        <div style={{ fontSize: "28px", color: "#2a7a4b" }}>✓</div>
        <div style={{ fontSize: "15px", fontWeight: 500, color: DARK }}>Order complete</div>
        <div style={{ fontSize: "12px", color: MUTED }}>₦{total.toLocaleString()} received</div>
        <button style={{ ...goldBtnSt, width: "auto", padding: "10px 24px", marginTop: "12px" }} onClick={resetAll}>
          New Order
        </button>
      </div>
    </Shell>
  );
}
