# Scanner Auto-Close Audit

**Date:** 2026-06-12  
**Author:** Engineering / Copilot Audit  
**Status:** Investigation only — no production changes  

---

## 1. Objective

Determine whether `window.close()` can be safely called from the Laravel scanner tab
after a successful transaction, so the scanner window closes automatically when the
user presses **OK** in the success modal.

---

## 2. Current Scanner Launch Flow

```
CodeIgniter (legacy app)
    │
    │  navigates to →  GET /scanner-launch/{legacy_id}
    ▼
ScannerLaunchController::launch()
    ├─ EcosystemHubService::syncLegacyEntity($legacy_id)
    ├─ session(['company_legacy_user_id' => $legacy_id])
    └─ redirect('/scanner')           ← server-side HTTP 302
         │
         ▼
    QrScannerController::index()
         └─ renders scanner.index view
                 │
                 ├─ html5-qrcode detects QR
                 ├─ POST /scanner/lookup   →  QrScannerController::lookup()
                 ├─ Bootstrap modal #transactionModal opens
                 ├─ POST /scanner/validate-transaction
                 └─ #txSuccessModal shown on data.success === true
```

### Key files

| File | Role |
|------|------|
| `routes/web.php` | Defines `/scanner-launch`, `/scanner`, `/scanner/lookup`, `/scanner/validate-transaction` |
| `app/Http/Controllers/ScannerLaunchController.php` | Sets session, redirects to `/scanner` |
| `app/Http/Controllers/QrScannerController.php` | Renders scanner view, handles QR lookup |
| `resources/views/scanner/index.blade.php` | Scanner UI + `html5-qrcode` + Bootstrap modal trigger |
| `resources/views/scanner/partials/transaction-modal.blade.php` | Transaction form + success/error modals |

---

## 3. How CodeIgniter Opens the Scanner

The CodeIgniter (legacy) codebase is external to this repository. Based on all available
evidence in the Laravel app, the entry point URL is:

```
/scanner-launch/{legacy_id}
```

There are **two possible mechanisms** CodeIgniter could use, each with very different
`window.close()` implications:

### Option A — Regular navigation (link / form / `window.location.href`)

CodeIgniter renders an `<a href="/scanner-launch/...">` or sets
`window.location.href = '...'`.  
The scanner opens **in the same tab**, or the user navigates to it manually.

### Option B — Script-opened tab (`window.open(...)`)

CodeIgniter calls `window.open('/scanner-launch/...', '_blank')` (or similar).  
The scanner opens **in a new tab that has an opener relationship**.

> **Current evidence from the Laravel codebase:**  
> - No `window.open`, `window.opener`, or `postMessage` calls exist anywhere in the
>   Laravel views.  
> - The scanner launch page performs a **server-side redirect** (`HTTP 302`) from
>   `/scanner-launch/{id}` → `/scanner`, which changes the page's URL while the tab
>   remains open.  
> - **We cannot determine** which option CodeIgniter uses without inspecting its source.

---

## 4. How `window.close()` Works in Modern Browsers

### The spec rule

> A window can only be closed by script if it was **opened by script** via `window.open()`.

| Browser | Behaviour when tab was NOT opened by `window.open()` |
|---------|------------------------------------------------------|
| Chrome ≥ 46 | `window.close()` is silently ignored; no error, no effect |
| Firefox | Same — silently ignored |
| Safari | Same — silently ignored |
| Edge (Chromium) | Same — silently ignored |

### The redirect complication

Even if CodeIgniter uses `window.open()`, the chain is:

```
window.open('/scanner-launch/{id}')   →   HTTP 302   →   /scanner
```

- The browser follows the redirect in the **same tab/window handle**.
- The `window.opener` reference **is preserved** through same-origin server-side redirects
  in Chrome and Firefox.
- However, if the legacy CodeIgniter app and the Laravel scanner run on **different
  origins** (different domain or port), `window.opener` is `null` due to the
  cross-origin opener policy (`COOP`).

---

## 5. Risk Assessment

| Risk | Likelihood | Impact |
|------|-----------|--------|
| `window.close()` silently no-ops (Option A) | High if CodeIgniter uses normal nav | Low — user just sees the success modal with no auto-close |
| `window.close()` works but closes unexpectedly during testing | Low | Medium — unexpected for developer during debugging |
| Cross-origin `COOP` blocks `window.opener` (different domains) | Medium | `window.close()` will silently fail |
| `window.close()` works but leaves CodeIgniter in a broken state (e.g. waiting for scanner result) | Low | Medium — depends on CodeIgniter UX expectations |
| Breaking existing Cancel / X button close flow | None — those call `closeScannerUI()` (hides `.scanner-modal`), not `window.close()` | N/A |

**Overall risk of attempting `window.close()`:** Low — the worst case is silent failure.  
The success modal flow already closes the transaction modal and resets fields, so the
scanner UX remains correct even if `window.close()` is ignored.

---

## 6. Recommended Implementation

### Step 1 — Confirm how CodeIgniter opens the scanner (prerequisite)

Inspect the CodeIgniter view/controller that contains the scanner button. Look for:

```php
// CodeIgniter — if this is what it does:
echo anchor('/scanner-launch/'.$id, 'Open Scanner');
// or:
<a href="/scanner-launch/<?= $id ?>" target="_blank">Open Scanner</a>
// or:
<button onclick="window.open('/scanner-launch/<?= $id ?>', '_blank')">Open Scanner</button>
```

Only `window.open(...)` (including `target="_blank"` on a link activated by click)
qualifies a tab as "script-opened" for `window.close()` purposes.

### Step 2 — Add the diagnostic snippet to scanner page (see §7)

### Step 3 — If CodeIgniter uses `window.open()` and same origin: safe to add `window.close()`

Add to the `txSuccessOkBtn` click handler, **after** the existing close/reset logic:

```javascript
txSuccessOkBtn.addEventListener('click', function () {
    $('#txSuccessModal').modal('hide');
    $('#transactionModal').modal('hide');

    var amtEl = document.getElementById('transaction-amount');
    var loyEl = document.getElementById('loyalty-value');
    if (amtEl) { amtEl.value = ''; amtEl.style.backgroundColor = '#fff9e6'; }
    if (loyEl) { loyEl.value = ''; loyEl.style.backgroundColor = '#f8f9fa'; }

    // Attempt to close the scanner tab. Only works if this tab was
    // opened via window.open() by the parent application.
    // Silently ignored by browsers otherwise — no side effects.
    window.close();
});
```

### Step 4 — If `window.close()` will not work (different origin / normal navigation)

Preferred alternative: use `window.opener.postMessage()` to notify CodeIgniter that the
transaction is complete, and let CodeIgniter handle the navigation:

```javascript
// In scanner (child tab) — after success:
if (window.opener && !window.opener.closed) {
    window.opener.postMessage({ event: 'scannerTransactionComplete' }, '*');
    // Restrict '*' to the exact CodeIgniter origin in production, e.g. 'https://legacy.example.com'
}
window.close();
```

```javascript
// In CodeIgniter (parent) — listen for the message:
window.addEventListener('message', function (e) {
    if (e.data && e.data.event === 'scannerTransactionComplete') {
        // e.g. refresh scanner list, show toast, etc.
    }
});
```

---

## 7. Safe Non-Destructive Diagnostic Test

The following snippet can be pasted into the browser DevTools **console** while on the
`/scanner` page. It does not modify any code.

```javascript
// Paste in DevTools console on the /scanner page.
// Shows whether this tab qualifies for window.close().

console.group('Scanner Tab Auto-Close Diagnostic');
console.log('window.opener:', window.opener);
console.log('window.history.length:', window.history.length);
console.log(
    'Verdict:',
    window.opener !== null
        ? '✅ window.opener is set — window.close() SHOULD work'
        : '❌ window.opener is null — window.close() will be silently ignored'
);
console.groupEnd();
```

Expected outputs:

| Result | Meaning |
|--------|---------|
| `window.opener` is an object | CodeIgniter used `window.open()` or `target="_blank"`. `window.close()` is viable. |
| `window.opener` is `null` | Tab was opened by normal navigation. `window.close()` will silently fail. |
| `window.opener` is `null` despite `window.open()` | Cross-origin COOP policy is blocking the reference. `postMessage` approach is needed. |

---

## 8. Estimated Effort

| Scenario | Effort |
|----------|--------|
| Confirm CodeIgniter open mechanism (code review) | 15 min |
| Run diagnostic test (DevTools) | 5 min |
| Add `window.close()` to OK handler (if viable) | 15 min |
| Implement `postMessage` fallback (if needed) | 1–2 h |
| Add `postMessage` listener in CodeIgniter | 1 h |

**Minimum viable path** (happy path, `window.open()` confirmed): ~30 min total.  
**Full cross-origin fallback path**: ~3 h total.

---

## 9. Summary

1. The Laravel scanner has a clean entry point at `/scanner-launch/{legacy_id}` which
   redirects to `/scanner` after setting the session. No scanner auto-close logic exists
   yet.
2. `window.close()` feasibility is **entirely determined by how CodeIgniter opens the
   scanner tab** — which is unknown without inspecting the legacy codebase.
3. The safe first step is the **diagnostic console snippet** (§7), which is zero-risk and
   takes 5 minutes to run.
4. If `window.opener` is set, a single `window.close()` call in the OK button handler is
   sufficient and safe.
5. If `window.opener` is null, `window.close()` is not viable; `postMessage` is the
   correct alternative.
6. No production changes are recommended until the diagnostic test is run.
