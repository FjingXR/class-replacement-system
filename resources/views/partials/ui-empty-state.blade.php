@php
    $id = $id ?? 'emptyState';
    $text = $text ?? '';
    $icon = $icon ?? '<svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
    $ctaLabel = $ctaLabel ?? null;
    $ctaOnclick = $ctaOnclick ?? null;
    $ctaStyle = $ctaStyle ?? 'display:none';
@endphp

<div class="empty-state" id="{{ $id }}" style="display:none">
    {!! $icon !!}
    <h3 class="empty-title" id="emptyTitle">{{ $title }}</h3>
    @if($text)
        <p class="empty-text" id="emptyText">{{ $text }}</p>
    @endif
    @if($ctaLabel)
        <button class="empty-cta" id="emptyCta" onclick="{{ $ctaOnclick }}" style="{{ $ctaStyle }}">{{ $ctaLabel }}</button>
    @endif
</div>
