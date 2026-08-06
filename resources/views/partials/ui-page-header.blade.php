@php
    $title = $title ?? null;
    $description = $description ?? null;
    $chips = $chips ?? [];
@endphp

<div class="page-header">
    @if($title)
        <h1 class="page-title">{{ $title }}</h1>
    @endif
    @if(count($chips) > 0)
        <div class="page-chips">
            <span class="semester-chip" id="semesterChip"></span>
            @foreach($chips as $chip)
                <span class="{{ $chip['class'] ?? 'semester-chip' }}">{{ $chip['label'] }}</span>
            @endforeach
        </div>
    @else
        <span class="semester-chip" id="semesterChip"></span>
    @endif
    @if($description)
        <p class="page-desc">{{ $description }}</p>
    @endif
</div>
