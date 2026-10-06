# Design: Replacement Arrangement — Dynamic Back Navigation (OOP)

## Technical Approach

Add a `BackNavigator` class to `ui-common.js` that handles dynamic back navigation based on URL `from` parameter. Each source page appends `from=<page-key>` when navigating to `/replacement-arrangement`.

## Data Flow

```
replacement-home → /replacement-arrangement?code=...&date=...&from=replacement-home
my-request-history → /replacement-arrangement?from=my-request-history
venue-timetable → /replacement-arrangement?venue=...&date=...&time=...&from=venue-timetable
```

## Implementation

### 1. Add `BackNavigator` class to `ui-common.js`

```javascript
class BackNavigator {
    static #routes = {
        'replacement-home': '/replacement-home-ui',
        'my-request-history': '/my-request-history-ui',
        'venue-timetable': '/venue-timetable-ui'
    };

    static getDefault() {
        return '/replacement-home-ui';
    }

    static getBackUrl() {
        const params = new URLSearchParams(window.location.search);
        const from = params.get('from');
        return BackNavigator.#routes[from] || BackNavigator.getDefault();
    }

    static navigate() {
        window.location.href = BackNavigator.getBackUrl();
    }
}
```

### 2. Source pages — add `from` parameter

| Page | Current URL | New URL |
|------|-------------|---------|
| replacement-home | `/replacement-arrangement?code=X&date=Y` | `/replacement-arrangement?code=X&date=Y&from=replacement-home` |
| my-request-history | `/replacement-arrangement` | `/replacement-arrangement?from=my-request-history` |
| venue-timetable | `/replacement-arrangement?venue=X&date=Y&time=Z` | `/replacement-arrangement?venue=X&date=Y&time=Z&from=venue-timetable` |

### 3. Replacement-arrangement — update `goBack()`

Replace hardcoded `/replacement-home-ui` with `BackNavigator.navigate()`.

## Dependencies

- `ui-common.js` — `BackNavigator` class (new)

## Risk Mitigation

- Backward compatible — existing URLs without `from` still work (fallback to default)
- No backend changes needed
- Each source page independently testable
