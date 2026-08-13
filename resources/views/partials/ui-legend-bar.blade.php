@php
    $items = $items ?? [
        ['color' => 'var(--color-success-container)', 'label' => 'Normal Class', 'tip' => 'Scheduled class with no issues'],
        ['color' => 'var(--color-primary-container)', 'label' => 'Replacement', 'tip' => 'Approved replacement session'],
        ['color' => 'var(--color-warning-container)', 'label' => 'Pending', 'tip' => 'Replacement request awaiting approval'],
        ['color' => 'var(--color-error-container)', 'label' => 'Conflict / Public Holiday', 'tip' => 'Scheduling conflict or public holiday'],
    ];
@endphp

<div class="legend-bar">
    @foreach($items as $item)
        <div class="legend-item" @if(!empty($item['tip'])) title="{{ $item['tip'] }}" @endif>
            <span class="legend-swatch" style="background: {{ $item['color'] }};"></span>
            <span>{{ $item['label'] }}</span>
        </div>
    @endforeach
    <span class="legend-hint">Hover a colour to learn more</span>
</div>
