@php
    $scrollId = $scrollId ?? 'gridScroll';
    $tableId = $tableId ?? 'timetable';
    $headId = $headId ?? 'tableHead';
    $bodyId = $bodyId ?? 'tableBody';
@endphp

<div class="grid-wrapper" @if(!empty($wrapperId)) id="{{ $wrapperId }}" @endif>
    <div class="grid-scroll" id="{{ $scrollId }}">
        <table class="timetable" id="{{ $tableId }}">
            <thead id="{{ $headId }}"></thead>
            <tbody id="{{ $bodyId }}"></tbody>
        </table>
    </div>
</div>
