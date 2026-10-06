---
name: copy-fix-overdue
created: 2026-10-06
status: proposing
---
# Proposal — copy-fix-overdue

**Input:** user correction — the Overdue status description on replacement-home says
"a replacement can no longer be arranged automatically", but that is false: the
Overdue row keeps a working **Arrange** button (the round-1 F-1 decision) and the
arrangement page really does let the lecturer book the passed class into any
upcoming week (the week selector hides past/current weeks; the 3-working-day
lead-time rule applies to the *chosen slot*, not to the passed class date).

**Change (one string):** `replacement-home:480` Status Description, overdue branch →

> "Class date has passed — you can still arrange a replacement in any upcoming week"

No other occurrences exist (verified: the only other "can no longer" hit is the
arrangement page's 3-working-day toast, which is a different, correct rule).

## Acceptance
1. Overdue row's detail modal shows the corrected description.
2. Changelog postscript on replacement-home; tasks closed; note recorded against
   the round-1 F-1 wording.
