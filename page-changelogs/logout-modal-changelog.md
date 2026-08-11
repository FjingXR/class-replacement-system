# Changelog — Logout Modal

## Files Changed

### `resources/views/partials/ui-nav-bar.blade.php`
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | Line 49 (desktop logout btn) | Modified | Replaced `onclick="alert('Logout')"` with `onclick="showLogoutModal()"` |
| 2026-08-03 | Line 98 (mobile drawer logout btn) | Modified | Replaced `onclick="alert('Logout')"` with `onclick="showLogoutModal()"` |
| 2026-08-03 | End of file (line 105) | Added | Hidden logout form: `<form id="logout-form" method="POST" action="{{ route('logout') }}">` + `@csrf` |

### `resources/views/partials/ui-logout-modal.blade.php` (NEW)
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | Entire file | Created | Modal markup: countdown bar, Cancel/Logout now buttons, "Don't ask me again" checkbox. Styles in `<style>` block. |

### `public/js/logout-modal.js` (NEW)
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | Entire file | Created | `showLogoutModal()`: localStorage check, mobile drawer close, 5s countdown, auto-submit, button handlers, checkbox handler |

### `resources/views/layouts/ui-template.blade.php`
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | Line 26 | Added | `@include('partials.ui-logout-modal')` inside nav guard |
| 2026-08-03 | Line 36 | Added | `<script src="/js/logout-modal.js"></script>` after mock-data.js |
