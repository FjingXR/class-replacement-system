@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement', 'hideNav' => true, 'pageKey' => 'replacementArrangement'])

@section('title', 'Replacement Arrangement — Class Replacement System')

@section('page-styles')

        .cell-available { --hover-label: 'Select ?'; }

        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 64px;
            pointer-events: none;
        }
        .top-bar > * { pointer-events: auto; }

        .back-btn {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            height: 36px;
            padding: 0 14px;
            border-radius: var(--radius-xl);
            border: none;
            background: var(--color-primary);
            color: var(--color-on-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            transition: transform 0.15s, box-shadow var(--transition);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .back-btn:hover { transform: translateY(-50%) scale(1.04); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15), 0 1px 4px rgba(0, 0, 0, 0.08); }
        .back-btn:active { transform: translateY(-50%) scale(0.96); }

        .top-center {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .top-logo {
            height: 32px;
            width: auto;
            display: block;
            cursor: pointer;
        }
        .top-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--color-on-surface);
            letter-spacing: -0.3px;
        }

        .theme-toggle {
            position: fixed;
            top: 14px;
            right: 16px;
            z-index: 50;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-outline);
            background: var(--color-surface);
            color: var(--color-on-surface-variant);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: background var(--transition), transform 0.15s, border-color var(--transition);
            box-shadow: var(--shadow-sm);
        }
        .theme-toggle:hover { transform: scale(1.08); }
        .theme-toggle:active { transform: scale(0.95); }

        .app-container {
            padding-top: 68px;
        }

        .selector-dropdown {
            padding: 6px 32px 6px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--color-outline);
            background: var(--color-surface);
            color: var(--color-on-surface);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%233d5a48' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .selector-dropdown:hover { border-color: var(--color-primary); }
        .selector-dropdown:focus { border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb, 0, 77, 152), 0.15); }
        .selector-dropdown option { background: var(--color-surface); color: var(--color-on-surface); }

        .toolbar-center {
            text-align: center;
            flex: 1;
            min-width: 0;
        }
        .toolbar-center .toolbar-subtitle {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-on-surface-variant);
        }
        .toolbar-center .toolbar-meta {
            font-size: 13px;
            color: var(--color-on-surface-variant);
            margin-top: 2px;
            opacity: 0.8;
        }
        .toolbar-center .selector-dropdown {
            margin-bottom: 4px;
        }
        .toolbar-center .selector-dropdown:disabled {
            opacity: 0.7;
            cursor: var(--cursor-cancel);
        }

        .hint-text {
            text-align: center;
            font-size: 12px;
            color: var(--color-on-surface-variant);
            margin-top: 12px;
            margin-bottom: -4px;
            opacity: 0.6;
            font-weight: 400;
        }

        .cell-selected {
            background: var(--color-primary-container);
            border: 2px solid var(--color-primary);
            box-shadow: inset 0 0 0 1px var(--color-primary);
            cursor: pointer;
        }
        .cell-selected .sel-text {
            font-size: 10px;
            font-weight: 700;
            color: var(--color-on-primary-container);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.2;
            text-align: center;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cell-selected .sel-text::before {
            content: "";
        }
        .cell-selected:hover .sel-text::before {
            content: "REMOVE";
        }

        .cell-time-label { display: none; }

        .footer-area {
            margin-top: 14px;
            padding: 14px 18px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: background var(--transition), border-color var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 13px;
            color: var(--color-on-surface-variant);
        }
        .footer-left strong {
            color: var(--color-on-surface);
            font-weight: 600;
        }

        .selection-counter {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--color-on-surface-variant);
            padding: 10px 14px;
            background: var(--color-surface-variant);
            border-radius: var(--radius-sm);
            height: 40px;
        }
        .selection-counter .count-num {
            font-weight: 700;
            color: var(--color-primary);
        }
        .selection-counter .count-max {
            font-weight: 600;
            color: var(--color-on-surface-variant);
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            padding: 10px 22px;
            border-radius: var(--radius-md);
            border: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.15s, box-shadow var(--transition), background var(--transition);
        }
        .btn:active { transform: scale(0.97); }



        .btn-primary {
            background: var(--color-primary);
            color: var(--color-on-primary);
            box-shadow: 0 2px 8px rgba(151, 230, 194, 0.2);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(151, 230, 194, 0.3);
            filter: brightness(1.05);
        }
        .light .btn-primary { box-shadow: 0 2px 8px rgba(46, 194, 126, 0.2); }
        .light .btn-primary:hover { box-shadow: 0 4px 16px rgba(46, 194, 126, 0.3); }

        /* ── When block is selected, disable hover on other cells ── */
        .has-selection .cell-available { cursor: var(--cursor-cancel) !important; }
        .has-selection .cell-available:hover { filter: none; box-shadow: none; }
        .has-selection .cell-available:hover::after { opacity: 0 !important; }

        .btn-primary:disabled {
            opacity: 0.35;
            cursor: var(--cursor-cancel);
            filter: none !important;
            box-shadow: none !important;
            transform: none !important;
        }
        .btn-primary:disabled:hover { filter: none !important; box-shadow: none !important; }

        /* ===== Selection Summary ===== */
        .sel-summary {
            margin-top: 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: background var(--transition), border-color var(--transition);
            overflow: hidden;
        }

        .sel-summary-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--color-outline);
        }

        .sel-summary-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--color-on-surface);
        }

        .sel-summary-count {
            font-size: 13px;
            font-weight: 500;
            color: var(--color-on-surface-variant);
        }
        .sel-summary-count strong {
            color: var(--color-primary);
            font-weight: 700;
        }

        .sel-summary-empty {
            padding: 44px 20px;
            text-align: center;
            color: var(--color-on-surface-variant);
        }
        .sel-summary-empty svg {
            width: 44px; height: 44px;
            opacity: 0.25;
            margin-bottom: 10px;
            color: var(--color-on-surface-variant);
        }
        .sel-summary-empty p {
            font-size: 13px;
            line-height: 1.7;
        }
        .sel-summary-empty p:first-of-type {
            font-weight: 500;
            color: var(--color-on-surface);
            margin-bottom: 4px;
        }

        .sel-summary-grid {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--color-outline);
        }

        .sel-summary-card {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            padding: 12px 14px;
            position: relative;
            animation: cardSlideIn 0.2s ease;
            transition: background 0.2s, border-color 0.15s, transform 0.15s;
        }
        .sel-summary-card:hover {
            transform: translateY(-2px);
            border-color: var(--color-primary);
        }

        @keyframes cardSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes cardFadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.95); }
        }
        .sel-summary-card.card-removing {
            animation: cardFadeOut 0.2s ease forwards;
            pointer-events: none;
        }


        .card-venue {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .card-day {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .card-date {
            font-size: 11px;
            opacity: 0.7;
            margin-bottom: 6px;
        }
        .card-time {
            font-size: 12px;
            font-weight: 500;
        }

        .card-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 24px; height: 24px;
            border-radius: var(--radius-sm);
            border: none;
            background: transparent;
            color: var(--color-on-primary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            line-height: 1;
            transition: background 0.15s, color 0.15s;
            opacity: 0.5;
        }
        .card-remove:hover {
            background: var(--color-error-container);
            color: var(--color-error);
            opacity: 1;
        }

        .sel-summary-info {
            display: none;
            padding: 14px 20px;
            border-bottom: 1px solid var(--color-outline);
        }
        .info-rows {
            display: flex;
            flex-wrap: wrap;
            gap: 16px 36px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .info-label {
            font-size: 11px;
            font-weight: 500;
            color: var(--color-on-surface-variant);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-on-surface);
        }

        .sel-summary-tip {
            padding: 12px 20px;
            font-size: 12px;
            color: var(--color-on-surface-variant);
            text-align: center;
            opacity: 0.65;
        }

        @media (max-width: 1024px) {
            .sel-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .top-title { font-size: 16px; }
            .top-logo { height: 26px; }
            .toolbar-left, .toolbar-right { justify-content: center; }
            .footer-area { flex-direction: column; text-align: center; }
            .footer-left { justify-content: center; }
            .footer-right {
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
            }
            .footer-right .btn {
                padding: 8px 14px;
                font-size: 12px;
            }
            .time-header-col, .time-col { width: 14%; min-width: 120px; }
            .hour-header, .timetable td.hour-cell { min-width: 60px; }
            .header-logo { height: 44px; }
            .sel-summary-grid { grid-template-columns: 1fr; }
            .sel-summary-info .info-rows { flex-direction: column; gap: 10px; }

            /* Compact selection summary cards on mobile */
            .sel-summary-card {
                padding: 10px 12px;
            }
            .card-venue { font-size: 12px; }
            .card-day { font-size: 13px; }
            .card-date { font-size: 10px; margin-bottom: 4px; }
            .card-time { font-size: 11px; }
            .theme-toggle { display: none; }

            /* Compact timetable grid on mobile — overrides theme.css card-block layout */
            .timetable td.hour-cell {
                width: 48px;
                height: 48px;
                padding: 0;
                display: inline-block;
                position: relative;
            }
            .timetable tbody tr {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .cell-content {
                position: static;
                width: 48px;
                height: 48px;
                min-height: 48px;
                font-size: 9px;
                padding: 2px;
            }
            .cell-time-label {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                color: var(--color-on-surface-variant);
                line-height: 1.2;
                opacity: 0.7;
                pointer-events: none;
                text-align: center;
                white-space: pre-line;
            }
        }

        /* ───── F3: Progress Indicator ───── */
        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 8px 0;
        }
        .progress-bar {
            flex: 1;
            height: 8px;
            background: var(--color-outline);
            border-radius: var(--radius-xs);
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: var(--radius-xs);
            transition: width 0.3s, background 0.3s;
            background: var(--color-outline);
        }
        .progress-text {
            font-size: 12px;
            color: var(--color-on-surface-variant);
            white-space: nowrap;
        }

        /* ───── F4: Venue Capacity Badge ───── */
        .venue-warning {
            color: var(--color-error);
            margin-left: 4px;
        }

        /* ───── F7: Toast Notification — handled by shared ui-common.js ───── */

        /* ───── F1: Keyboard Shortcuts ───── */
        .cell-focused {
            outline: 2px solid var(--color-primary);
            outline-offset: -2px;
        }
        .help-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .help-overlay.active {
            display: flex;
        }
        .help-card {
            background: var(--color-surface);
            border-radius: var(--radius-lg);
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: var(--shadow-lg);
        }
        .help-card h3 {
            margin: 0 0 16px 0;
            font-size: 16px;
            color: var(--color-on-surface);
        }
        .help-card .shortcut-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: var(--color-on-surface-variant);
        }
        .help-card .shortcut-key {
            font-family: monospace;
            background: var(--color-surface-variant);
            padding: 2px 6px;
            border-radius: var(--radius-xs);
            font-size: 12px;
        }
        .help-card .help-close {
            margin-top: 16px;
            text-align: right;
        }

@endsection

@section('content')
    <div class="top-bar">
        <button class="back-btn" onclick="goBack()" aria-label="Back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <span>Back</span>
        </button>
        <div class="top-center">
            <img src="/images/logo_icon.png" alt="Logo" class="top-logo" onclick="navigateHome()">
            <span class="top-title">Replacement Arrangement</span>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>

    @include('partials.ui-page-header', [])

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Select subject</strong> — choose the course to arrange a replacement for',
                '<strong>Slot status</strong> — Available (green), Unavailable (booked / Sunday / public holiday)',
                '<strong>Select slot</strong> — click an available slot to propose it as the replacement',
                '<strong>Submit</strong> — confirm your selection to send the request for approval',
                '<strong>Back</strong> — use the back button to return to the conflict list',
            ]
        ])

        <div class="toolbar">
            <div class="toolbar-left">
                @include('partials.ui-week-nav', ['prevOnclick' => 'weekNav.prevWeek()', 'nextOnclick' => 'weekNav.nextWeek()', 'selectId' => 'weekSelector', 'selectOnclick' => 'weekNav.onWeekChange()', 'showTodayBtn' => true])
            </div>
            <div class="toolbar-center">
                <select class="selector-dropdown" id="subjectSelector" onchange="onSubjectChange()">
                    <option value="">Select a subject</option>
                </select>
                <div class="toolbar-subtitle" id="subjectInfo"></div>
                <div class="toolbar-meta" id="subjectMeta"></div>
            </div>
            <div class="toolbar-right">
                <select class="selector-dropdown" id="buildingSelector" onchange="onVenueChange()">
                </select>
            </div>
        </div>

        <div class="venue-count-note" id="venueCountNote" style="display:none; font-size:12px; color:var(--color-on-surface-variant); padding:4px 16px;"></div>

        <!-- Slot Picker: shows conflict/cancelled slots when subject selected -->
        <div class="slot-picker" id="slotPicker" style="display:none; margin: 12px 16px; padding: 12px 16px; background: var(--color-surface); border: 1px solid var(--color-outline); border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div style="font-size: 13px; font-weight: 600; color: var(--color-on-surface); margin-bottom: 8px;">Select slot to replace:</div>
            <div id="slotPickerList" style="display: flex; flex-direction: column; gap: 6px;"></div>
        </div>

        <div class="hint-text">Select an available (green) time slot</div>

        {{-- Progress bar — commented out for now
        <div class="progress-wrapper" id="progressWrapper">
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <span class="progress-text" id="progressText">Selected 0 of 4 slots</span>
        </div>
        --}}

        @include('partials.ui-grid-table')

        @include('partials.ui-legend-bar', [
            'items' => [
                ['color' => 'var(--color-success-container)', 'label' => 'Available', 'tip' => 'Free slot — click to select as replacement'],
                ['color' => 'var(--color-primary-container)', 'label' => 'Your Current Selection', 'tip' => 'Slot you have selected for the replacement'],
                ['color' => 'var(--color-tertiary-container)', 'label' => 'Pending (You)', 'tip' => 'Your replacement request awaiting approval'],
                ['color' => 'var(--color-error-container)', 'label' => 'Classes on Public Holiday / Sunday', 'tip' => 'Cannot book — falls on a public holiday or Sunday'],
                ['color' => 'var(--color-surface-variant)', 'label' => 'Reserved by Others', 'tip' => 'Cannot book — already reserved by another staff'],
            ]
        ])

        {{-- Summary Bar — commented out for now
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Slots',
                    'description' => 'All time slots shown for <strong>this venue</strong> in the selected week.'],
                ['class' => 'card-available', 'valueId' => 'sumAvailable', 'label' => 'Available',
                    'description' => '<strong>Free slots</strong> you can select as the replacement.'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending',
                    'description' => 'Your replacement requests <strong>awaiting approval</strong>.'],
                ['class' => 'card-conflict', 'valueId' => 'sumUnavailable', 'label' => 'Unavailable',
                    'description' => '<strong>Cannot select</strong> — booked by others, Sunday, or public holiday.'],
            ]
        ])
        --}}

        <div class="sel-summary" id="selSummary">
            <div class="sel-summary-header">
                <span class="sel-summary-title">Selection Summary</span>
                {{--<span class="sel-summary-count"><strong id="summaryCount">0</strong> / 4 Selected</span>--}}
            </div>
            <div class="sel-summary-empty" id="summaryEmpty">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                    <line x1="12" y1="14" x2="12" y2="18"/>
                    <line x1="10" y1="16" x2="14" y2="16"/>
                </svg>
                <p>No time slots selected.</p>
                <p>Click an available (green) time slot to begin.</p>
            </div>
            <div class="sel-summary-grid" id="summaryGrid"></div>
            <div class="sel-summary-info" id="summaryInfo">
                <div class="info-rows">
                    <div class="info-item">
                        <span class="info-label">Total Selected</span>
                        <span class="info-value" id="infoTotal">0 of 4 slots</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Duration</span>
                        <span class="info-value" id="infoDuration">0 hours</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Venue</span>
                        <span class="info-value" id="infoBuilding">B103</span>
                    </div>
                </div>
            </div>
            <div class="sel-summary-tip" id="summaryTip">Tip: Click an available (green) time slot to begin.</div>
        </div>

        <div class="footer-area">
            <div class="footer-left">
                <span><strong>Cohort:</strong> DFT2 (S1) / DSF2 (S1) / DFT2 (S1) Jefferson Ng (2310971)</span>
            </div>
            <div class="footer-right">
                {{-- Clear this Page button — disabled for now (single-block selection)
                <button class="btn btn-outline" onclick="clearSelection()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Clear this Page
                </button>
                --}}
                <button class="btn btn-danger" id="clearAllBtn" disabled onclick="clearAll()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Clear ALL
                </button>
                <button class="btn btn-primary" onclick="proceed()">
                    Submit Request
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="modal-overlay" id="confirmModal" style="display:none">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle">Confirm</span>
                <button class="modal-close" onclick="hideConfirmModal(event)">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="hideConfirmModal(event)">Cancel</button>
                <button class="btn btn-primary" id="modalConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>

<div class="help-overlay" id="helpOverlay">
    <div class="help-card">
        <h3>Keyboard Shortcuts</h3>
        <div class="shortcut-row"><span>Navigate grid</span><span class="shortcut-key">↑ ↓ ← →</span></div>
        <div class="shortcut-row"><span>Select / deselect slot</span><span class="shortcut-key">Enter / Space</span></div>
        <div class="shortcut-row"><span>Undo last selection</span><span class="shortcut-key">Ctrl+Z</span></div>
        <div class="shortcut-row"><span>Previous week</span><span class="shortcut-key">[</span></div>
        <div class="shortcut-row"><span>Next week</span><span class="shortcut-key">]</span></div>
        <div class="shortcut-row"><span>Switch venue (1-3)</span><span class="shortcut-key">Ctrl+1/2/3</span></div>
        <div class="shortcut-row"><span>Close modal / clear focus</span><span class="shortcut-key">Escape</span></div>
        <div class="shortcut-row"><span>Show this help</span><span class="shortcut-key">?</span></div>
        <div class="help-close">
            <button class="btn btn-outline" onclick="hideHelp()">Close</button>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
        // Max selectable slots — derived from the original class duration passed via
        // URL (duration in hours × 2 = 30-min slots), defaulting to 4 slots (2 hours).
        let MAX_SELECTION = 4;

        const weekData = generateWeekData();
        const venueSlotData = MockData.venueSlots;

        let selectedSlotsByVenue = {};
        var weekNav = new WeekNavigator(MockData.semester, weekData, 'weekSelector');
        weekNav.onBeforeNavigate = function() { saveCurrentWeek(); };
        let currentVenue = 'B103';
        let selectedBlock = null;          // { day, startHour, endHour } or null
        let selectionHistory = [];          // block-level undo: each entry = { action:'select'|'deselect', block:{...} }
        let focusedCell = { day: null, hour: null };

        function saveCurrentWeek() {
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][weekNav.currentWeek] = selectedBlock ? { day: selectedBlock.day, startHour: selectedBlock.startHour, endHour: selectedBlock.endHour } : null;
        }

        function loadCurrentWeek() {
            selectedBlock = null;
            clearPreview();
            const venueData = selectedSlotsByVenue[currentVenue] || {};
            const saved = venueData[weekNav.currentWeek] || null;
            const body = document.getElementById('tableBody');
            if (saved) {
                const firstTd = body.querySelector(`td[data-day="${saved.day}"][data-hour="${saved.startHour}"]`);
                if (firstTd) {
                    const cellDiv = firstTd.querySelector('.cell-content');
                    if (cellDiv && cellDiv.classList.contains('cell-available')) {
                        const span = saved.endHour - saved.startHour;
                        // Check all cells in range are still available
                        let allAvailable = true;
                        for (let h = saved.startHour; h < saved.endHour; h++) {
                            const td = body.querySelector(`td[data-day="${saved.day}"][data-hour="${h}"] .cell-content`);
                            if (!td || !td.classList.contains('cell-available')) { allAvailable = false; break; }
                        }
                        if (allAvailable) {
                            selectedBlock = { day: saved.day, startHour: saved.startHour, endHour: saved.endHour };
                            renderMergedBlock(body);
                        }
                    }
                }
            }
        }

        function getDays() {
            const idx = parseInt(document.getElementById('weekSelector').value);
            return weekData[idx].days;
        }

        function getGlobalTotal() {
            let total = 0;
            Object.values(selectedSlotsByVenue).forEach(venue => {
                Object.values(venue).forEach(block => {
                    if (block) total += (block.endHour - block.startHour);
                });
            });
            return total;
        }

        function updateCounter() {
            const el = document.getElementById('selCount');
            if (el) el.textContent = selectedBlock ? (selectedBlock.endHour - selectedBlock.startHour) : 0;
            const btn = document.querySelector('.btn-primary');
            if (btn) btn.disabled = !selectedBlock;
            const clearBtn = document.getElementById('clearAllBtn');
            if (clearBtn) clearBtn.disabled = !selectedBlock;
            updateSelectionSummary();
            updateSelectionProgress();
            updateSummaryStats();
        }

        function updateSummaryStats() {
            /* Count exactly what the grid renders, so the cards match the timetable.
               Unavailable = Occupied + Booked-by-others(Reserved) + Sunday + Public Holiday.
               Available includes the user's current selection (still a pickable slot). */
            const available = document.querySelectorAll('.timetable .cell-content.cell-available').length
                + document.querySelectorAll('.timetable .cell-content.cell-selected').length;
            const pending = document.querySelectorAll('.timetable .cell-content.cell-pending').length;
            const occupied = document.querySelectorAll('.timetable .cell-content.cell-occupied').length;
            const reserved = document.querySelectorAll('.timetable .cell-content.cell-reserved').length;
            const sunday = document.querySelectorAll('.timetable .cell-content.cell-sun').length;
            const ph = document.querySelectorAll('.timetable .cell-content.cell-ph').length;
            const unavailable = occupied + reserved + sunday + ph;

            document.getElementById('sumTotal').textContent = available + pending + unavailable;
            document.getElementById('sumAvailable').textContent = available;
            document.getElementById('sumPending').textContent = pending;
            document.getElementById('sumUnavailable').textContent = unavailable;
        }

        function updateSelectionSummary() {
            const currWeek = weekNav.currentWeek;
            const allBlocks = [];
            Object.keys(selectedSlotsByVenue).forEach(venueKey => {
                const venueData = selectedSlotsByVenue[venueKey];
                if (!venueData) return;
                const block = venueData[currWeek];
                if (block) {
                    const days = weekData[currWeek].days;
                    allBlocks.push({
                        venue: venueKey,
                        weekIdx: currWeek,
                        weekLabel: weekData[currWeek].label,
                        day: days[block.day],
                        dayIdx: block.day,
                        startHour: block.startHour,
                        endHour: block.endHour,
                        slotCount: block.endHour - block.startHour
                    });
                }
            });

            const grid = document.getElementById('summaryGrid');

            if (allBlocks.length === 0) {
                const sc = document.getElementById('summaryCount');
                if (sc) sc.textContent = '0';
                document.getElementById('summaryEmpty').style.display = '';
                grid.style.display = 'none';
                grid.innerHTML = '';
                document.getElementById('summaryInfo').style.display = 'none';
                document.getElementById('summaryTip').textContent = 'Tip: Click an available (green) time slot to begin.';
                return;
            }

            document.getElementById('summaryEmpty').style.display = 'none';
            grid.style.display = 'grid';
            grid.innerHTML = '';
            document.getElementById('summaryInfo').style.display = '';

            allBlocks.forEach(b => {
                const startStr = hours[b.startHour];
                const endStr = add30min(hours[b.endHour - 1]);
                const endDisplay = add30min(hours[b.endHour - 1]); // end of last slot

                const card = document.createElement('div');
                card.className = 'sel-summary-card';
                card.dataset.venue = b.venue;
                card.dataset.week = b.weekIdx;
                card.innerHTML = `
                    <button class="card-remove" onclick="deselectBlock()" aria-label="Remove">×</button>
                    <div class="card-venue">${b.venue}</div>
                    <div class="card-day">${b.weekLabel} · ${b.day.abbr}</div>
                    <div class="card-date">${b.day.date}</div>
                    <div class="card-time">${to12h(startStr)} – ${to12h(endDisplay)} · ${b.slotCount} slots</div>
                `;
                grid.appendChild(card);
            });

            const totalSlots = allBlocks.reduce((sum, b) => sum + b.slotCount, 0);
            const sc = document.getElementById('summaryCount');
            if (sc) sc.textContent = totalSlots;
            document.getElementById('infoTotal').textContent = `${totalSlots} of ${MAX_SELECTION} slots`;
            const totalMins = totalSlots * 30;
            const hrs = Math.floor(totalMins / 60);
            const mins = totalMins % 60;
            const durationStr = hrs > 0 ? `${hrs}h ${mins}m` : `${mins}m`;
            document.getElementById('infoDuration').textContent = durationStr;
            document.getElementById('infoBuilding').textContent = document.getElementById('buildingSelector').value;

            const tip = document.getElementById('summaryTip');
            if (getGlobalTotal() >= MAX_SELECTION) {
                tip.textContent = 'Tip: Maximum selection reached.';
            } else if (allBlocks.length > 0) {
                tip.textContent = 'Tip: Click the selected block to remove it.';
            } else {
                tip.textContent = 'Tip: Click an available (green) time slot to begin.';
            }
        }

        function deselectBlock() {
            if (!selectedBlock) return;
            const body = document.getElementById('tableBody');
            clearMergedBlock(body);
            // Restore cell-available on all cells in range
            for (let h = selectedBlock.startHour; h < selectedBlock.endHour; h++) {
                const td = body.querySelector(`td[data-day="${selectedBlock.day}"][data-hour="${h}"]`);
                if (td) {
                    const cellDiv = td.querySelector('.cell-content');
                    if (cellDiv) {
                        cellDiv.classList.remove('cell-selected');
                        cellDiv.classList.add('cell-available');
                        cellDiv.innerHTML = timeLabelHtml(h);
                    }
                }
            }
            pushHistory({ action: 'deselect', block: { ...selectedBlock } });
            selectedBlock = null;
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][weekNav.currentWeek] = null;
            // Re-enable hover on available cells
            const table = body.closest('.timetable');
            if (table) table.classList.remove('has-selection');
            updateCounter();
        }

        function renderMergedBlock(body) {
            if (!selectedBlock) return;
            const span = selectedBlock.endHour - selectedBlock.startHour;
            // Remove existing block if any
            clearMergedBlock(body);
            // Hide intermediate tds and set colSpan on first td
            for (let h = selectedBlock.startHour; h < selectedBlock.endHour; h++) {
                const td = body.querySelector(`td[data-day="${selectedBlock.day}"][data-hour="${h}"]`);
                if (!td) continue;
                if (h === selectedBlock.startHour) {
                    td.colSpan = span;
                } else {
                    td.style.display = 'none';
                }
                // Mark cells as selected
                const cellDiv = td.querySelector('.cell-content');
                if (cellDiv) {
                    cellDiv.classList.remove('cell-available');
                    cellDiv.classList.add('cell-selected');
                }
            }
            // Create event-block on first td
            const firstTd = body.querySelector(`td[data-day="${selectedBlock.day}"][data-hour="${selectedBlock.startHour}"]`);
            if (!firstTd) return;
            const div = document.createElement('div');
            div.className = 'event-block event-selection';
            div.setAttribute('tabindex', '0');
            div.addEventListener('click', () => deselectBlock());
            firstTd.appendChild(div);
            // Disable hover on other available cells
            const table = body.closest('.timetable');
            if (table) table.classList.add('has-selection');
        }

        function clearMergedBlock(body) {
            if (!body) body = document.getElementById('tableBody');
            // Remove existing event-block
            body.querySelectorAll('.event-selection').forEach(el => el.remove());
            // Reset colSpan and display on all cells
            if (selectedBlock) {
                for (let h = selectedBlock.startHour; h < selectedBlock.endHour; h++) {
                    const td = body.querySelector(`td[data-day="${selectedBlock.day}"][data-hour="${h}"]`);
                    if (td) {
                        td.colSpan = 1;
                        td.style.display = '';
                    }
                }
            }
        }

        function isCellAvailable(day, hour) {
            const body = document.getElementById('tableBody');
            const td = body.querySelector(`td[data-day="${day}"][data-hour="${hour}"] .cell-content`);
            return td && td.classList.contains('cell-available');
        }

        function canPlaceBlock(day, startHour) {
            const span = MAX_SELECTION;
            if (startHour + span > hours.length) return false;
            for (let h = startHour; h < startHour + span; h++) {
                if (!isCellAvailable(day, h)) return false;
            }
            return true;
        }

        function selectBlock(day, startHour) {
            if (selectedBlock) {
                showAlertModal('Clear current selection', 'You already have a selected block. Clear it first before selecting a new one.');
                return;
            }
            const span = MAX_SELECTION;
            if (!canPlaceBlock(day, startHour)) return;
            selectedBlock = { day, startHour, endHour: startHour + span };
            previewRange = null;
            const body = document.getElementById('tableBody');
            renderMergedBlock(body);
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][weekNav.currentWeek] = { day, startHour, endHour: startHour + span };
            pushHistory({ action: 'select', block: { ...selectedBlock } });
            updateCounter();
        }

        let previewRange = null;

        function previewBlock(day, startHour, el) {
            clearPreview();
            if (selectedBlock) return;
            const span = MAX_SELECTION;
            const body = document.getElementById('tableBody');
            const ok = canPlaceBlock(day, startHour);
            previewRange = { day, startHour, endHour: startHour + span };
            const firstTd = body.querySelector(`td[data-day="${day}"][data-hour="${startHour}"]`);
            if (!firstTd) return;
            const div = document.createElement('div');
            div.className = 'event-block event-selection-preview ' + (ok ? 'preview-ok' : 'preview-fail');
            div.style.position = 'absolute';
            div.style.top = firstTd.offsetTop + 'px';
            div.style.left = firstTd.offsetLeft + 'px';
            div.style.height = firstTd.offsetHeight + 'px';
            const table = document.getElementById('timetable');
            const borderSpacing = parseInt(getComputedStyle(table).borderSpacing) || 0;
            div.style.width = (firstTd.offsetWidth * span + (span - 1) * borderSpacing) + 'px';
            div.style.pointerEvents = 'none';
            if (!ok) firstTd.style.cursor = 'not-allowed';
            table.appendChild(div);
        }

        function clearPreview() {
            const table = document.getElementById('timetable');
            if (!table) return;
            table.querySelectorAll('.event-selection-preview').forEach(el => el.remove());
            const body = document.getElementById('tableBody');
            if (body) body.querySelectorAll('td[data-hour]').forEach(td => { td.style.cursor = ''; });
            previewRange = null;
        }

        function buildTimetable() {
            const days = getDays();

            buildTimetableGrid({
                events: [],
                days: days,
                cellRender: function(td, di, hi, day) {
                    const div = document.createElement('div');
                    div.className = 'cell-content';

                    const isSunday = day.abbr === 'Sun';
                    const venueData = venueSlotData[currentVenue] || [];
                    const cellData = venueData.find(d => d[0] === di && d[1] === hi);

                    if (isSunday || day.holiday) {
                        div.className += day.holiday ? ' cell-ph' : ' cell-sun';
                    } else if (cellData) {
                        if (cellData[2] === 1) {
                            div.className += ' cell-occupied';
                        } else if (cellData[2] === 3) {
                            div.className += ' cell-pending';
                        } else if (cellData[2] === 4) {
                            div.className += ' cell-reserved';
                        } else {
                            div.className += ' cell-available';
                            div.addEventListener('click', () => toggleCell(di, hi, div));
                            div.addEventListener('mouseenter', () => previewBlock(di, hi, div));
                            div.addEventListener('mouseleave', () => clearPreview());
                        }
                    } else {
                        div.className += ' cell-available';
                        div.addEventListener('click', () => toggleCell(di, hi, div));
                        div.addEventListener('mouseenter', () => previewBlock(di, hi, div));
                        div.addEventListener('mouseleave', () => clearPreview());
                    }

                    const timeLabel = document.createElement('span');
                    timeLabel.className = 'cell-time-label';
                    timeLabel.textContent = hours[hi] + '\n' + add30min(hours[hi]);
                    div.appendChild(timeLabel);

                    td.appendChild(div);
                }
            });

            loadCurrentWeek();
            updateCounter();
            weekNav._updateArrows();
        }

        function timeLabelHtml(hi) {
            return '<span class="cell-time-label">' + hours[hi] + '\n' + add30min(hours[hi]) + '</span>';
        }

        function toggleCell(di, hi, el) {
            // If clicking inside the current block, deselect it
            if (selectedBlock && di === selectedBlock.day && hi >= selectedBlock.startHour && hi < selectedBlock.endHour) {
                deselectBlock();
                return;
            }
            // Otherwise try to place a new block starting at this cell
            if (el.classList.contains('cell-available')) {
                selectBlock(di, hi);
            }
        }

        function clearSelection() {
            deselectBlock();
        }

        function onVenueChange() {
            saveCurrentWeek();
            currentVenue = document.getElementById('buildingSelector').value;
            buildTimetable();
        }

        let confirmCallback = null;

        function showAlertModal(title, bodyHtml) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = bodyHtml;
            const cancelBtn = document.querySelector('.modal-footer .btn-outline');
            const confirmBtn = document.getElementById('modalConfirmBtn');
            cancelBtn.style.display = 'none';
            confirmBtn.textContent = 'OK';
            confirmBtn.className = 'btn btn-primary';
            confirmBtn.onclick = function() {
                cancelBtn.style.display = '';
                confirmBtn.textContent = 'Confirm';
                confirmBtn.className = 'btn btn-primary';
                hideConfirmModal();
            };
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function showConfirmModal(title, bodyHtml, callback) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = bodyHtml;
            confirmCallback = callback;
            const confirmBtn = document.getElementById('modalConfirmBtn');
            confirmBtn.onclick = function() {
                if (confirmCallback) confirmCallback();
                else hideConfirmModal();
            };
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function hideConfirmModal(e) {
            if (e) e.stopPropagation();
            document.getElementById('confirmModal').style.display = 'none';
            confirmCallback = null;
        }

        function buildSubmissionToastMessage() {
            const slots = [];
            for (const venue in selectedSlotsByVenue) {
                for (const week in selectedSlotsByVenue[venue]) {
                    const block = selectedSlotsByVenue[venue][week];
                    if (block) {
                        const shortDayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        const dayData = weekData[week].days[block.day];
                        const dayName = shortDayNames[block.day];
                        const dateStr = dayData.date.replace(/ \d{4}$/, '');
                        const startStr = hours[block.startHour];
                        const endStr = add30min(hours[block.endHour - 1]);
                        const timeRange = to12h(startStr) + '–' + to12h(endStr);
                        slots.push(venue + ' · ' + dayName + ', ' + dateStr + ' · ' + timeRange);
                    }
                }
            }
            return {
                message: '\u2713 Submitted \u2014 Pending Approval',
                details: slots.join('  |  ')
            };
        }

        function proceed() {
            if (!selectedBlock) {
                showConfirmModal('No Selection', 'Please select at least one timeslot before proceeding.', null);
                return;
            }
            const days = weekData[weekNav.currentWeek].days;
            const weekLabel = weekData[weekNav.currentWeek].label;
            const day = days[selectedBlock.day];
            const startStr = hours[selectedBlock.startHour];
            const endStr = add30min(hours[selectedBlock.endHour - 1]);
            const slotCount = selectedBlock.endHour - selectedBlock.startHour;
            const listHtml = `<div style="padding:3px 0;font-size:13px;">${currentVenue} · (${weekLabel}) ${day.abbr}, ${day.date} — ${to12h(startStr)} ~ ${to12h(endStr)} · ${slotCount} slots</div>`;
            showConfirmModal(
                'Confirm Your Selection',
                `<div style="margin-bottom:12px;font-weight:500;">You are about to submit a replacement request for the following block:</div>
                 <div style="border:1px solid var(--color-outline);border-radius:var(--radius-sm);padding:10px 14px;max-height:200px;overflow-y:auto;">${listHtml}</div>`,
                function() {
                    hideConfirmModal();
                    const toast = buildSubmissionToastMessage();
                    deselectBlock();
                    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                    updateCounter();
                    toast.show(toast.message, null, 5000, 'View \u2192', '/my-request-history-ui', toast.details);
                }
            );
        }

        function clearAll() {
            showConfirmModal(
                'Clear All Selections',
                'Are you sure you want to clear all selections across <strong>ALL</strong> weeks? This action cannot be undone.',
                function() {
                    hideConfirmModal();
                    var savedBlock = selectedBlock ? { ...selectedBlock } : null;
                    var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                    deselectBlock();
                    updateCounter();
                    toast.show('All selections cleared.', function() {
                        Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                        if (savedBlock) {
                            selectedBlock = savedBlock;
                            const body = document.getElementById('tableBody');
                            renderMergedBlock(body);
                        }
                        updateCounter();
                    });
                }
            );
        }

        function navigateTo(url) {
            if (selectedBlock) {
                showConfirmModal(
                    'Unsaved Changes',
                    'You have a selected block that will be lost if you leave this page. Are you sure you want to leave?',
                    function() {
                        var savedBlock = selectedBlock ? { ...selectedBlock } : null;
                        var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                        hideConfirmModal();
                        Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                        deselectBlock();
                        toast.show('Selections cleared.', function() {
                            Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                            if (savedBlock) {
                                selectedBlock = savedBlock;
                                const body = document.getElementById('tableBody');
                                renderMergedBlock(body);
                            }
                        });
                        window.location.href = url;
                    }
                );
            } else {
                window.location.href = url;
            }
        }

        function goBack() {
            if (selectedBlock) {
                showConfirmModal(
                    'Unsaved Changes',
                    'You have a selected block that will be lost if you leave this page. Are you sure you want to go back?',
                    function() {
                        hideConfirmModal();
                        var savedBlock = selectedBlock ? { ...selectedBlock } : null;
                        var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                        Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                        deselectBlock();
                        updateCounter();
                        var navTimer = setTimeout(function() { BackNavigator.navigate(); }, 5000);
                        toast.show('Selections cleared.', function() {
                            clearTimeout(navTimer);
                            Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                            if (savedBlock) {
                                selectedBlock = savedBlock;
                                const body = document.getElementById('tableBody');
                                renderMergedBlock(body);
                            }
                            updateCounter();
                        });
                    }
                );
            } else {
                BackNavigator.navigate();
            }
        }

        function navigateHome() {
            navigateTo('/');
        }

        function updateSelectionProgress() {
            const count = getGlobalTotal();
            const max = MAX_SELECTION;
            const pct = max > 0 ? (count / max) * 100 : 0;
            const fill = document.getElementById('progressFill');
            const text = document.getElementById('progressText');
            if (fill) {
                fill.style.width = pct + '%';
                if (count === 0) fill.style.background = 'var(--color-outline)';
                else if (count < max) fill.style.background = 'var(--color-tertiary)';
                else fill.style.background = 'var(--color-primary)';
            }
            if (text) text.textContent = 'Selected ' + count + ' of ' + max + ' slots';
        }

        function checkConflict(dayIndex, hourIndex) {
            const weekLabel = weekData[weekNav.currentWeek].label;
            const semesterWeek = parseInt(weekLabel.replace('Week ', ''));
            const events = MockData.myTimetable.eventsByWeek[semesterWeek] || [];
            for (let i = 0; i < events.length; i++) {
                const e = events[i];
                if (e.di === dayIndex && hourIndex >= e.start && hourIndex < e.end) {
                    return e.code;
                }
            }
            return null;
        }

        function pushHistory(entry) {
            selectionHistory.push(entry);
        }

        function undoSelection() {
            if (selectionHistory.length === 0) return;
            const last = selectionHistory.pop();
            const body = document.getElementById('tableBody');

            if (last.action === 'select') {
                // Undo a block selection → deselect it
                if (selectedBlock && selectedBlock.day === last.block.day && selectedBlock.startHour === last.block.startHour) {
                    deselectBlock();
                }
            } else if (last.action === 'deselect') {
                // Undo a block deselect → re-select it
                if (!selectedBlock) {
                    selectedBlock = { ...last.block };
                    renderMergedBlock(body);
                    if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
                    selectedSlotsByVenue[currentVenue][weekNav.currentWeek] = { day: selectedBlock.day, startHour: selectedBlock.startHour, endHour: selectedBlock.endHour };
                }
            }
            updateCounter();
            toast.show('Selection undone');
        }

        function focusCell(day, hour) {
            unfocusCell();
            const body = document.getElementById('tableBody');
            const td = body.querySelector('td[data-day="' + day + '"][data-hour="' + hour + '"]');
            if (td) {
                const cellDiv = td.querySelector('.cell-content');
                if (cellDiv) cellDiv.classList.add('cell-focused');
            }
            focusedCell = { day: day, hour: hour };
        }

        function unfocusCell() {
            if (focusedCell.day !== null && focusedCell.hour !== null) {
                const body = document.getElementById('tableBody');
                const td = body.querySelector('td[data-day="' + focusedCell.day + '"][data-hour="' + focusedCell.hour + '"]');
                if (td) {
                    const cellDiv = td.querySelector('.cell-content');
                    if (cellDiv) cellDiv.classList.remove('cell-focused');
                }
            }
            focusedCell = { day: null, hour: null };
        }

        function showHelp() {
            document.getElementById('helpOverlay').classList.add('active');
        }

        function hideHelp() {
            document.getElementById('helpOverlay').classList.remove('active');
        }

        function handleKeyDown(e) {
            const modal = document.getElementById('confirmModal');
            if (modal && modal.style.display === 'flex') {
                if (e.key === 'Escape') hideConfirmModal(e);
                return;
            }
            const help = document.getElementById('helpOverlay');
            if (help && help.classList.contains('active')) {
                if (e.key === 'Escape') hideHelp();
                return;
            }

            const days = getDays();
            const maxDay = days.length - 1;
            const maxHour = hours.length - 1;

            switch (e.key) {
                case 'ArrowUp':
                    e.preventDefault();
                    if (focusedCell.day === null) focusCell(0, 0);
                    else if (focusedCell.day > 0) focusCell(focusedCell.day - 1, focusedCell.hour);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    if (focusedCell.day === null) focusCell(0, 0);
                    else if (focusedCell.day < maxDay) focusCell(focusedCell.day + 1, focusedCell.hour);
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    if (focusedCell.day === null) focusCell(0, 0);
                    else if (focusedCell.hour > 0) focusCell(focusedCell.day, focusedCell.hour - 1);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    if (focusedCell.day === null) focusCell(0, 0);
                    else if (focusedCell.hour < maxHour) focusCell(focusedCell.day, focusedCell.hour + 1);
                    break;
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    if (focusedCell.day !== null) {
                        const body = document.getElementById('tableBody');
                        const td = body.querySelector('td[data-day="' + focusedCell.day + '"][data-hour="' + focusedCell.hour + '"]');
                        if (td) {
                            const cellDiv = td.querySelector('.cell-content');
                            if (cellDiv) toggleCell(focusedCell.day, focusedCell.hour, cellDiv);
                        }
                    }
                    break;
                case 'Escape':
                    unfocusCell();
                    break;
                case '?':
                    showHelp();
                    break;
                case 'z':
                case 'Z':
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        undoSelection();
                    }
                    break;
                case '1':
                case '2':
                case '3':
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        const venueIdx = parseInt(e.key) - 1;
                        const venueSel = document.getElementById('buildingSelector');
                        if (venueIdx < venueSel.options.length) {
                            venueSel.selectedIndex = venueIdx;
                            onVenueChange();
                        }
                    }
                    break;
            }
        }

        // ───── Subject Dropdown + URL Param Reading ─────

        let currentCourse = null;
        let urlParams = {};

        function buildSubjectDropdown() {
            const sel = document.getElementById('subjectSelector');
            const courses = MockData.courses || [];
            sel.innerHTML = '<option value="">Select a subject</option>';
            courses.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.code;
                opt.textContent = c.code + ' — ' + c.name;
                sel.appendChild(opt);
            });
        }

        function onSubjectChange() {
            const code = document.getElementById('subjectSelector').value;
            const infoEl = document.getElementById('subjectInfo');
            const metaEl = document.getElementById('subjectMeta');
            const noteEl = document.getElementById('venueCountNote');

            if (!code) {
                currentCourse = null;
                infoEl.textContent = '';
                metaEl.textContent = '';
                noteEl.style.display = 'none';
                selectedOriginalSlot = null;
                renderSlotPicker([]);
                buildVenueDropdown(false);
                return;
            }

            currentCourse = (MockData.courses || []).find(c => c.code === code);
            if (!currentCourse) return;

            infoEl.textContent = currentCourse.code + ' — ' + currentCourse.name + ' (' + currentCourse.type + ')';
            metaEl.textContent = 'Cohort: ' + currentCourse.cohorts.join(', ') + ' | Students: ' + currentCourse.studentCount;

            // Extract and render conflict/cancelled slots for this subject
            const slots = extractSlotsForSubject(code);
            renderSlotPicker(slots);
            
            buildVenueDropdown(true);
        }

        function buildVenueDropdown(filterByCourse) {
            const sel = document.getElementById('buildingSelector');
            const venues = MockData.venues || [];
            const noteEl = document.getElementById('venueCountNote');
            sel.innerHTML = '';

            let filtered = venues;
            if (filterByCourse && currentCourse) {
                const allowedType = currentCourse.type === 'L' ? ['LectureHall', 'Tutorial'] : ['Tutorial'];
                filtered = venues.filter(v => {
                    const typeOk = v.type === 'Tutorial' || (currentCourse.type === 'L' && v.type === 'LectureHall');
                    const capOk = v.capacity >= currentCourse.studentCount;
                    return typeOk && capOk;
                });
            }

            filtered.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.code;
                const typeLabel = v.type === 'LectureHall' ? 'Lecture Hall' : v.type;
                opt.textContent = v.code + ' — ' + typeLabel + ' (' + v.capacity + ' seats)';
                sel.appendChild(opt);
            });

            if (filterByCourse && currentCourse) {
                noteEl.textContent = 'Showing ' + filtered.length + ' venues that fit ' + currentCourse.studentCount + ' students';
                noteEl.style.display = '';
            } else {
                noteEl.style.display = 'none';
            }
        }

        // ───── Slot Picker (conflict/cancelled slots for selected subject) ─────

        let selectedOriginalSlot = null;
        const slotDayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        function extractSlotsForSubject(courseCode) {
            const slots = [];
            const cohortTimetable = MockData.cohortTimetable || {};
            
            Object.keys(cohortTimetable).forEach(weekKey => {
                const weekData = cohortTimetable[weekKey];
                if (!weekData || !Array.isArray(weekData)) return;
                
                weekData.forEach(event => {
                    if (event.code === courseCode && (event.status === 'conflict' || event.status === 'cancelled')) {
                        slots.push({
                            week: parseInt(weekKey),
                            day: event.di,
                            dayName: slotDayNames[event.di] || 'Unknown',
                            start: event.start,
                            end: event.end,
                            venue: event.venue,
                            status: event.status,
                            name: event.name,
                            type: event.type
                        });
                    }
                });
            });
            
            return slots.sort((a, b) => a.week - b.week || a.day - b.day || a.start - b.start);
        }

        function renderSlotPicker(slots) {
            const picker = document.getElementById('slotPicker');
            const list = document.getElementById('slotPickerList');
            
            if (!slots || slots.length === 0) {
                picker.style.display = 'none';
                return;
            }
            
            list.innerHTML = '';
            slots.forEach((slot, index) => {
                const startStr = to12h(hours[slot.start]);
                const endStr = to12h(hours[slot.end + 1] || add30min(hours[slot.end]));
                const statusClass = slot.status === 'conflict' ? 'status-conflict' : 'status-cancelled';
                const statusLabel = slot.status === 'conflict' ? 'Conflict' : 'Cancelled';
                
                const div = document.createElement('div');
                div.className = 'slot-picker-item';
                div.style.cssText = 'display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid var(--color-outline); border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s;';
                div.innerHTML = `
                    <input type="radio" name="originalSlot" id="slot_${index}" value="${index}" style="cursor: pointer;">
                    <label for="slot_${index}" style="flex: 1; cursor: pointer; font-size: 13px;">
                        Week ${slot.week} · ${slot.dayName} ${startStr} – ${endStr} @ ${slot.venue}
                    </label>
                    <span class="badge ${statusClass}" style="font-size: 11px; padding: 2px 8px; border-radius: var(--radius-lg); background: ${slot.status === 'conflict' ? 'var(--color-error-container)' : 'var(--color-surface-variant)'}; color: ${slot.status === 'conflict' ? 'var(--color-on-error-container)' : 'var(--color-on-surface-variant)'};">
                        ${statusLabel}
                    </span>
                `;
                
                div.addEventListener('click', () => {
                    document.getElementById(`slot_${index}`).checked = true;
                    selectedOriginalSlot = slot;
                    updateHintText();
                });
                
                div.addEventListener('mouseenter', () => {
                    if (selectedOriginalSlot !== slot) {
                        div.style.background = 'var(--color-surface-variant)';
                    }
                });
                
                div.addEventListener('mouseleave', () => {
                    if (selectedOriginalSlot !== slot) {
                        div.style.background = '';
                    }
                });
                
                list.appendChild(div);
            });
            
            picker.style.display = '';
        }

        function updateHintText() {
            const hintEl = document.getElementById('hintText');
            if (selectedOriginalSlot) {
                hintEl.textContent = 'Now select a new venue + time slot on the grid below';
            } else {
                hintEl.textContent = 'Select an available (green) time slot';
            }
        }

        function readUrlParams() {
            const params = new URLSearchParams(window.location.search);
            urlParams = {
                code: params.get('code'),
                cohort: params.get('cohort'),
                venue: params.get('venue'),
                date: params.get('date'),
                time: params.get('time'),
                day: params.get('day'),
                start: params.get('start'),
                end: params.get('end'),
                originalVenue: params.get('originalVenue'),
                duration: params.get('duration')
            };
            return urlParams;
        }

        function applyUrlParams() {
            const sel = document.getElementById('subjectSelector');
            if (urlParams.code) {
                sel.value = urlParams.code;
                sel.disabled = true;
                sel.dispatchEvent(new Event('change'));
            }
            if (urlParams.venue) {
                const venueSel = document.getElementById('buildingSelector');
                const venueOpt = Array.from(venueSel.options).find(o => o.value === urlParams.venue);
                if (venueOpt) {
                    venueSel.value = urlParams.venue;
                    onVenueChange();
                }
            }
            
            // Auto-select original slot if day/start/end/originalVenue params provided
            if (urlParams.code && urlParams.day !== null && urlParams.start !== null && urlParams.originalVenue) {
                const slots = extractSlotsForSubject(urlParams.code);
                const matchingSlot = slots.find(s => 
                    s.day === parseInt(urlParams.day) && 
                    s.start === parseInt(urlParams.start) && 
                    s.venue === urlParams.originalVenue
                );
                if (matchingSlot) {
                    selectedOriginalSlot = matchingSlot;
                    const slotIndex = slots.indexOf(matchingSlot);
                    const radio = document.getElementById(`slot_${slotIndex}`);
                    if (radio) {
                        radio.checked = true;
                        updateHintText();
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            readUrlParams();
            // If the original class duration was passed in (hours), cap the selection
            // at that many 30-min slots so the replacement matches the class length.
            if (urlParams.duration && !isNaN(parseFloat(urlParams.duration))) {
                const hrs = parseFloat(urlParams.duration);
                if (hrs > 0) MAX_SELECTION = Math.round(hrs * 2);
            }
            buildSubjectDropdown();
            buildVenueDropdown();
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            /* restore the previously saved week (localStorage), else falls back to week 1 */
            weekNav.load();
            populateWeekSelect('weekSelector', {
                ranges: false,
                selected: weekNav.currentWeek,
                labelFn: function(w, i, isMobile) {
                    const first = w.days[0].date;
                    const last = w.days[w.days.length - 1].date;
                    if (isMobile) {
                        const shortFirst = first.replace(/ \d{4}$/, '');
                        const shortLast = last.replace(/ \d{4}$/, '');
                        return w.label + ' \u00B7 ' + shortFirst + ' ~ ' + shortLast;
                    }
                    return w.label + ' \u00B7 ' + first + ' ~ ' + last;
                }
            });
            buildTimetable();
            applyUrlParams();

            document.addEventListener('keydown', handleKeyDown);
            initWeekKeyboardShortcuts();

            document.getElementById('todayBtn')?.addEventListener('click', function() {
                try {
                    saveCurrentWeek();
                    weekNav.jumpToToday();
                    weekNav.save();
                    const sel = document.getElementById('weekSelector');
                    if (sel) {
                        sel.value = weekNav.currentWeek;
                        buildTimetable();
                    }
                } catch (err) {
                    window.__todayBtnError = err.message + ' | ' + (err.stack || '').split('\n').slice(0,3).join(' ');
                }
            });
        });
@endsection
