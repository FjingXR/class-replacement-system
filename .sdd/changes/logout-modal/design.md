# Design: Logout Confirmation Modal

## Architecture

No new migrations or models. Changes span 4 existing files + 2 new files:

| File | Change type |
|------|------------|
| `resources/views/partials/ui-nav-bar.blade.php` | Modify — replace onclick + add hidden form |
| `resources/views/partials/ui-logout-modal.blade.php` | **NEW** — modal markup + styles |
| `public/js/logout-modal.js` | **NEW** — countdown, skip/undo, localStorage |
| `resources/views/layouts/ui-template.blade.php` | Modify — include JS |

## Key decisions

### 1. Logout form in nav bar

Replace both `onclick="alert('Logout')"` with `onclick="showLogoutModal()"`. Add a hidden logout form at the end of the partial (single form, used by both modal and direct submit):

```blade
<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>
```

Placed at the end of the partial, after the `</div>` closing `.nav-drawer`. Both buttons call `showLogoutModal()` which checks localStorage first.

### 2. Modal markup

New partial `ui-logout-modal.blade.php`:

```blade
<div class="modal-overlay" id="logoutModal" role="alertdialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="modal">
        <h3 id="logoutModalTitle">Confirm Logout</h3>
        <p>Your session will end in <span id="logoutCountdown">5</span> seconds.</p>
        <div class="logout-countdown-bar">
            <div class="logout-countdown-fill" id="logoutCountdownFill"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-outline" id="logoutCancel">Cancel</button>
            <button type="button" class="btn-danger" id="logoutNow">Logout now</button>
        </div>
        <label class="logout-dont-ask">
            <input type="checkbox" id="logoutDontAsk"> Don't ask me again
        </label>
    </div>
</div>
```

Reuses existing `.modal-overlay` + `.modal` + `.modal-footer` classes from theme.css. Uses existing `.btn-outline` (Cancel) and `.btn-danger` (Logout now) button classes. Uses `role="alertdialog"` for time-sensitive auto-action.

### 3. JS logic (`logout-modal.js`)

```javascript
const LOGOUT_COUNTDOWN = 5;
let logoutTimer = null;

function showLogoutModal() {
    // Close mobile nav drawer if open
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    if (drawer && drawer.classList.contains('open')) {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Check "don't ask me again"
    if (localStorage.getItem('logout_no_confirm') === '1') {
        document.getElementById('logout-form').submit();
        return;
    }

    // Show modal
    const modal = document.getElementById('logoutModal');
    modal.classList.add('show');
    let remaining = LOGOUT_COUNTDOWN;
    updateCountdownUI(remaining);

    // Countdown interval
    logoutTimer = setInterval(() => {
        remaining--;
        updateCountdownUI(remaining);
        if (remaining <= 0) {
            clearInterval(logoutTimer);
            document.getElementById('logout-form').submit();
        }
    }, 1000);

    // Button handlers
    document.getElementById('logoutNow').onclick = () => {
        clearInterval(logoutTimer);
        document.getElementById('logout-form').submit();
    };
    document.getElementById('logoutCancel').onclick = () => {
        clearInterval(logoutTimer);
        modal.classList.remove('show');
    };

    // "Don't ask me again" handler
    document.getElementById('logoutDontAsk').onchange = (e) => {
        if (e.target.checked) {
            localStorage.setItem('logout_no_confirm', '1');
        } else {
            localStorage.removeItem('logout_no_confirm');
        }
    };
}

function updateCountdownUI(seconds) {
    document.getElementById('logoutCountdown').textContent = seconds;
    const fill = document.getElementById('logoutCountdownFill');
    const pct = (seconds / LOGOUT_COUNTDOWN) * 100;
    fill.style.width = pct + '%';
}
```

### 4. CSS (in partial)

Styles are placed in a `<style>` block inside `ui-logout-modal.blade.php` (single-use, not promoted to theme.css — follows DRY "promote-on-3rd-duplication" rule):

```css
/* Logout countdown bar */
.logout-countdown-bar {
    width: 100%;
    height: 6px;
    background: var(--color-surface-variant);
    border-radius: 3px;
    overflow: hidden;
    margin: 0.75rem 0;
}
.logout-countdown-fill {
    height: 100%;
    background: var(--color-primary);
    border-radius: 3px;
    transition: width 1s linear;
}

/* Logout dont-ask checkbox */
.logout-dont-ask {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    font-size: 0.875rem;
    color: var(--color-on-surface-variant);
    cursor: pointer;
}
```

Reuses `var(--color-primary)` and `var(--color-surface-variant)` tokens — no hardcoded hex.

### 5. Layout inclusion

In `resources/views/layouts/ui-template.blade.php`, add inside `@auth` block after the nav bar include:

```blade
@include('partials.ui-logout-modal')
<script src="/js/logout-modal.js"></script>
```

Placed after `@include('partials.ui-nav-bar')` and before `@yield('content')`.

### 6. Mobile nav drawer interaction

`showLogoutModal()` checks if the mobile nav drawer is open (has class `.open`) and closes it before showing the modal. This includes:
- Removing `.open` from the drawer
- Removing `.open` from the overlay
- Resetting `document.body.style.overflow = ''` (prevents scroll-lock leak)

This prevents layered UI states where the drawer and modal overlap.

## Frontend approach

All styles reuse existing CSS tokens from `theme.css` (no new custom properties):
- `var(--color-primary)` — countdown fill bar
- `var(--color-surface-variant)` — countdown bar background
- `var(--color-on-surface-variant)` — checkbox label text
- Existing `.modal-overlay` + `.modal` — modal container
- Existing `.modal-footer` — button layout
- Existing `.btn-outline` + `.btn-danger` — action buttons

## Promoted to shared

None — all styles are single-use, kept in the partial. Promote to `theme.css` only if reused by 3+ pages.
