@php
    $selectClass = $selectClass ?? 'week-select';
    $showTodayBtn = $showTodayBtn ?? true;
    $disabled = $disabled ?? false;
@endphp

<div class="week-nav">
    <button class="week-arrow" onclick="{{ $prevOnclick }}" aria-label="Previous week" {{ $disabled ? 'disabled' : '' }}>&#8249;</button>
    <select class="{{ $selectClass }}" id="{{ $selectId }}" onchange="{{ $selectOnclick }}" {{ $disabled ? 'disabled' : '' }}></select>
    <button class="week-arrow" onclick="{{ $nextOnclick }}" aria-label="Next week" {{ $disabled ? 'disabled' : '' }}>&#8250;</button>
</div>
@if($showTodayBtn)
    @include('partials.ui-today-btn')
@endif
