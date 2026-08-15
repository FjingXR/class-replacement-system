@php
    $descriptions = [
        'card-total'       => 'Total number of <strong>scheduled classes</strong> for the selected period.',
        'card-hours'       => 'Total <strong>teaching hours</strong> for the selected period (each slot = <strong>30 minutes</strong>).',
        'card-replacement' => 'Classes with a <strong>replacement lecturer</strong> assigned.',
        'card-pending'     => 'Replacement requests still <strong>waiting for approval</strong> or a volunteer.',
        'card-conflict'    => '<strong>Scheduling clashes</strong> or classes on <strong>public holidays</strong> that need attention.',
        'card-available'   => '<strong>Free time slots</strong> that can be booked for this venue.',
        'card-approved'    => 'Requests that have been <strong>approved</strong> and are ready to proceed.',
        'card-rejected'    => 'Requests that were <strong>declined</strong> and need alternative arrangements.',
        'card-venues'      => 'Number of <strong>unique venues</strong> involved in the conflicted classes.',
        'card-students'    => 'Total <strong>students impacted</strong> by the scheduling conflicts.',
        'card-duration'    => 'Total <strong>hours of class time</strong> that need to be rescheduled.',
        'card-courses'     => 'Number of <strong>different courses</strong> affected by the conflicts.',
    ];
@endphp

<div class="summary-section" id="summarySection">
    <div class="summary-hint">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <span>Stats reflect the <strong>selected period</strong> and any active filters. Hover a card to learn more.</span>
    </div>
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
                        <span class="summary-description">{!! $desc !!}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
