@php
    $items = $items ?? [
        ['color' => 'var(--color-success-container)', 'label' => 'Normal Class', 'tip' => 'Scheduled class with no issues'],
        ['color' => 'var(--color-primary-container)', 'label' => 'Replacement', 'tip' => 'Approved replacement session'],
        ['color' => 'var(--color-tertiary-container)', 'label' => 'Pending', 'tip' => 'Replacement request awaiting approval'],
        ['class' => 'event-conflict', 'label' => 'Conflict / Public Holiday', 'tip' => 'Scheduling conflict or public holiday — this class will not run as scheduled'],
    ];
@endphp

<div class="legend-bar">
    <span class="legend-hint" aria-hidden="true"><span class="hint-hover">Hover a colour to learn more</span><span class="hint-touch">Tap a colour to learn more</span></span>
    <div class="legend-items">
        @foreach($items as $item)
            <div class="legend-item" @if(!empty($item['tip'])) data-tip="{{ $item['tip'] }}" @endif>
                {{-- Swatch may carry a real block class (e.g. event-conflict) so the
                     legend shows the exact loud styling instead of a flat colour. --}}
                <span class="legend-swatch{{ empty($item['class']) ? '' : ' '.$item['class'] }}" @if(empty($item['class'])) style="background: {{ $item['color'] }};" @endif></span>
                <span>{{ $item['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
