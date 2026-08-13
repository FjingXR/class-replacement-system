@php
    $scrollId = $scrollId ?? 'gridScroll';
    $tableId = $tableId ?? 'timetable';
    $headId = $headId ?? 'tableHead';
    $bodyId = $bodyId ?? 'tableBody';
    $tableClass = $tableClass ?? 'timetable';
@endphp

<div class="grid-wrapper" @if(!empty($wrapperId)) id="{{ $wrapperId }}" @endif>
    <div class="grid-scroll" id="{{ $scrollId }}">
        <table class="{{ $tableClass }}" id="{{ $tableId }}">
            <thead id="{{ $headId }}"></thead>
            <tbody id="{{ $bodyId }}"></tbody>
        </table>
    </div>
</div>
