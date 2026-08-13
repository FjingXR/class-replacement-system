# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: venue-timetable.spec.ts >> Venue Timetable UI >> TC17 — clicking prev week on week 1 stays at week 1
- Location: tests/venue-timetable.spec.ts:132:3

# Error details

```
Error: expect(locator).toBeDisabled() failed

Locator:  locator('.week-arrow[aria-label="Previous week"]')
Expected: disabled
Received: enabled
Timeout:  5000ms

Call log:
  - Expect "toBeDisabled" with timeout 5000ms
  - waiting for locator('.week-arrow[aria-label="Previous week"]')
    14 × locator resolved to <button class="week-arrow" onclick="prevWeek()" aria-label="Previous week">‹</button>
       - unexpected value "enabled"

```

```yaml
- button "Previous week": ‹
```