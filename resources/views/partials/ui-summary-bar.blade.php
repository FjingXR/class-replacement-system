@php
    $descriptions = [
        'card-total'       => 'Total number of scheduled classes for the selected period.',
        'card-hours'       => 'Total teaching hours for the selected period (each slot = 30 minutes).',
        'card-replacement' => 'Classes with a replacement lecturer assigned.',
        'card-pending'     => 'Replacement requests still waiting for approval or a volunteer.',
        'card-conflict'    => 'Scheduling clashes or classes on public holidays that need attention.',
        'card-available'   => 'Free time slots that can be booked for this venue.',
        'card-approved'    => 'Requests that have been approved and are ready to proceed.',
        'card-rejected'    => 'Requests that were declined and need alternative arrangements.',
        'card-conflicted'  => 'Classes with scheduling conflicts that need a replacement arrangement.',
        'card-venues'      => 'Number of unique venues involved in the conflicted classes.',
        'card-students'    => 'Total students impacted by the scheduling conflicts.',
        'card-duration'    => 'Total hours of class time that need to be rescheduled.',
        'card-courses'     => 'Number of different courses affected by the conflicts.',
    ];
@endphp

<div class="summary-bar" id="summaryBar">
    @foreach ($cards as $card)
        @php
            $desc = $card['description'] ?? $descriptions[$card['class']] ?? $card['label'];
        @endphp
        <div class="summary-card {{ $card['class'] }}">
            <div class="summary-card-inner">
                <div class="summary-card-front">
                    <span class="summary-value" id="{{ $card['valueId'] }}">0</span>
                    <span class="summary-label">{{ $card['label'] }}</span>
                </div>
                <div class="summary-card-back">
                    <span class="summary-description">{{ $desc }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
