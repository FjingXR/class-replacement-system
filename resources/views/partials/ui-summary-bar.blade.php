<div class="summary-bar" id="summaryBar">
    @foreach ($cards as $card)
        <div class="summary-card {{ $card['class'] }}">
            <span class="summary-value" id="{{ $card['valueId'] }}">0</span>
            <span class="summary-label">{{ $card['label'] }}</span>
        </div>
    @endforeach
</div>
