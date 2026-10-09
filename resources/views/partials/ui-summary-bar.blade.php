@php
    $descriptions = [
        'card-total'       => 'Total number of <span class="info-keyword">scheduled classes</span> for the selected period.',
        'card-hours'       => 'Total <span class="info-keyword">teaching hours</span> for the selected period (each slot = <strong>30 minutes</strong>).',
        'card-replacement' => 'Classes with a <span class="info-keyword">replacement lecturer</span> assigned.',
        'card-pending'     => 'Replacement requests still <span class="warn-keyword">waiting for approval</span> or a volunteer.',
        'card-conflict'    => '<span class="warn-keyword">Scheduling clashes</span> or classes on <strong>public holidays</strong> that need attention.',
        'card-available'   => '<span class="info-keyword">Free time slots</span> that can be booked for this venue.',
        'card-approved'    => 'Requests that have been <span class="info-keyword">approved</span> and are ready to proceed.',
        'card-rejected'    => 'Requests that were <span class="warn-keyword">declined</span> and need alternative arrangements.',
        'card-venues'      => 'Number of <span class="info-keyword">unique venues</span> involved in the conflicted classes.',
        'card-students'    => 'Total <span class="info-keyword">students impacted</span> by the scheduling conflicts.',
        'card-duration'    => 'Total <span class="info-keyword">hours of class time</span> that need to be rescheduled.',
        'card-courses'     => 'Number of <span class="info-keyword">different courses</span> affected by the conflicts.',
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
