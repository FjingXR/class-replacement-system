@php
    /* Per-action button rendered after the week arrows in ui-week-nav.
       Variants:
       - default: id "todayBtn", label "Today" → current week
         (WeekNavigator.initTodayBtn → jumpToToday) — venue & timetables
       - 'earliest': id "earliestBtn", label "Earliest bookable" → first week
         with a bookable slot, pulsing its lead-time boundary day
         (WeekNavigator.jumpToEarliestBookable) — replacement-arrangement;
         the page refreshes the data-tip with the computed date at init,
         and the boundary day's grid time-col carries the badge (not here) */
    $todayLabel = $todayLabel ?? 'Today';
    $todayTip = $todayTip ?? 'Go to current week';
    $todayIcon = $todayIcon ?? 'today';
    $todayId = $todayIcon === 'earliest' ? 'earliestBtn' : 'todayBtn';
@endphp

<button class="today-btn{{ $todayIcon === 'earliest' ? ' today-btn-earliest' : '' }}" id="{{ $todayId }}" data-tip="{{ $todayTip }}" aria-label="{{ $todayTip }}">
    @if($todayIcon === 'earliest')
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <path d="m14.5 14.5 2.5 2.5-2.5 2.5"/>
        </svg>
    @else
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <circle cx="12" cy="12" r="6"/>
            <circle cx="12" cy="12" r="2"/>
        </svg>
    @endif
    {{ $todayLabel }}
</button>
