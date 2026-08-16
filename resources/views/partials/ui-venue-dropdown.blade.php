@php
    $selectId = $selectId ?? 'venueSelect';
@endphp
<div class="venue-dd" id="{{ $selectId }}Dropdown">
    <button class="venue-dd-trigger" type="button">Select a venue &#9662;</button>
    <div class="venue-dd-panel"></div>
</div>
