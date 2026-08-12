# SDD: macOS-Inspired UI Refactor

## Status: Proposed

## Summary
Refactor the entire UI from Material Design 3-inspired design to a clean, macOS-inspired aesthetic. macOS becomes the default theme, with dark/light toggle preserved as secondary options.

## Motivation
- Modernize the UI with Apple-like clean aesthetics
- Improve visual consistency across all pages
- Better typography hierarchy (Apple HIG)
- Cleaner shadows, borders, and spacing

## Scope
- **Phase 1:** Login pages (staff + student)
- **Phase 2:** CSS variables + theme system (add `.macos` class, update `toggleTheme()`)
- **Phase 3:** Shared partials (13 files)
- **Phase 4:** Core UI templates (8 pages)
- **Phase 5:** Responsive + polish

## Theme System
- **Default:** macOS-inspired (clean, light background, pill buttons, SF Pro-like font)
- **Dark:** Current dark mode (preserved)
- **Light:** Current light mode (preserved)
- Toggle cycles: macOS → dark → light → macOS

## Design Tokens (macOS Theme)
- **Font:** `-apple-system, BlinkMacSystemFont, 'Inter', sans-serif`
- **Primary:** `#0066CC` (blue)
- **Secondary:** `#28A745` (green)
- **Background:** `#F5F5F7` (light gray)
- **Surface:** `#FFFFFF` (white)
- **Border radius:** `16px` (pill buttons), `12px` (cards), `8px` (inputs)
- **Shadows:** Subtle, layered (`0 4px 12px rgba(0,0,0,0.1)`)

## Key Changes

### Login Pages
- Pill-shaped buttons (`border-radius: 16px`)
- Compact padding (`10px 24px`)
- System font stack
- Clean card shadows
- 3-state theme toggle

### Theme System
- Add `.macos` class to `theme.css`
- Update `toggleTheme()` in `ui-common.js`
- Update pre-paint IIFEs in both layouts
- Update theme toggle icon (3 states)

### Shared Partials
- Nav bar: pill-shaped items, clean shadows
- Summary cards: rounded, subtle shadows
- Modal: macOS-style centered dialog
- Form inputs: larger border-radius, subtle borders

### UI Templates
- All 8 pages updated to use macOS design tokens
- Typography hierarchy (bold headings, regular body)
- Clean spacing and layout

## Files Affected
- `public/css/theme.css` (add `.macos` class, ~200 lines)
- `public/js/ui-common.js` (update `toggleTheme()`, ~20 lines)
- `resources/views/layouts/login-template.blade.php` (button styles)
- `resources/views/layouts/ui-template.blade.php` (pre-paint IIFE)
- 13 partials under `partials/`
- 8 UI design templates under `ui-design-templates/`

## Estimated Effort
- Phase 1 (Login): 1-2 hours
- Phase 2 (CSS Variables): 2-3 hours
- Phase 3 (Partials): 4-6 hours
- Phase 4 (UI Templates): 8-12 hours
- Phase 5 (Responsive): 3-4 hours
- **Total:** 18-27 hours

## FYP Deadline
3-4 weeks from now
