@php
    $selectClass = $selectClass ?? 'week-select';
    $showTodayBtn = $showTodayBtn ?? true;
    $disabled = $disabled ?? false;
    $showPrint = $showPrint ?? false;
@endphp

<div class="week-nav">
    <button class="week-arrow" onclick="{{ $prevOnclick }}" aria-label="Previous week" {{ $disabled ? 'disabled' : '' }}>&#8249;</button>
    <select class="{{ $selectClass }}" id="{{ $selectId }}" onchange="{{ $selectOnclick }}" {{ $disabled ? 'disabled' : '' }}></select>
    <button class="week-arrow" onclick="{{ $nextOnclick }}" aria-label="Next week" {{ $disabled ? 'disabled' : '' }}>&#8250;</button>
</div>
@if($showTodayBtn)
    @include('partials.ui-today-btn')
@endif
@if($showPrint)
    {{-- Disabled stub — timetable printing is post-mock scope; no hover/tooltip by design --}}
    <button type="button" class="print-btn" disabled aria-disabled="true" aria-label="Print timetable (coming soon)">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9V2h12v7"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
        </svg>
    </button>
@endif
