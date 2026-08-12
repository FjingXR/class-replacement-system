# Design: macOS-Inspired UI Refactor

## Technical Approach

Replace Material Design 3-inspired design tokens with macOS-inspired aesthetics. The refactor uses the existing CSS variable system — add a `.macos` class with new tokens, update `toggleTheme()` to cycle through 3 themes.

## Architecture Decisions

### 1. Theme System

**Decision:** Add `.macos` as third theme class alongside `.dark` and `.light`.

```css
/* theme.css */
.macos {
    --color-bg: #F5F5F7;
    --color-on-bg: #1D1D1F;
    --color-surface: #FFFFFF;
    --color-on-surface: #1D1D1F;
    --color-primary: #0066CC;
    --color-on-primary: #FFFFFF;
    /* ... */
}
```

### 2. Toggle Theme Function

**Decision:** Cycle through 3 themes: macOS → dark → light → macOS.

```javascript
// ui-common.js
function toggleTheme() {
    const html = document.documentElement;
    const themes = ['macos', 'dark', 'light'];
    const current = themes.find(t => html.classList.contains(t)) || 'macos';
    const next = themes[(themes.indexOf(current) + 1) % themes.length];
    
    themes.forEach(t => html.classList.remove(t));
    html.classList.add(next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
}
```

### 3. Pre-paint IIFE

**Decision:** Handle 3 themes in pre-paint to prevent flash of unstyled content.

```javascript
// login-template.blade.php
(function() {
    var saved = localStorage.getItem('theme');
    if (saved && ['macos', 'dark', 'light'].includes(saved)) {
        document.documentElement.className = saved;
    }
    // Default is 'macos' from HTML
})();
```

### 4. Button Shape (Login)

**Decision:** Use pill-shaped buttons matching macOS "Use Password..." style.

```css
.login-btn {
    border-radius: 16px;
    padding: 10px 24px;
    font-weight: 500;
    font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
}
```

### 5. Typography Hierarchy

**Decision:** Follow Apple HIG typography hierarchy.

- **Primary:** Bold/Semibold, 20pt-34pt (headings)
- **Secondary:** Regular/Medium, 15pt-17pt (body)
- **Tertiary:** Regular, 11pt-13pt, 60% opacity (captions)

## File Changes

| File | Change |
|------|--------|
| `public/css/theme.css` | Add `.macos` class (~200 lines) |
| `public/js/ui-common.js` | Update `toggleTheme()` (~20 lines) |
| `resources/views/layouts/login-template.blade.php` | Update button/input styles |
| `resources/views/layouts/ui-template.blade.php` | Update pre-paint IIFE |
| 13 partials | Update to use macOS design tokens |
| 8 UI design templates | Update to use macOS design tokens |

## Known Residuals

- SF Pro font not used (copyright safe: use system font stack)
- Apple logo not used (use custom icon)
- Color values similar but not exact copies
