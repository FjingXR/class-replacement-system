# Tasks: macOS-Inspired UI Refactor

## Phase 1: Login Pages

### Task 1.1: Update login button to macOS style
- [ ] Change `border-radius` from `14px` to `16px` (pill shape)
- [ ] Change `padding` from `16px` to `10px 24px` (compact)
- [ ] Change `font-weight` from `600` to `500`
- [ ] Update `font-family` to `-apple-system, BlinkMacSystemFont, 'Inter', sans-serif`
- [ ] Remove `transform: translateY(-2px)` on hover
- [ ] Add subtle shadow on hover instead

### Task 1.2: Update login input fields
- [ ] Increase `border-radius` to `8px`
- [ ] Add subtle border color
- [ ] Update focus state to use macOS blue

### Task 1.3: Update login card
- [ ] Update shadow to macOS style (`0 4px 12px rgba(0,0,0,0.1)`)
- [ ] Increase card `border-radius` to `16px`

### Task 1.4: Update theme toggle
- [ ] Add macOS as third theme option
- [ ] Update `toggleTheme()` to cycle: macOS → dark → light → macOS
- [ ] Update icon to show 3 states (macOS logo / moon / sun)
- [ ] Update pre-paint IIFE to handle 3 themes

## Phase 2: CSS Variables + Theme System

### Task 2.1: Add macOS CSS variables to theme.css
- [ ] Add `.macos` class with macOS design tokens
- [ ] Update color palette (blue, green, gray)
- [ ] Update shadows, border-radius, transitions

### Task 2.2: Update toggleTheme() in ui-common.js
- [ ] Cycle through 3 themes
- [ ] Store preference in localStorage
- [ ] Update icon based on theme

### Task 2.3: Update pre-paint IIFEs
- [ ] Update `login-template.blade.php`
- [ ] Update `ui-template.blade.php`

## Phase 3: Shared Partials

### Task 3.1: Update nav bar
- [ ] Pill-shaped nav items
- [ ] Clean shadows
- [ ] macOS font

### Task 3.2: Update summary cards
- [ ] Rounded corners
- [ ] Subtle shadows
- [ ] Clean typography

### Task 3.3: Update modal
- [ ] macOS-style centered dialog
- [ ] Rounded corners
- [ ] Subtle shadow

### Task 3.4: Update form inputs
- [ ] Larger border-radius
- [ ] Subtle borders
- [ ] Clean focus states

## Phase 4: Core UI Templates

### Task 4.1: Update replacement-home
- [ ] Dashboard cards
- [ ] Summary stats

### Task 4.2: Update MyTimetable
- [ ] Timetable grid
- [ ] Event blocks

### Task 4.3: Update my-request-history
- [ ] Request list
- [ ] Status badges

### Task 4.4: Update replacement-arrangement
- [ ] Form inputs
- [ ] Action buttons

### Task 4.5: Update request-approval
- [ ] Approval cards
- [ ] Action buttons

### Task 4.6: Update CohortTimetable
- [ ] Timetable grid
- [ ] Navigation

### Task 4.7: Update student-my-timetable
- [ ] Student-specific layout

### Task 4.8: Update venue-timetable
- [ ] Venue-specific layout

## Phase 5: Responsive + Polish

### Task 5.1: Update mobile styles
- [ ] macOS-style stacked cards
- [ ] 48px touch targets
- [ ] Modal bottom sheet

### Task 5.2: Update tablet styles
- [ ] 3-column summary cards
- [ ] Modal max-width

### Task 5.3: Update skeleton loading
- [ ] Clean shimmer animation

### Task 5.4: Update toast notifications
- [ ] macOS-style banners
