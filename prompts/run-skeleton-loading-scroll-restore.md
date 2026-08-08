/sdd-propose

Add two shared UX enhancements to the TARUMT Class Replacement System: **Skeleton Loading** and **Scroll Restoration**. These are NFR Rule 9 mandates — they go into the shared layer (`public/css/theme.css` + `public/js/ui-common.js`), not page-specific templates.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §10.0 UI Design Rules, Rule 9: mobile responsive design).
- public/css/theme.css — shared CSS tokens; all new CSS goes here.
- public/js/ui-common.js — shared JS helpers; all new JS goes here.
- resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php — first page to apply (already has card view + table).
- resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php — second page to apply.
- resources/views/ui-design-templates/request-approval-UI-design-template.blade.php — third page to apply.

Discuss with me BEFORE you generate the proposal (do not skip):

## Feature 1: Skeleton Loading (NFR Rule 9)

**Problem:** When pages load, users see a blank flash before content appears. Rule 9 mandates "Skeleton loading: Grey placeholder shapes with shimmer animation while data loads."

**Scope:**
- Add `.skeleton`, `.skeleton-text`, `.skeleton-row`, `.skeleton-card` CSS classes to `theme.css`
- Shimmer animation via `@keyframes shimmer` using `--color-surface-variant` and a moving gradient
- Each page that uses mock data should show skeleton placeholders for ~300-500ms (simulated load) before rendering real content
- Skeleton should match the layout of the real content (e.g., table rows → skeleton rows, cards → skeleton cards)
- Skeleton disappears when data is "ready" (use `setTimeout` for mock phase; later replaced by real API call)

**Questions to resolve:**
1. Should skeleton appear on initial page load only, or also when filters change? (Recommendation: initial load only — filters are instant on mock data)
2. How many skeleton rows to show? (Recommendation: match rows-per-page default, e.g. 10 rows)
3. Should summary cards also skeleton-ize? (Recommendation: yes, show skeleton number placeholders)

## Feature 2: Scroll Restoration (NFR Rule 9)

**Problem:** When a user navigates away and clicks browser Back, the page scrolls to top and loses context. Rule 9 mandates "Scroll restoration: Remember scroll position on browser back/forward via sessionStorage."

**Scope:**
- Add `saveScrollPosition(pageKey)` and `restoreScrollPosition(pageKey)` functions to `ui-common.js`
- On `beforeunload` or route change, save `window.scrollY` to `sessionStorage` under key `scroll-{pageKey}`
- On `pageshow` event (back/forward), check `event.persisted` or `sessionStorage` for saved position and `window.scrollTo(0, savedY)`
- Each page calls `saveScrollPosition('my-request-history')` etc. on load
- Clear saved position when user performs a deliberate navigation (not back/forward)

**Questions to resolve:**
1. Which pages get scroll restore? (Recommendation: all table-heavy pages — my-request-history, replacement-home, request-approval)
2. Should scroll position reset when filters change? (Recommendation: yes — user expects top of filtered results)

## Implementation constraints:
- All CSS in `theme.css` only — no inline styles, no page-specific `<style>` blocks for skeleton
- All JS in `ui-common.js` only — reusable functions, not page-specific
- Pages import and call the shared functions; no copy-paste
- Follow existing token system (`--color-surface-variant`, `--color-outline`, etc.)
- No new dependencies
- Shimmer animation must respect `prefers-reduced-motion`

After discussion, generate the SDD proposal with design.md and tasks.md.
