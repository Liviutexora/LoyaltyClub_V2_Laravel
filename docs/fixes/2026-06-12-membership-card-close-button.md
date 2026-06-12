# Membership Card Close Button

**Date:** 2026-06-12  
**Type:** UI-only change  
**Status:** Deployed to development  

---

## File Modified

`resources/views/membership/card.blade.php`

---

## Code Added

A single button placed directly below the QR code section:

```blade
<div style="margin-top:20px;">
    <button type="button" class="btn btn-outline-secondary" onclick="window.close()">Close</button>
</div>
```

---

## Expected Behaviour

| Scenario | Expected result |
|----------|----------------|
| Card tab was opened by Dashboard via `window.open()` or `target="_blank"` click | Tab closes; user remains in the Dashboard tab |
| Card tab was opened by normal navigation | `window.close()` silently ignored — no side effect |
| Card opened directly by typing URL | `window.close()` silently ignored — no side effect |

---

## Testing Steps

1. From the Dashboard (or any page with a membership card link), open `/membership/{legacy_id}` in a new tab.
2. Confirm the QR card renders correctly.
3. Confirm the **Close** button is visible below the QR code.
4. Click **Close**.
   - If the tab was opened via `window.open()` or a `target="_blank"` link: the tab closes and focus returns to the Dashboard.
   - If opened by direct navigation: nothing happens (expected — `window.close()` is silently ignored by the browser).

---

## Rollback Instructions

**Manual:** Remove the `<div style="margin-top:20px;">...</div>` block from
`resources/views/membership/card.blade.php`, leaving only the `</div>` closing
the `.card-container` and the `@endsection` directive.

**Via git:**
```bash
git diff resources/views/membership/card.blade.php
git checkout resources/views/membership/card.blade.php
```
