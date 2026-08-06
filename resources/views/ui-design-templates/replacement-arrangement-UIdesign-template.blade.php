@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement', 'hideNav' => true, 'pageKey' => 'replacementArrangement'])

@section('title', 'Replacement Arrangement — Class Replacement System')

@section('page-styles')

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
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: none;
            background: var(--color-primary);
            color: var(--color-on-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: transform 0.15s, box-shadow var(--transition);
            box-shadow: 0 2px 8px rgba(141, 181, 230, 0.25);
        }
        .back-btn:hover { transform: translateY(-50%) scale(1.06); box-shadow: 0 4px 16px rgba(141, 181, 230, 0.35); }
        .back-btn:active { transform: translateY(-50%) scale(0.95); }
        .light .back-btn { box-shadow: 0 2px 8px rgba(26, 95, 180, 0.2); }
        .light .back-btn:hover { box-shadow: 0 4px 16px rgba(26, 95, 180, 0.3); }

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
            border-radius: 10px;
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
            width: 100%;
            max-width: 100%;
            padding: 16px 24px;
            padding-top: 68px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 16px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: background var(--transition), border-color var(--transition);
            flex-wrap: wrap;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .selector-dropdown {
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition);
            outline: none;
        }
        .selector-dropdown:hover { filter: brightness(1.1); }
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

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
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

        .grid-wrapper {
            margin-top: 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: background var(--transition), border-color var(--transition);
            position: relative;
            overflow: hidden;
        }

        .grid-scroll {
            overflow: auto;
            padding-bottom: 0;
        }

        .timetable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            min-width: 900px;
        }

        .timetable th, .timetable td {
            border: 1px solid var(--color-outline);
            text-align: center;
            vertical-align: middle;
            font-size: 13px;
            transition: background var(--transition), border-color var(--transition);
        }

        .timetable th {
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
            font-weight: 600;
            position: sticky;
            z-index: 10;
        }

        .timetable thead th {
            top: 0;
            z-index: 20;
        }

        .time-header-col {
            width: 110px;
            min-width: 110px;
            max-width: 110px;
            left: 0;
            z-index: 30 !important;
        }
        thead .time-header-col { z-index: 40 !important; }

        .time-col {
            width: 110px;
            min-width: 110px;
            max-width: 110px;
            left: 0;
            position: sticky;
            z-index: 15;
            background: var(--color-surface);
            font-weight: 600;
            text-align: center;
        }
        .time-col .day-label {
            display: block;
            font-size: 14px;
            text-align: center;
        }
        .time-col .date-label {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--color-on-surface-variant);
            margin-top: 2px;
            text-align: center;
        }
        .time-col .holiday-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: var(--color-error);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
            text-align: center;
        }

        .timetable thead th.hour-header {
            padding: 6px 4px;
            font-size: 12px;
            min-width: 80px;
            text-align: center;
        }
        .hour-header .hour-top {
            display: block;
            font-size: 13px;
            font-weight: 600;
        }
        .hour-header .hour-bottom {
            display: block;
            font-size: 10px;
            font-weight: 400;
            opacity: 0.6;
            margin-top: 1px;
        }

        .timetable td.hour-cell {
            padding: 0;
            height: 80px;
            min-width: 80px;
            cursor: default;
        }

        .cell-content {
            width: 100%;
            height: 100%;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background var(--transition), box-shadow var(--transition), transform 0.1s;
            position: relative;
        }

        .cell-available {
            background: var(--color-secondary-container);
            cursor: pointer;
        }
        .cell-available:hover {
            filter: brightness(1.2);
            z-index: 5;
        }
        .cell-available:active {
            transform: scale(0.97);
        }

        .cell-occupied {
            background: var(--color-error-container);
            cursor: not-allowed;
            height: 100%;
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

        .cell-pending {
            background: var(--color-tertiary-container);
            cursor: not-allowed;
            height: 100%;
        }

        .cell-reserved {
            background: var(--color-surface-variant);
            cursor: not-allowed;
            height: 100%;
        }

        .timetable tr:last-child td { border-bottom: none; }

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
            border-radius: 10px;
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

        .btn-outline {
            background: var(--color-surface);
            border: 1px solid var(--color-outline-strong);
            color: var(--color-on-surface-variant);
        }
        .btn-outline:hover {
            background: var(--color-surface-variant);
            border-color: var(--color-on-surface-variant);
        }

        .btn-primary {
            background: var(--color-secondary);
            color: var(--color-on-secondary);
            box-shadow: 0 2px 8px rgba(151, 230, 194, 0.2);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(151, 230, 194, 0.3);
            filter: brightness(1.05);
        }
        .light .btn-primary { box-shadow: 0 2px 8px rgba(46, 194, 126, 0.2); }
        .light .btn-primary:hover { box-shadow: 0 4px 16px rgba(46, 194, 126, 0.3); }

        .btn-primary:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            filter: none !important;
            box-shadow: none !important;
            transform: none !important;
        }
        .btn-primary:disabled:hover { filter: none !important; box-shadow: none !important; }

        .cell-time-label { display: none; }

        .btn-danger {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }
        .btn-danger:hover {
            filter: brightness(1.1);
        }

        .legend {
            margin-top: 12px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            padding: 10px 18px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            transition: background var(--transition), border-color var(--transition);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--color-on-surface-variant);
        }
        .legend-swatch {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            flex-shrink: 0;
            border: 1px solid var(--color-outline);
        }

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
            border-radius: 10px;
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
            border-radius: 6px;
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
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
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
            background: rgba(0,0,0,0.5);
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
            border-radius: 12px;
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
            border-radius: 4px;
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
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
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

        <div class="toolbar">
            <div class="toolbar-left">
                @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelector', 'selectOnclick' => 'onWeekChange()', 'selectClass' => 'selector-dropdown', 'showTodayBtn' => false])
            </div>
            <div class="toolbar-center">
                <div class="toolbar-subtitle">BMIT6767 Kylian Mbappe Dembele (L)</div>
                <div class="toolbar-meta">Mon, 31-Aug-2026, 10:00 AM - 12:00 PM (2 hours)</div>
            </div>
            <div class="toolbar-right">
                <select class="selector-dropdown" id="buildingSelector" onchange="onVenueChange()">
                </select>
            </div>
        </div>

        <div class="hint-text">Select an available (green) time slot</div>

        <div class="progress-wrapper" id="progressWrapper">
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <span class="progress-text" id="progressText">Selected 0 of 4 slots</span>
        </div>

        @include('partials.ui-grid-table')

        <div class="legend">
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-secondary);"></div>
                Available
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-primary);"></div>
                Your Current Selection
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-tertiary);"></div>
                Pending (You)
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-surface-variant);"></div>
                Reserved by Others
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-error);"></div>
                Occupied / Class on Public Holiday
            </div>
        </div>

        <div class="sel-summary" id="selSummary">
            <div class="sel-summary-header">
                <span class="sel-summary-title">Selection Summary</span>
                <span class="sel-summary-count"><strong id="summaryCount">0</strong> / 4 Selected</span>
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
                <button class="btn btn-outline" onclick="clearSelection()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Clear this Page
                </button>
                <button class="btn btn-danger" onclick="clearAll()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Clear ALL
                </button>
                <button class="btn btn-primary" onclick="proceed()">
                    Proceed
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
        const MAX_SELECTION = 4;

        const weekData = MockData.arrangementWeeks;
        const venueSlotData = MockData.venueSlots;

        let selectedSlotsByVenue = {};
        let currentWeek = 0;
        let currentVenue = 'B103';
        let selectedCells = [];
        let selectionHistory = [];
        let focusedCell = { day: null, hour: null };

        function saveCurrentWeek() {
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][currentWeek] = selectedCells.map(c => ({ day: c.day, hour: c.hour }));
        }

        function loadCurrentWeek() {
            selectedCells = [];
            const venueData = selectedSlotsByVenue[currentVenue] || {};
            const saved = venueData[currentWeek] || [];
            const body = document.getElementById('tableBody');
            saved.forEach(s => {
                const cellDiv = body.querySelector(
                    `td[data-day="${s.day}"][data-hour="${s.hour}"] .cell-content`
                );
                if (cellDiv && cellDiv.classList.contains('cell-available')) {
                    cellDiv.classList.remove('cell-available');
                    cellDiv.classList.add('cell-selected');
                    cellDiv.innerHTML = '<span class="sel-text"></span>' + timeLabelHtml(s.hour);
                    selectedCells.push({ day: s.day, hour: s.hour, el: cellDiv });
                }
            });
        }

        function getDays() {
            const idx = parseInt(document.getElementById('weekSelector').value);
            return weekData[idx].days;
        }

        function getGlobalTotal() {
            let total = 0;
            Object.values(selectedSlotsByVenue).forEach(venue => {
                Object.values(venue).forEach(slots => {
                    if (slots) total += slots.length;
                });
            });
            return total;
        }

        function updateCounter() {
            const el = document.getElementById('selCount');
            if (el) el.textContent = selectedCells.length;
            const btn = document.querySelector('.btn-primary');
            if (btn) btn.disabled = selectedCells.length === 0;
            updateSelectionSummary();
            updateProgress();
        }

        function updateSelectionSummary() {
            const allSelections = [];
            Object.keys(selectedSlotsByVenue).forEach(venueKey => {
                const venueData = selectedSlotsByVenue[venueKey];
                if (!venueData) return;
                Object.keys(venueData).forEach(weekKey => {
                    const weekIdx = parseInt(weekKey);
                    const slots = venueData[weekKey];
                    if (slots && slots.length > 0) {
                        const days = weekData[weekIdx].days;
                        slots.forEach(s => {
                            allSelections.push({
                                venue: venueKey,
                                weekIdx: weekIdx,
                                weekLabel: weekData[weekIdx].label,
                                day: days[s.day],
                                dayIdx: s.day,
                                hour: s.hour
                            });
                        });
                    }
                });
            });

            const totalCount = allSelections.length;

            document.getElementById('summaryCount').textContent = totalCount;
            const grid = document.getElementById('summaryGrid');

            if (totalCount === 0) {
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

            const sorted = allSelections.sort((a, b) => a.venue.localeCompare(b.venue) || a.weekIdx - b.weekIdx || a.dayIdx - b.dayIdx || a.hour - b.hour);

            sorted.forEach(s => {
                const startStr = hours[s.hour];
                const endStr = add30min(startStr);

                const card = document.createElement('div');
                card.className = 'sel-summary-card';
                card.dataset.venue = s.venue;
                card.dataset.week = s.weekIdx;
                card.dataset.day = s.dayIdx;
                card.dataset.hour = s.hour;
                card.innerHTML = `
                    <button class="card-remove" onclick="deselectFromSummary('${s.venue}', ${s.weekIdx}, ${s.dayIdx}, ${s.hour})" aria-label="Remove">×</button>
                    <div class="card-venue">${s.venue}</div>
                    <div class="card-day">${s.weekLabel} · ${s.day.abbr}</div>
                    <div class="card-date">${s.day.date}</div>
                    <div class="card-time">${startStr} → ${endStr}</div>
                `;
                grid.appendChild(card);
            });

            document.getElementById('infoTotal').textContent = `${totalCount} of ${MAX_SELECTION} slots`;
            const totalMins = totalCount * 30;
            const hrs = Math.floor(totalMins / 60);
            const mins = totalMins % 60;
            const durationStr = hrs > 0 ? `${hrs}h ${mins}m` : `${mins}m`;
            document.getElementById('infoDuration').textContent = durationStr;
            document.getElementById('infoBuilding').textContent = document.getElementById('buildingSelector').value;

            const tip = document.getElementById('summaryTip');
            if (getGlobalTotal() >= MAX_SELECTION) {
                tip.textContent = 'Tip: Maximum of 4 selections reached.';
            } else if (totalCount > 0) {
                tip.textContent = 'Tip: Click another green time slot to add more selections.';
            } else {
                tip.textContent = 'Tip: Click an available (green) time slot to begin.';
            }
        }

        function deselectFromSummary(venue, weekIdx, di, hi) {
            const card = document.querySelector(`.sel-summary-card[data-venue="${venue}"][data-week="${weekIdx}"][data-day="${di}"][data-hour="${hi}"]`);
            if (card) card.classList.add('card-removing');
            setTimeout(() => {
                const venueData = selectedSlotsByVenue[venue];
                if (venueData) {
                    const slots = venueData[weekIdx];
                    if (slots) {
                        const idx = slots.findIndex(s => s.day === di && s.hour === hi);
                        if (idx !== -1) slots.splice(idx, 1);
                    }
                }
                if (venue === currentVenue && weekIdx === currentWeek) {
                    const cell = selectedCells.find(c => c.day === di && c.hour === hi);
                    if (cell) {
                        cell.el.classList.remove('cell-selected');
                        cell.el.classList.add('cell-available');
                        cell.el.innerHTML = timeLabelHtml(hi);
                        selectedCells = selectedCells.filter(c => !(c.day === di && c.hour === hi));
                    }
                }
                updateCounter();
            }, 200);
        }

        function buildTimetable() {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            const days = getDays();

            const timeHeaderRow = document.createElement('tr');
            const cornerTh = document.createElement('th');
            cornerTh.className = 'time-header-col';
            cornerTh.style.cssText = 'position: sticky; left: 0; z-index: 40;';
            cornerTh.innerHTML = '<span style="font-size:13px;font-weight:600;">Day / Time</span>';
            timeHeaderRow.appendChild(cornerTh);

            for (let i = 0; i < hours.length; i += 2) {
                const th = document.createElement('th');
                th.className = 'hour-header';
                th.colSpan = 2;
                th.innerHTML = `<span class="hour-top">${hours[i]}</span><span class="hour-bottom">${hours[i + 2] || add30min(hours[i + 1])}</span>`;
                timeHeaderRow.appendChild(th);
            }
            head.appendChild(timeHeaderRow);

            days.forEach((day, di) => {
                const tr = document.createElement('tr');
                tr.dataset.dayIndex = di;

                const dayTd = document.createElement('td');
                dayTd.className = 'time-col';
                let dayHtml = `<span class="day-label">${day.abbr}</span><span class="date-label">${day.date}</span>`;
                if (day.holiday) {
                    dayHtml += `<span class="holiday-label">Public Holiday</span>`;
                }
                dayTd.innerHTML = dayHtml;
                tr.appendChild(dayTd);

                hours.forEach((h, hi) => {
                    const td = document.createElement('td');
                    td.className = 'hour-cell';
                    td.dataset.day = di;
                    td.dataset.hour = hi;

                    const div = document.createElement('div');
                    div.className = 'cell-content';

                    const isSunday = day.abbr === 'Sun';
                    const venueData = venueSlotData[currentVenue] || [];
                    const cellData = venueData.find(d => d[0] === di && d[1] === hi);

                    if (isSunday || day.holiday) {
                        div.className += ' cell-occupied';
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
                        }
                    } else {
                        div.className += ' cell-available';
                        div.addEventListener('click', () => toggleCell(di, hi, div));
                    }

                    const timeLabel = document.createElement('span');
                    timeLabel.className = 'cell-time-label';
                    timeLabel.textContent = hours[hi] + '\n' + add30min(hours[hi]);
                    div.appendChild(timeLabel);

                    td.appendChild(div);

                    tr.appendChild(td);
                });

                body.appendChild(tr);
            });

            loadCurrentWeek();
            updateCounter();
            updateWeekArrows(currentWeek >= weekData.length - 1, currentWeek <= 0);
        }

        function timeLabelHtml(hi) {
            return '<span class="cell-time-label">' + hours[hi] + '\n' + add30min(hours[hi]) + '</span>';
        }

        function toggleCell(di, hi, el) {
            if (el.classList.contains('cell-selected')) {
                pushHistory({ action: 'deselect', day: di, hour: hi, venue: currentVenue, week: currentWeek });
                el.classList.remove('cell-selected');
                el.classList.add('cell-available');
                el.innerHTML = timeLabelHtml(hi);
                selectedCells = selectedCells.filter(c => !(c.day === di && c.hour === hi));
                saveCurrentWeek();
                updateCounter();
                return;
            }

            if (el.classList.contains('cell-available')) {
                if (getGlobalTotal() >= MAX_SELECTION) {
                    showAlertModal(
                        'Selection Limit',
                        `You can only select up to ${MAX_SELECTION} slots in total across all weeks.`
                    );
                    return;
                }
                el.classList.remove('cell-available');
                el.classList.add('cell-selected');
                el.innerHTML = '<span class="sel-text"></span>' + timeLabelHtml(hi);
                selectedCells.push({ day: di, hour: hi, el });
                pushHistory({ action: 'select', day: di, hour: hi, venue: currentVenue, week: currentWeek });
                const conflict = checkConflict(di, hi);
                if (conflict) {
                    showToast('This slot overlaps with your ' + conflict + ' class');
                }
                saveCurrentWeek();
                updateCounter();
            }
        }

        function clearSelection() {
            selectedCells.forEach(c => {
                c.el.classList.remove('cell-selected');
                c.el.classList.add('cell-available');
                c.el.innerHTML = timeLabelHtml(c.hour);
            });
            selectedCells = [];
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][currentWeek] = [];
            updateCounter();
        }

        function prevWeek() {
            if (currentWeek < weekData.length - 1) {
                saveCurrentWeek();
                currentWeek++;
                document.getElementById('weekSelector').value = currentWeek;
                buildTimetable();
            }
        }

        function nextWeek() {
            if (currentWeek > 0) {
                saveCurrentWeek();
                currentWeek--;
                document.getElementById('weekSelector').value = currentWeek;
                buildTimetable();
            }
        }

        function onWeekChange() {
            saveCurrentWeek();
            currentWeek = parseInt(document.getElementById('weekSelector').value);
            buildTimetable();
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
                    for (const slot of selectedSlotsByVenue[venue][week]) {
                        const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        const dayData = weekData[week].days[slot.day];
                        const dayName = dayNames[slot.day];
                        const dateStr = dayData.date.replace(/ \d{4}$/, '');
                        const startStr = hours[slot.hour];
                        const endStr = add30min(startStr);
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
            if (selectedCells.length === 0) {
                showConfirmModal('No Selection', 'Please select at least one timeslot before proceeding.', null);
                return;
            }
            const days = weekData[currentWeek].days;
            const weekLabel = weekData[currentWeek].label;
            const listHtml = selectedCells.map(c => {
                const day = days[c.day];
                const startStr = hours[c.hour];
                const endStr = add30min(startStr);
                return `<div style="padding:3px 0;font-size:13px;">${currentVenue} · (${weekLabel}) ${day.abbr}, ${day.date} — ${to12h(startStr)} ~ ${to12h(endStr)}</div>`;
            }).join('');
            showConfirmModal(
                'Confirm Your Selection',
                `<div style="margin-bottom:12px;font-weight:500;">You are about to submit a replacement request for the following <strong>${selectedCells.length}</strong> slot(s):</div>
                 <div style="border:1px solid var(--color-outline);border-radius:8px;padding:10px 14px;max-height:200px;overflow-y:auto;">${listHtml}</div>`,
                function() {
                    hideConfirmModal();
                    const toast = buildSubmissionToastMessage();
                    selectedCells.forEach(c => {
                        c.el.classList.remove('cell-selected');
                        c.el.classList.add('cell-available');
                        c.el.innerHTML = timeLabelHtml(c.hour);
                    });
                    selectedCells = [];
                    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                    updateCounter();
                    showToast(toast.message, null, 5000, 'View \u2192', '/my-request-history-ui', toast.details);
                }
            );
        }

        function clearAll() {
            showConfirmModal(
                'Clear All Selections',
                'Are you sure you want to clear all selections across <strong>ALL</strong> weeks? This action cannot be undone.',
                function() {
                    hideConfirmModal();
                    var savedCells = selectedCells.slice();
                    var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                    selectedCells.forEach(c => {
                        c.el.classList.remove('cell-selected');
                        c.el.classList.add('cell-available');
                        c.el.innerHTML = timeLabelHtml(c.hour);
                    });
                    selectedCells = [];
                    updateCounter();
                    showToast('All selections cleared.', function() {
                        Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                        savedCells.forEach(c => {
                            c.el.classList.remove('cell-available');
                            c.el.classList.add('cell-selected');
                            c.el.innerHTML = '<span class="sel-text"></span>' + timeLabelHtml(c.hour);
                        });
                        selectedCells = savedCells;
                        updateCounter();
                    });
                }
            );
        }

        function navigateTo(url) {
            if (selectedCells.length > 0) {
                showConfirmModal(
                    'Unsaved Changes',
                    'You have selected time slots that will be lost if you leave this page. Are you sure you want to leave?',
                    function() {
                        var savedCells = selectedCells.slice();
                        var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                        hideConfirmModal();
                        selectedCells = [];
                        Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                        showToast('Selections cleared.', function() {
                            Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                            selectedCells = savedCells;
                        });
                        window.location.href = url;
                    }
                );
            } else {
                window.location.href = url;
            }
        }

        function goBack() {
            if (selectedCells.length > 0) {
                showConfirmModal(
                    'Unsaved Changes',
                    'You have selected time slots that will be lost if you leave this page. Are you sure you want to go back?',
                    function() {
                        hideConfirmModal();
                        var savedCells = selectedCells.slice();
                        var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                        Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                        selectedCells.forEach(c => {
                            c.el.classList.remove('cell-selected');
                            c.el.classList.add('cell-available');
                            c.el.innerHTML = timeLabelHtml(c.hour);
                        });
                        selectedCells = [];
                        updateCounter();
                        var navTimer = setTimeout(function() { window.location.href = '/replacement-home-ui'; }, 5000);
                        showToast('Selections cleared.', function() {
                            clearTimeout(navTimer);
                            Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                            savedCells.forEach(c => {
                                c.el.classList.remove('cell-available');
                                c.el.classList.add('cell-selected');
                                c.el.innerHTML = '<span class="sel-text"></span>' + timeLabelHtml(c.hour);
                            });
                            selectedCells = savedCells;
                            updateCounter();
                        });
                    }
                );
            } else {
                window.location.href = '/replacement-home-ui';
            }
        }

        function navigateHome() {
            navigateTo('/');
        }

        function updateProgress() {
            const count = getGlobalTotal();
            const max = MAX_SELECTION;
            const pct = max > 0 ? (count / max) * 100 : 0;
            const fill = document.getElementById('progressFill');
            const text = document.getElementById('progressText');
            if (fill) {
                fill.style.width = pct + '%';
                if (count === 0) fill.style.background = 'var(--color-outline)';
                else if (count < max) fill.style.background = 'var(--color-tertiary)';
                else fill.style.background = 'var(--color-secondary)';
            }
            if (text) text.textContent = 'Selected ' + count + ' of ' + max + ' slots';
        }

        function buildVenueDropdown() {
            const sel = document.getElementById('buildingSelector');
            const venues = MockData.venues || [];
            const cohortStudents = 35; // DFT2(S1) + DSF2(S1) combined
            sel.innerHTML = '';
            venues.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.code;
                const typeLabel = v.type === 'LectureHall' ? 'Lecture Hall' : v.type;
                opt.textContent = v.code + ' — ' + typeLabel + ' (' + v.capacity + ' seats)';
                if (v.capacity < cohortStudents) {
                    opt.textContent += ' ⚠';
                }
                sel.appendChild(opt);
            });
        }

        function checkConflict(dayIndex, hourIndex) {
            const weekLabel = weekData[currentWeek].label;
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
            const cellDiv = body.querySelector(
                'td[data-day="' + last.day + '"][data-hour="' + last.hour + '"] .cell-content'
            );
            if (!cellDiv) return;

            if (last.action === 'select') {
                cellDiv.classList.remove('cell-selected');
                cellDiv.classList.add('cell-available');
                cellDiv.innerHTML = timeLabelHtml(last.hour);
                selectedCells = selectedCells.filter(c => !(c.day === last.day && c.hour === last.hour));
                if (selectedSlotsByVenue[last.venue] && selectedSlotsByVenue[last.venue][last.week]) {
                    const slots = selectedSlotsByVenue[last.venue][last.week];
                    const idx = slots.findIndex(s => s.day === last.day && s.hour === last.hour);
                    if (idx !== -1) slots.splice(idx, 1);
                }
            } else if (last.action === 'deselect') {
                if (cellDiv.classList.contains('cell-available')) {
                    cellDiv.classList.remove('cell-available');
                    cellDiv.classList.add('cell-selected');
                    cellDiv.innerHTML = '<span class="sel-text"></span>' + timeLabelHtml(last.hour);
                    selectedCells.push({ day: last.day, hour: last.hour, el: cellDiv });
                    if (!selectedSlotsByVenue[last.venue]) selectedSlotsByVenue[last.venue] = {};
                    if (!selectedSlotsByVenue[last.venue][last.week]) selectedSlotsByVenue[last.venue][last.week] = [];
                    selectedSlotsByVenue[last.venue][last.week].push({ day: last.day, hour: last.hour });
                }
            }
            updateCounter();
            showToast('Selection undone');
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

        document.addEventListener('DOMContentLoaded', function() {
            buildVenueDropdown();
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            const sel = document.getElementById('weekSelector');
            const isMobile = window.innerWidth <= 768;
            sel.innerHTML = weekData.map((w, i) => {
                const first = w.days[0].date;
                const last = w.days[w.days.length - 1].date;
                const shortFirst = first.replace(/ \d{4}$/, '');
                const shortLast = last.replace(/ \d{4}$/, '');
                const label = isMobile
                    ? `${w.label} · ${shortFirst} ~ ${shortLast}`
                    : `${w.label} · ${first} ~ ${last}`;
                return `<option value="${i}">${label}</option>`;
            }).join('');
            sel.value = currentWeek;
            buildTimetable();

            document.addEventListener('keydown', handleKeyDown);
            initWeekKeyboardShortcuts();
        });
@endsection
