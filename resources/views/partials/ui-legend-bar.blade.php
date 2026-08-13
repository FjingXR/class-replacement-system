@php
    $items = $items ?? [
        ['color' => 'var(--color-success)', 'label' => 'Normal Class'],
        ['color' => 'var(--color-primary)', 'label' => 'Replacement'],
        ['color' => 'var(--color-warning)', 'label' => 'Pending'],
        ['color' => 'var(--color-error)', 'label' => 'Conflict / Public Holiday'],
    ];
@endphp

<div class="legend-bar">
    @foreach($items as $item)
        <div class="legend-item">
            <span class="legend-swatch" style="background: {{ $item['color'] }};"></span>
            <span>{{ $item['label'] }}</span>
        </div>
    @endforeach
</div>
