@php
    $selectId = $selectId ?? 'venueSelect';
@endphp
<div class="venue-dd" id="{{ $selectId }}Dropdown">
    <button class="venue-dd-trigger" type="button">
        {{-- label + arrow are separate: VenueDropdown._updateTrigger() writes the
             label span only, and the chevron (shared SVG, currentColor) stays put --}}
        <span class="venue-dd-label">Select a venue</span>
        <svg class="dd-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 1l4 4 4-4"/></svg>
    </button>
    <div class="venue-dd-panel"></div>
</div>
