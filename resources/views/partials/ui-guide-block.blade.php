@php
    $guideTitle = $guideTitle ?? 'Quick Guide';
    $guideItems = $guideItems ?? [];
@endphp

<div class="guide-block" id="guideBlock">
    <button class="guide-toggle" onclick="document.getElementById('guideBlock').classList.toggle('open')">
        <span class="guide-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </span>
        {{ $guideTitle }}
        <span class="guide-chevron">&#9662;</span>
    </button>
    <div class="guide-content">
        <ul class="guide-list">
            @foreach($guideItems as $item)
                <li>{!! $item !!}</li>
            @endforeach
        </ul>
    </div>
</div>
