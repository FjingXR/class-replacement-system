# Staff ID Login — Optional "P" Prefix Support Plan

## Goal
Update the Staff login form validation to accept Staff IDs with an optional "P" prefix (e.g., `P5425` or `5425`). This matches real TARUMT staff ID usage and aligns with FR 2.1 in the FYP report (Ch3 §3.4).

---

## Why This Change

- The seeder (`database/seeders/DatabaseSeeder.php`) stores staff IDs as pure 4-digit numbers (e.g., `5425`, `5516`).
- Real TARUMT staff IDs are commonly written with a "P" prefix (e.g., `P5425`).
- FR 2.1 now states: "Lecturers shall be able to log in using Staff ID (four digits with an optional 'P' prefix, e.g., P5425 or 5425) and password."
- Current code rejects the P-prefixed form → report-vs-code mismatch.

---

## Design Decisions

| Aspect | Decision |
|--------|----------|
| **Regex** | `^P?\d{4}$` — optional uppercase `P` followed by exactly 4 digits |
| **Case sensitivity** | Uppercase `P` only (matches real ID format; lowercase `p` rejected) |
| **Hint text** | Update to explain the accepted format |
| **Placeholder** | Update example to include P-prefixed variant |
| **Backend lookup** | No change — the login handler must strip the `P` prefix before querying (see Step 3) |
| **Student login** | Unaffected — student regex stays `^\d{2}[A-Za-z]{3}\d{4}$` |

---

## Files to Create/Modify

| # | File | Action |
|---|------|--------|
| 1 | `resources/views/auth/login-staff.blade.php` | **Modify** — update `idRegex`, `formatHint`, `idPlaceholder` |
| 2 | `public/js/ui-common.js` | **Verify** — `validateLogin()` uses the page's `idRegex` const; no change expected |
| 3 | Login handler (backend) | **Modify** — strip optional `P` prefix from `login_id` before Fortify authentication |
| 4 | `page-changelogs/login-oop-refactor-changelog.md` | **Update** — record this change |

---

## Step 1: Update `login-staff.blade.php`

Current (lines 10-12):

```php
'idPlaceholder' => 'e.g. 6767',
'idRegex' => '^\d+$',
'formatHint' => 'Numeric staff ID only',
```

Change to:

```php
'idPlaceholder' => 'e.g. P5425 or 5425',
'idRegex' => '^P?\d{4}$',
'formatHint' => '4 digits, optional "P" prefix',
```

---

## Step 2: Verify `ui-common.js` `validateLogin()`

The login template builds the regex from `@json($idRegex)` at runtime, so the page-level change flows through automatically. No JS change expected — but verify the login button enables correctly for both `P5425` and `5425` after the change.

---

## Step 3: Strip `P` Prefix Before Authentication (Backend)

The seeder stores staff IDs as pure digits (`5425`). If a user logs in as `P5425`, the backend must normalize it to `5425` before Fortify authenticates.

**Where:** The Fortify login flow uses `config/fortify.php` → `'username' => 'login_id'`. Normalization can be done:

- **Option A (recommended):** In the login request lifecycle (e.g., a middleware on the `/login` route or in the login template's hidden input) — convert `P5425` → `5425` before submission.
- **Option B:** A custom Fortify `LoginResponse`/request hook that trims a leading `P` from `login_id` for staff logins.

**Rule:** Only strip the `P` when `login_type === 'staff'`. Student IDs must pass through untouched.

**Note:** If the DB is later seeded with P-prefixed staff IDs instead, skip this step — but then `loginId()` in `app/Models/User.php` must stay consistent with the stored format.

---

## Step 4: Verification Checklist

- [ ] Login with `5425` + password → success
- [ ] Login with `P5425` + password → success
- [ ] Login with `p5425` (lowercase) → rejected by client regex
- [ ] Login with `542` (3 digits) → rejected by client regex
- [ ] Login with `54255` (5 digits) → rejected by client regex
- [ ] Login with `P54255` → rejected by client regex
- [ ] Student login page unaffected (`25RSD0001` still works)
- [ ] Format hint text displays correctly under the input

---

## Order of Implementation

1. `login-staff.blade.php` — regex + hint + placeholder
2. Verify client-side validation in browser (both login pages)
3. Backend normalization (strip `P` for staff logins)
4. Full verification checklist
5. Update `login-oop-refactor-changelog.md`

---

## Notes

- The `loginType` hidden field already distinguishes staff from student (`'loginType' => 'staff'`), so the P-strip rule can safely key off it.
- Student ID regex (`^\d{2}[A-Za-z]{3}\d{4}$`) is untouched — this task is staff-only.
- This change keeps the report (FR 2.1) and code consistent for the moderator check.
