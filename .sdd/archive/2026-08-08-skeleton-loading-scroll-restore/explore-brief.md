# Explore Brief: Skeleton Loading + Scroll Restoration

## Goal

Add two shared UX enhancements to the TARUMT Class Replacement System as NFR Rule 9 mandates. Both go into the shared layer (`theme.css` + `ui-common.js`), not page-specific templates.

---

## Feature 1: Skeleton Loading

### Problem
When pages load, users see a blank flash before content appears. Rule 9 mandates "Skeleton loading: Grey placeholder shapes with shimmer animation while data loads."

### Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| When to show | Initial load + filter/search changes | Future-proof for backend wiring; on mock data filters are instant so skeleton won't flash |
| Number of skeleton rows | 10 (matches default RPP) | Proportional to actual content, no need to read RPP setting |
| Summary cards skeleton | Yes — skeleton placeholder numbers | Full consistency with table skeleton |
| Respect `prefers-reduced-motion` | Yes — disable shimmer, show static grey blocks | Accessibility (WCAG 2.1); one `@media` rule |
| Color scheme | Use existing tokens: `--color-surface-variant` base + `--color-outline` shimmer | No new tokens needed; skeleton is temporary |
| Shimmer speed | 1.5s per cycle | Industry standard (GitHub, LinkedIn, Facebook) |

### Rejected Approaches
- **Skeleton on filter changes only:** Too simple now, rework needed later for backend
- **Skeleton on all operations (sort, pagination, etc.):** Overkill for instant mock data; causes glitchy <50ms flashes
- **Dedicated skeleton tokens:** Unnecessary for ~300ms temporary animation
- **Custom shimmer colors:** Existing token system provides sufficient contrast

### CSS Classes (theme.css)
- `.skeleton` — base container
- `.skeleton-text` — text line placeholder
- `.skeleton-row` — table row placeholder
- `.skeleton-card` — summary card placeholder
- `@keyframes shimmer` — gradient animation
- `@media (prefers-reduced-motion: reduce)` — disable animation

### JS Functions (ui-common.js)
- `showSkeleton(container)` — inject skeleton HTML into container
- `hideSkeleton(container)` — remove skeleton, show real content
- `withSkeleton(callback, delay=400)` — wraps any async operation with skeleton

---

## Feature 2: Scroll Restoration

### Problem
When a user navigates away and clicks browser Back, the page scrolls to top and loses context. Rule 9 mandates "Scroll restoration: Remember scroll position on browser back/forward via sessionStorage."

### Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Which pages | All pages (global) | Future-proof; zero per-page work; new pages get it for free |
| When to clear | Clear on deliberate navigation (nav link clicks) | Back/forward keeps position; deliberate nav resets |
| Reset on filter/search | Yes — reset to top | Standard UX; user expects to see results from top after filtering |

### Rejected Approaches
- **Table-heavy pages only:** Would need manual addition to each new page later
- **Never clear:** Disorienting — user sees stale position after deliberate navigation
- **Clear after 30 minutes:** Adds timestamp tracking complexity for marginal benefit
- **Reset only on search, keep on filter:** Too granular; inconsistent behavior confuses users

### JS Functions (ui-common.js)
- `saveScrollPosition(pageKey)` — saves `window.scrollY` to `sessionStorage` key `scroll-{pageKey}`
- `restoreScrollPosition(pageKey)` — restores from `sessionStorage` on `pageshow`
- `clearScrollPosition(pageKey)` — removes saved position
- `initScrollRestore(pageKey)` — wires up `beforeunload` save + `pageshow` restore + nav click clear

### Data Flow
```
Page load → initScrollRestore('page-name')
  ↓
beforeunload → saveScrollPosition() → sessionStorage
  ↓
User clicks link → clearScrollPosition() → sessionStorage.removeItem()
  ↓
User clicks Back → pageshow → restoreScrollPosition() → window.scrollTo()
```

---

## Cross-Module Data Flows

| Flow | From | To | Data |
|------|------|-----|------|
| Skeleton show | Page JS | ui-common.js | container element, delay |
| Skeleton hide | ui-common.js | Page DOM | removes skeleton nodes |
| Scroll save | beforeunload event | sessionStorage | pageKey + scrollY |
| Scroll restore | pageshow event | window.scrollTo | saved Y position |
| Scroll clear | nav click handler | sessionStorage | removes key |

---

## Implementation Constraints

- All CSS in `theme.css` only — no inline styles, no page-specific `<style>` blocks
- All JS in `ui-common.js` only — reusable functions, not page-specific
- Pages import and call shared functions; no copy-paste
- Follow existing token system (`--color-surface-variant`, `--color-outline`, etc.)
- No new dependencies
- Shimmer animation must respect `prefers-reduced-motion`

---

## Open Questions / Unresolved

1. **Skeleton on initial load timing:** 300ms, 400ms, or 500ms? (Recommendation: 400ms — feels instant but long enough to see the skeleton)
2. **Scroll restore key naming:** `scroll-{pageKey}` format — any conflicts with existing sessionStorage keys?
3. **Skeleton injection method:** Inner HTML replacement vs. overlay vs. conditional rendering? (Recommendation: inner HTML replacement — simplest, matches existing patterns)
