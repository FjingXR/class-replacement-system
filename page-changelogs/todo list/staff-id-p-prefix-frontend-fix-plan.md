# FR 2.1 Frontend Fix — Staff ID Optional "P" Prefix (Detailed Plan)

**Date:** 2026-08-21 | **Status:** `pending` | **Priority:** `high` | **FR Ref:** `draft/QuaterFinal/ch3/ch3.4.md:22` FR 2.1
**Scope:** FRONTEND ONLY — no DB migration, no seeder change. Backend auth note included for completeness but code change deferred (user will handle separately if needed).

---

## 1. Goal

Make the **Staff Login page accept** `P5425` **and** `5425` (both 4-digit forms) so `draft/QuaterFinal/ch3/ch3.4.md:22` ("four digits with an optional 'P' prefix, e.g., P5425 or 5425") matches reality. Student login must stay untouched.

---

## 2. Problem Statement (Report says X, Code shows Y)

| Layer | Report `ch3.4.md:22` | Current Code | Result |
|-------|----------------------|--------------|--------|
| Frontend validation | `P5425 or 5425` accepted | `resources/views/auth/login-staff.blade.php:3` `'idRegex' => '^\d+$'` (any digit length, no P) | `P5425` → button stays `disabled`, never submits |
| Format hint | "4 digits, optional P" | `'formatHint' => 'Numeric staff ID only'` `:3` | User misled |
| Placeholder | `P5425 or 5425` example | `'idPlaceholder' => 'e.g. 6767'` `:3` | Wrong example |

**Backend note (not in this frontend plan):** `app/Providers/FortifyServiceProvider.php:28-35` does `Lecturer::where('staff_id', $loginId)->first()` exact match; DB stores `5425` (`dataset/lecturers.md:1`). Even after frontend allows `P5425`, backend will fail unless it strips `P` — see §7 but **DO NOT EDIT CODE NOW**.

---

## 3. Current Frontend Code Audit

### 3.1 `resources/views/auth/login-staff.blade.php:1`

```php
@extends('layouts.login-template', [
    'title' => 'Staff',
    'bgImage' => '/images/staff-login-bg.jpg',
    'btnColorToken' => '--color-primary',
    'btnColorOnToken' => '--color-on-primary',
    'btnHoverDark' => '#003d7a',
    'btnHoverLight' => '#0066cc',
    'spinnerColorToken' => '--color-on-primary',
    'idLabel' => 'Staff ID',
    'idPlaceholder' => 'e.g. 6767',
    'idRegex' => '^\d+$',
    'formatHint' => 'Numeric staff ID only',
    'loginType' => 'staff',
    'roleSwitchHtml' => 'Student? <a href="' . route('login.student') . '">Student Login</a>',
])
```

**Issues:** `idRegex` too loose (`123` passes, `12345` passes, `P5425` fails) and contradicts FR 2.1 exact-4-digits rule. Placeholder/hint wrong.

### 3.2 `resources/views/layouts/login-template.blade.php:420-431`

```js
const idRegex = new RegExp(@json($idRegex));

function validateLogin() {
    const loginId = document.getElementById('login_id').value.trim();
    const password = document.getElementById('password').value;
    const btn = document.getElementById('loginBtn');
    const idValid = idRegex.test(loginId);
    const pwValid = password.length > 0;
    btn.disabled = !(idValid && pwValid);
}
```

- Consumes `@json($idRegex)` directly → **no JS change needed** if we fix blade `idRegex`. `validateLogin()` is called on `oninput` (`:382,396`) and `DOMContentLoaded` (`:436`).
- `public/js/ui-common.js` not involved for login (only `theme.css` toggle).

### 3.3 `resources/views/auth/login-student.blade.php:1`

```php
'idRegex' => '^\d{2}[A-Za-z]{3}\d{4}$',
```

**Must NOT change** — verify after staff fix that student page still validates `25RSD0001`.

---

## 4. Design Decisions

| Aspect | Decision | Rationale |
|--------|----------|-----------|
| Frontend regex | `^P?\d{4}$` | Optional uppercase `P` + exactly 4 digits. Matches FR 2.1 example `P5425`/`5425` and real TARUMT HR format. |
| Case sensitivity | **Uppercase `P` only** (reject `p5425`) | Report writes `P` uppercase; dataset is `5425` numeric; avoids accidental `p` vs `P` confusion. If supervisor wants lowercase, change to `^[Pp]?\d{4}$` later — one-line. |
| Placeholder | `e.g. P5425 or 5425` | Shows both valid forms, B1-friendly. |
| Hint | `4 digits, optional "P" prefix` | Short, tells length + option. Matches FR wording. |
| Student regex | Untouched | Separate FR 1.1. |

---

## 5. Step-by-Step Frontend Fix (No Code Edits Yet — Execute in Order)

### Step 1 — Edit `login-staff.blade.php` (1 file, 3 tokens)

**File:** `resources/views/auth/login-staff.blade.php:3`

**Before:**
```php
'idPlaceholder' => 'e.g. 6767',
'idRegex' => '^\d+$',
'formatHint' => 'Numeric staff ID only',
```

**After:**
```php
'idPlaceholder' => 'e.g. P5425 or 5425',
'idRegex' => '^P?\d{4}$',
'formatHint' => '4 digits, optional "P" prefix',
```

**Why 3 tokens together:** User sees placeholder + hint + live validation — all must agree.

### Step 2 — Verify `login-template.blade.php` Needs No Edit

- Confirm `:420` still `new RegExp(@json($idRegex))` (no hardcode).
- Confirm `:382` `oninput="validateLogin()"` present.
- **Action:** Visual check only — no write.

### Step 3 — Clear Blade Cache (Required by `AGENTS.md:31`)

```bash
pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &
```

Always kill old server first — stale compiled views hold old regex in memory.

### Step 4 — Browser Verification (Frontend Only)

Open `http://localhost:8000/login/staff` (staff) and `/login/student` (control).

| # | Input (Staff) | Expected `validateLogin()` |
|---|---------------|----------------------------|
| 1 | `5425` + pw | Button **enabled** |
| 2 | `P5425` + pw | **Enabled** |
| 3 | `p5425` | **Disabled** (lowercase rejected) |
| 4 | `542` (3 digits) | Disabled |
| 5 | `54255` (5 digits) | Disabled |
| 6 | `P542` | Disabled |
| 7 | `PP5425` | Disabled |
| 8 | ` 5425 ` (spaces) | Enabled after `trim()` |
| 9 | Empty + pw | Disabled |
| 10 | Valid ID + empty pw | Disabled |

**Student control:** `25RSD0001` → enabled; `25186` → disabled; `P5425` on student page → disabled (student regex unchanged).

### Step 5 — Update Changelog

- `page-changelogs/login-oop-refactor-changelog.md` — add entry: date, FR 2.1, 3 tokens changed, regex before/after.
- `page-changelogs/todo list/todo-list.md:173` TASK-004 status `pending` → `completed` after verify.

---

## 6. Verification Checklist (Frontend Sign-Off)

- [ ] `login-staff.blade.php` shows `idRegex ^P?\d{4}$` in View Source
- [ ] Console: `new RegExp("^P?\\d{4}$").test("P5425")==true`, `"5425"==true`, `"p5425"==false`
- [ ] Button enables for both `P5425` + `5425`, disables for bad lengths
- [ ] Hint reads `4 digits, optional "P" prefix` under Staff ID input
- [ ] Placeholder shows `e.g. P5425 or 5425`
- [ ] Student login unaffected (`login-student.blade.php` still `^\d{2}[A-Za-z]{3}\d{4}$`)
- [ ] No JS console errors on `DOMContentLoaded`

---

## 7. Backend Note (Deferred — Not Part of This Frontend Plan)

Frontend allow alone will still fail login if user types `P5425` because DB holds `5425`. Full login needs backend normalization:

```php
// app/Providers/FortifyServiceProvider.php:28 — inside staff branch
$loginId = $request->input('login_id');
if ($loginType === 'staff') {
    $loginId = strtoupper(ltrim($loginId, 'Pp')); // P5425→5425, p5425→5425 if lower allowed
    // optional: validate preg_match('/^\d{4}$/', $loginId) before query
    $lecturer = Lecturer::where('staff_id', $loginId)->first();
}
```

**User said "DO NOT TOUCH CODE YET" — so this is plan-only, not executed now.** Report-backend alignment will remain `pending` until you implement this in separate step.

---

## 8. Risks & Edge Cases

- Changing regex from `^\d+$` to `^P?\d{4}$` tightens from "any digits" to exactly 4 — old `staff_id` like `6767` still passes (4 digits), but `123` or `123456` will now be rejected (intended per FR 2.1).
- If HR ever uses 5-digit IDs, FR 2.1 must be amended — current dataset is all 4-digit (`dataset/lecturers.md`).
- No migration needed; `lecturers.staff_id` is `string(30)` unique (`0001_01_01_000008_create_lecturers_table.php:11`).

---

## 9. Order of Execution (When You Approve)

1. Step 1 file edit
2. Step 3 cache clear + serve
3. Step 4 browser matrix
4. Step 5 changelog + todo-list update
5. (Later) §7 backend strip — separate plan execution on your go

---

*Plan covers frontend only per your "ignore backend first" — detailed enough to execute in <10 min without re-reading code.*
