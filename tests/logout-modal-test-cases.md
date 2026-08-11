# Test Cases — Logout Confirmation Modal

## Prerequisites
- Run `php artisan serve` or access via local server
- Log in as any user (student or staff)
- Open browser DevTools → Application → Local Storage → ensure `logout_no_confirm` is NOT set

---

## TC-01: Desktop logout button opens modal
| Step | Action | Expected |
|------|--------|----------|
| 1 | Log in as any user | Nav bar shows user panel with avatar, name, role |
| 2 | Click the logout button (door icon) in the top-right | Modal appears with "Confirm Logout" heading |
| 3 | Observe countdown text | Shows "Your session will end in 5 seconds." |
| 4 | Observe countdown bar | Blue bar decreases from 100% to 0% over 5 seconds |

---

## TC-02: Cancel closes modal, stays on page
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click logout to open modal | Modal appears |
| 2 | Click "Cancel" button | Modal closes, user remains on the same page |
| 3 | Verify session is still active | User panel still shows logged-in user data |

---

## TC-03: Logout now submits immediately
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click logout to open modal | Modal appears, countdown starts |
| 2 | Click "Logout now" button | User is logged out immediately, redirected to login page |

---

## TC-04: Auto-submit at 0 seconds
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click logout to open modal | Modal appears |
| 2 | Do NOT click anything, wait 5 seconds | At 0s, user is auto-logged out, redirected to login page |

---

## TC-05: Don't ask me again — bypass modal
| Step | Action | Expected |
|------|--------|----------|
| 1 | Log in | User panel shows logged-in user |
| 2 | Click logout to open modal | Modal appears |
| 3 | Check "Don't ask me again" checkbox | Checkbox is ticked |
| 4 | Click "Cancel" | Modal closes, user stays on page |
| 5 | Click logout again | No modal — user is logged out immediately |
| 6 | Log in again | User panel shows logged-in user |
| 7 | Open DevTools → Application → Local Storage | `logout_no_confirm` = `"1"` |

---

## TC-06: Revert "Don't ask me again"
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open DevTools → Console | Console is ready |
| 2 | Run: `localStorage.removeItem('logout_no_confirm')` | Command executes |
| 3 | Click logout | Modal appears again (5s countdown) |

---

## TC-07: Mobile nav drawer — logout button opens modal
| Step | Action | Expected |
|------|--------|----------|
| 1 | Resize browser to mobile width (≤768px) | Hamburger menu icon appears |
| 2 | Click hamburger icon | Nav drawer slides open |
| 3 | Click "Logout" button in the drawer | Drawer closes, then modal appears |
| 4 | Observe modal | Same as desktop — 5s countdown, Cancel, Logout now, Don't ask |

---

## TC-08: Mobile — page scrolls normally after cancel ✅ PASSED
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open Chrome, press F12 (DevTools), click the phone icon (top-left) to simulate mobile | Page switches to mobile view |
| 2 | Click the hamburger icon (3 lines) | Side menu opens |
| 3 | Click "Logout" in the side menu | Menu closes, modal appears |
| 4 | Click "Cancel" | Modal closes |
| 5 | Try scrolling the page up and down with your mouse wheel | Page scrolls normally (not stuck/frozen) |

---

## TC-09: Countdown bar animation ✅ PASSED
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click logout to open modal | Countdown bar appears full (blue) |
| 2 | Watch for 5 seconds | Bar smoothly decreases to 0% over 5 seconds |
| 3 | Observe number | Countdown text updates: 5 → 4 → 3 → 2 → 1 → 0 |

---

## TC-10: Click logout button twice quickly ✅ PASSED
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click the logout button | Modal appears |
| 2 | Click "Cancel" to close it | Modal closes |
| 3 | Now double-click the logout button VERY fast (like clicking a link) | Only ONE modal appears (not two stacked modals) |
| 4 | Open DevTools → Console tab (bottom panel) | No red error messages |

---

## TC-11: Modal doesn't break other modals ✅ PASSED
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open any other modal on the page (e.g., click a class in the timetable) | Other modal opens correctly |
| 2 | Close it | Other modal closes correctly |
| 3 | Click logout | Logout modal opens correctly |
| 4 | Cancel | Modal closes, other modals still work |

---

## TC-12: Dark theme consistency ✅ PASSED
| Step | Action | Expected |
|------|--------|----------|
| 1 | Ensure dark theme is active (moon icon in nav bar) | Dark background |
| 2 | Click logout | Modal has dark surface background, light text, matches theme |
| 3 | Click the moon icon to toggle to light theme | Modal has light surface background, dark text |
| 4 | Click the sun icon to toggle back to dark | Modal returns to dark theme |

---

## TC-13: Tab key moves focus inside modal
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click logout to open modal | Modal appears |
| 2 | Press the Tab key on your keyboard | A button gets a blue outline (focused) |
| 3 | Press Tab again | Focus moves to the next button |
| 4 | Press Enter | The focused button is clicked |
| 5 | If "Cancel" was focused → modal closes. If "Logout now" was focused → you're logged out |

---

## TC-14: Test with expired session (advanced)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open `config/session.php`, find `lifetime` and change it to `1` (1 minute) | Config updated |
| 2 | Log in as staff | Session starts |
| 3 | Wait 2 minutes without clicking anything | Session expires in background |
| 4 | Click logout | Modal may flash briefly, then you're redirected to login page (session was already dead) |
| 5 | **Remember:** change `lifetime` back to `43200` when done testing | Config restored |

---

## TC-15: Test in private/incognito window ✅ PASSED
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open Chrome → File → New Incognito Window (or press Ctrl+Shift+N) | New window opens |
| 2 | Go to your app URL, log in | User panel shows logged-in user |
| 3 | Click logout → modal appears → check "Don't ask me again" → click Cancel | Checkbox saved |
| 4 | Click logout again | Modal is SKIPPED (you're logged out directly) |
| 5 | Close the incognito window completely | Window closes |
| 6 | Open a NEW incognito window, go to app, log in | Fresh session |
| 7 | Click logout | Modal appears again (preference was cleared when window closed) |

---

## Notes
- All CSS uses existing tokens from `theme.css` — verify no hardcoded hex colors
- Modal reuses `.modal-overlay`, `.modal`, `.modal-header`, `.modal-body`, `.modal-footer` classes
- JS has no external dependencies (vanilla JS only)
