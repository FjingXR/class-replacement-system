@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement', 'hideNav' => true, 'pageKey' => 'replacementArrangement'])

@section('title', 'Replacement Arrangement — Class Replacement System')

@section('page-styles')

        /* Hover chip removed — the preview block carries the message instead */
        .cell-available::after { content: none; }
        .event-selection-preview .preview-label {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 3px 12px;
            border-radius: 999px;
            background: var(--color-tertiary);
            color: var(--color-on-tertiary);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .event-selection-preview.preview-fail .preview-label {
            background: var(--color-error);
            color: var(--color-on-error);
        }

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

        /* ── Toolbar Restructure (2-zone) ── */
        .toolbar-restructure {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .toolbar-primary {
            display: flex;
            flex-direction: row; /* slot picker sits to the right of the subject selector */
            flex-wrap: wrap; /* drops below the select when the row is too tight */
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 280px;
        }
        .toolbar-primary .selector-dropdown {
            flex: 1 1 220px;
            min-width: 0;
            max-width: 235px;
        }
        .toolbar-primary .toolbar-subtitle {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-on-surface);
        }
        /* ── Conflict Schedule context strip (icon-led segments) ── */
        .conflict-strip-wrap { width: 100%; margin: 0 0 12px; }
        .conflict-strip {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            padding: 7px 12px;
        }
        .cs-head {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--color-error-container);
            color: var(--color-on-error-container);
            font-size: 13px;
            font-weight: 600;
            padding: 5px 11px 5px 8px;
            border-radius: var(--radius-sm);
            white-space: nowrap;
        }
        .cs-head svg { flex-shrink: 0; }
        .cs-seg {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-width: 0;
            font-size: 13px;
            color: var(--color-on-surface);
        }
        .cs-seg svg { flex-shrink: 0; color: var(--color-on-surface-variant); }
        .cs-val { min-width: 0; }
        .cs-val strong { color: var(--color-on-bg); font-weight: 600; }
        .cs-none { color: var(--color-on-surface-variant); font-style: italic; }
        .cs-hint { font-size: 12px; margin-left: 4px; font-style: normal; }
        .cs-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: auto;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        @media (max-width: 900px) {
            .cs-badge { margin-left: 0; }
        }

        /* ── Booking intent banner (arrival from venue-timetable "Book" action) ── */
        .booking-intent {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            border: 1px solid color-mix(in srgb, var(--color-primary) 35%, transparent);
            border-radius: var(--radius-md);
            padding: 8px 12px;
            font-size: 13px;
            margin: 0 0 10px;
        }
        .booking-intent svg { flex-shrink: 0; }
        .booking-intent strong { font-weight: 600; }
        .bi-close {
            margin-left: auto;
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            padding: 2px 6px;
            border-radius: var(--radius-sm);
        }
        /* conflict variant: booked slot can't be auto-selected */
        .booking-intent-manual {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
            border-color: color-mix(in srgb, var(--color-error) 35%, transparent);
        }
        .semester-chip { display: none; }
        .page-header .semester-chip { display: none; }
        .toolbar-filters {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .toolbar-center {
            display: flex;
            align-items: center;
            gap: 8px; /* venue trigger + favourite need breathing room */
            flex: 0 0 auto; /* content-sized — leftover space goes to the subject/slot pair */
        }
        .toolbar-center .venue-dd-trigger {
            min-width: 0;
            max-width: 204px; /* natural width of the widest current label + arrow */
        }

        /* ── Slot Dropdown (custom) ── */
        .slot-dd {
            position: relative;
            flex: 1 1 180px;
            max-width: 260px;
        }
        .slot-dd-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            background: var(--color-surface);
            color: var(--color-on-surface);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
            text-align: left;
        }
        .slot-dd-trigger span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .slot-dd-trigger:hover {
            border-color: var(--color-primary);
        }
        .slot-dd-trigger svg {
            margin-left: auto;
            flex-shrink: 0;
            opacity: 0.6;
        }
        .slot-dd-panel {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            min-width: 320px;
            max-height: 280px;
            overflow-y: auto;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 50;
        }
        .slot-dd-panel.open {
            display: block;
        }
        .slot-dd-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            cursor: pointer;
            transition: background 0.12s;
            border-bottom: 1px solid var(--color-outline-variant);
        }
        .slot-dd-item:last-child {
            border-bottom: none;
        }
        .slot-dd-item:hover {
            background: var(--color-surface-variant);
        }
        .slot-dd-item.selected {
            background: var(--color-primary-container);
        }
        .slot-dd-item-label {
            flex: 1;
            font-size: 13px;
            color: var(--color-on-surface);
        }
        .slot-dd-item-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: var(--radius-lg);
        }
        .slot-dd-item-star {
            cursor: pointer;
            font-size: 14px;
            opacity: 0.3;
            transition: opacity 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .slot-dd-item-star.active {
            opacity: 1;
            color: var(--color-tertiary);
        }
        .slot-dd-item-star:hover {
            opacity: 0.8;
        }

        .slot-dd-item-date {
            font-size: 12px;
            color: var(--color-on-surface-variant);
            margin-left: auto;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .slot-dd-tooltip {
            display: none;
            position: absolute;
            padding: 6px 12px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            font-size: 12px;
            color: var(--color-on-surface);
            white-space: nowrap;
            pointer-events: none;
            z-index: 60;
        }

        .cell-selected {
            background: var(--color-primary-container);
            border: 2px solid var(--color-primary);
            box-shadow: inset 0 0 0 1px var(--color-primary);
            cursor: pointer;
        }
        /* Selected block: time range at rest; hover/focus shows the theme's
           danger pill (theme.css .event-block.event-selection::after) */
        .event-selection .ev-time-label {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: var(--color-on-primary-container);
            transition: opacity 0.15s;
        }
        .event-selection:hover .ev-time-label,
        .event-selection:focus-visible .ev-time-label { opacity: 0; }

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
            transition: transform 0.15s, filter 0.15s, box-shadow var(--transition);
        }
        /* Hover/active mirror the shared .btn-action design (replacement-home) */
        .btn-primary:hover {
            filter: brightness(1.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .btn-primary:active {
            transform: scale(0.97);
        }

        /* ── When block is selected, disable hover on other cells ── */
        .has-selection .cell-available { cursor: var(--cursor-cancel) !important; }
        .has-selection .cell-available:hover { filter: none; box-shadow: none; }
        .has-selection .cell-available:hover::after { opacity: 0 !important; }

        /* Grid locked until a subject is picked — free cells read as not-allowed */
        .timetable.no-subject .cell-available { cursor: var(--cursor-cancel); }
        .attention-pulse { animation: attention-pulse 1.2s ease-out 1; }
        @keyframes attention-pulse {
            0%   { box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-primary) 45%, transparent); }
            70%  { box-shadow: 0 0 0 8px color-mix(in srgb, var(--color-primary) 0%, transparent); }
            100% { box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-primary) 0%, transparent); }
        }

        /* Footer button tooltips — the wrapper span keeps hover alive while the
           inner button is disabled (disabled controls swallow mouse events) */
        .btn-tip { display: inline-flex; cursor: var(--cursor-cancel); }
        .btn-tip .btn:disabled { pointer-events: none; }

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
        }
        .sel-summary-tip .tip-action {
            color: var(--color-primary);
            font-weight: 700;
        }
        html.dark .sel-summary-tip .tip-action {
            color: var(--color-on-primary-container); /* dark navy primary is too dim on the dark surface */
        }
        .sel-summary-tip .tip-success {
            color: var(--color-success);
            font-weight: 700;
        }
        html.dark .sel-summary-tip .tip-success {
            color: var(--color-on-success-container); /* dark green success is too dim on the dark surface */
        }

        @media (max-width: 1024px) {
            .sel-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            /* the 7×22 grid cannot squeeze into a phone viewport — scroll it
               instead of clipping (selection needs the real grid) */
            .grid-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .grid-scroll table { min-width: 820px; }
            .top-title { font-size: 16px; }
            .top-logo { height: 26px; }
            .toolbar-restructure {
                flex-direction: column;
                align-items: stretch;
            }
            .toolbar-primary {
                min-width: 0;
                width: 100%;
            }
            .toolbar-primary .selector-dropdown {
                flex-basis: 100%;
                max-width: 100%;
            }
            .slot-dd {
                flex-basis: 100%;
                max-width: 100%;
            }
            .toolbar-filters {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                flex-basis: auto; /* column parent — basis would otherwise be height */
            }
            .toolbar-center {
                flex-direction: column;
                align-items: stretch;
            }
            .slot-dd-trigger {
                min-width: 0;
                width: 100%;
            }
            .slot-dd-panel {
                min-width: 100%;
            }
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

        <div class="booking-intent-wrap" id="bookingIntent"></div>

        <div class="conflict-strip-wrap" id="subjectInfo"></div>

        <div class="toolbar toolbar-restructure">
            <div class="toolbar-primary">
                <select class="selector-dropdown" id="subjectSelector" onchange="onSubjectChange()">
                    <option value="">Select a subject</option>
                </select>

                {{-- Slot Picker: custom dropdown (button + panel) --}}
                <div class="slot-dd" id="slotPicker" style="display:none;">
                    <button class="slot-dd-trigger" type="button" id="slotTrigger" onclick="toggleSlotPanel()">
                        <span id="slotTriggerText">Select a slot to replace</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="slot-dd-panel" id="slotPanel"></div>
                    <div class="slot-dd-tooltip" id="slotTooltip"></div>
                </div>
            </div>
            <div class="toolbar-center">
                @include('partials.ui-venue-dropdown', ['selectId' => 'buildingSelector'])
                <button class="fav-btn" id="favStar" data-tip="Add to Favourites">&#9734;</button>
            </div>
            <div class="toolbar-filters">
                @include('partials.ui-week-nav', ['prevOnclick' => 'weekNav.prevWeek()', 'nextOnclick' => 'weekNav.nextWeek()', 'selectId' => 'weekSelector', 'selectOnclick' => 'weekNav.selectWeek(parseInt(this.value, 10))', 'showTodayBtn' => true, 'showPrint' => true])
            </div>
        </div>

        {{-- Progress bar — commented out for now
        <div class="progress-wrapper" id="progressWrapper">
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <span class="progress-text" id="progressText">Selected 0 of 4 slots</span>
        </div>
        --}}

        <div class="venue-count-note" id="venueCountNote" style="display:none; font-size:12px; color:var(--color-on-surface-variant); padding:4px 0;"></div>

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
            <div class="sel-summary-tip" id="summaryTip">Tip: Click an <span class="tip-success">available (green)</span> time slot to begin.</div>
        </div>

        <div class="footer-area">
            <div class="footer-left">
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
                <span class="btn-tip" id="clearTip" data-tip="Nothing to clear yet — select a time slot first">
                    <button class="btn btn-danger" id="clearAllBtn" disabled onclick="clearAll()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Clear ALL
                    </button>
                </span>
                <span class="btn-tip" id="submitTip" data-tip="Select a time slot first to enable submission">
                    <button class="btn btn-primary" onclick="proceed()">
                        Submit Request
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </span>
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

<div class="help-overlay" id="helpOverlay" onclick="if(event.target===this)hideHelp()">
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
        /* the size of ONE block (slots) — derived from the original class
           duration via URL; the multi-week BUDGET stays MAX_SELECTION */
        let BLOCK_SPAN = 4;

        const weekData = generateWeekData();
        const venueSlotData = MockData.venueSlots;

        let selectedSlotsByVenue = {};
        var weekNav = new WeekNavigator(MockData.semester, weekData, 'weekSelector', 'arrangementWeek');
        weekNav.onBeforeNavigate = function() { saveCurrentWeek(); };
        /* restore-after-week-jump already happens inside buildTimetable()
           (loadCurrentWeek) — no after-navigate hook needed here */
        let currentVenue = 'B103';
        let venueDropdown = null;
        let selectedBlock = null;          // { day, startHour, endHour } or null
        let selectionHistory = [];          // block-level undo: each entry = { action:'select'|'deselect', block:{...} }
        let focusedCell = { day: null, hour: null };
        let revertingChange = false;        // suppress confirm guards while restoring a dropdown on cancel
        let lastSubject = '';               // last committed subject code
        let lastSlotIndex = null;           // last committed original-slot pick index
        let slotPickerSlots = [];           // currently rendered original-slot list (for cancel restore)

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
            /* multi-week model: the counter and button states reflect ALL saved
               selections, not just the week on screen */
            const total = getGlobalTotal();
            const el = document.getElementById('selCount');
            if (el) el.textContent = total;
            const has = total > 0;
            const btn = document.querySelector('.btn-primary');
            if (btn) btn.disabled = !has;
            const clearBtn = document.getElementById('clearAllBtn');
            if (clearBtn) clearBtn.disabled = !has;
            /* Footer button tooltips — state-aware (buttons render above via the shared
               data-tip system; the wrapper span keeps hover alive while disabled) */
            const submitTip = document.getElementById('submitTip');
            if (submitTip) submitTip.setAttribute('data-tip', has
                ? 'Send your replacement request for approval'
                : 'Select a time slot first to enable submission');
            const clearTip = document.getElementById('clearTip');
            if (clearTip) clearTip.setAttribute('data-tip', has
                ? 'Clear all your selected slots'
                : 'Nothing to clear yet — select a time slot first');
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

            const sumTotal = document.getElementById('sumTotal');
            if (sumTotal) {
                sumTotal.textContent = available + pending + unavailable;
                document.getElementById('sumAvailable').textContent = available;
                document.getElementById('sumPending').textContent = pending;
                document.getElementById('sumUnavailable').textContent = unavailable;
            }
        }

        function updateSelectionSummary() {
            /* multi-week model: the panel lists EVERY saved selection (any week),
               matching the Submit dialog and the enabled Submit button */
            const allBlocks = [];
            Object.keys(selectedSlotsByVenue).forEach(venueKey => {
                const venueData = selectedSlotsByVenue[venueKey];
                if (!venueData) return;
                Object.keys(venueData).forEach(weekKey => {
                    const block = venueData[weekKey];
                    if (!block) return;
                    const days = weekData[weekKey].days;
                    allBlocks.push({
                        venue: venueKey,
                        weekIdx: parseInt(weekKey, 10),
                        weekLabel: weekData[weekKey].label,
                        day: days[block.day],
                        dayIdx: block.day,
                        startHour: block.startHour,
                        endHour: block.endHour,
                        slotCount: block.endHour - block.startHour
                    });
                });
            });
            allBlocks.sort((a, b) => a.weekIdx - b.weekIdx || a.dayIdx - b.dayIdx || a.startHour - b.startHour);

            const grid = document.getElementById('summaryGrid');

            if (allBlocks.length === 0) {
                const sc = document.getElementById('summaryCount');
                if (sc) sc.textContent = '0';
                document.getElementById('summaryEmpty').style.display = '';
                grid.style.display = 'none';
                grid.innerHTML = '';
                document.getElementById('summaryInfo').style.display = 'none';
                document.getElementById('summaryTip').innerHTML = 'Tip: Click an <span class="tip-success">available (green)</span> time slot to begin.';
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
                    <button class="card-remove" onclick="removeSavedBlock('${b.venue}', ${b.weekIdx})" data-tip="Remove this selection" aria-label="Remove">×</button>
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
            document.getElementById('infoBuilding').textContent = venueDropdown ? venueDropdown.getSelected() : currentVenue;

            const tip = document.getElementById('summaryTip');
            if (getGlobalTotal() >= MAX_SELECTION) {
                tip.innerHTML = 'Tip: Maximum selection reached. <span class="tip-action">Click the selected block to remove it.</span>';
            } else if (allBlocks.length > 0) {
                tip.innerHTML = 'Tip: <span class="tip-action">Click the selected block to remove it.</span>';
            } else {
                tip.innerHTML = 'Tip: Click an <span class="tip-success">available (green)</span> time slot to begin.';
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
            pushHistory({ action: 'deselect', block: { ...selectedBlock }, venue: currentVenue.code || currentVenue, week: weekNav.currentWeek });
            selectedBlock = null;
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][weekNav.currentWeek] = null;
            // Re-enable hover on available cells
            const table = body.closest('.timetable');
            if (table) table.classList.remove('has-selection');
            updateCounter();
        }

        // Like deselectBlock, but preserves saved data in selectedSlotsByVenue
        // so loadCurrentWeek can restore the selection when navigating back.
        function clearVisualSelection() {
            if (!selectedBlock) return;
            const body = document.getElementById('tableBody');
            clearMergedBlock(body);
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
            selectedBlock = null;
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
            const startStr = hours[selectedBlock.startHour];
            const endStr = add30min(hours[selectedBlock.endHour - 1]);
            div.innerHTML = `<span class="ev-time-label">${to12h(startStr)} – ${to12h(endStr)}</span>`;
            div.addEventListener('click', () => userDeselectSelectedBlock());
            firstTd.appendChild(div);
            // Disable hover on other available cells
            const table = body.closest('.timetable');
            if (table) table.classList.add('has-selection');
            // Blocked-state hint: green slots explain why they can't be picked (renders above
            // the cell via the shared data-tip tooltip) until the selection is cleared
            body.querySelectorAll('.cell-content.cell-available').forEach(c => {
                c.setAttribute('data-tip', 'Clear your selection first — click it to remove');
            });
        }

        function clearMergedBlock(body) {
            if (!body) body = document.getElementById('tableBody');
            // Remove existing event-block
            body.querySelectorAll('.event-selection').forEach(el => el.remove());
            body.querySelectorAll('.cell-content[data-tip]').forEach(c => c.removeAttribute('data-tip'));
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
            const span = BLOCK_SPAN;
            if (startHour + span > hours.length) return false;
            for (let h = startHour; h < startHour + span; h++) {
                if (!isCellAvailable(day, h)) return false;
            }
            return true;
        }

        function selectBlock(day, startHour) {
            if (selectedBlock) {
                showAlertModal('Clear current selection', 'You already have a selected block. Clear it first before selecting a new one.');
                return false;
            }
            const span = BLOCK_SPAN;
            if (!canPlaceBlock(day, startHour)) {
                toast.show(startHour + span > hours.length
                    ? 'Not enough time left in the day for a ' + (span * 30) + '-minute selection.'
                    : 'That slot is no longer available.');
                return false;
            }
            /* multi-week budget: MAX_SELECTION slots in total across every week */
            if (getGlobalTotal() + span > MAX_SELECTION) {
                toast.show('You\u2019ve reached the ' + MAX_SELECTION + '-slot maximum \u2014 clear another week\u2019s selection first.');
                return false;
            }
            /* a real (manual) selection completes the booking — drop the intent banner */
            if (pendingBookingIntent) { pendingBookingIntent = null; bookingIntentMemory = null; renderBookingIntent(); }
            selectedBlock = { day, startHour, endHour: startHour + span };
            previewRange = null;
            const body = document.getElementById('tableBody');
            renderMergedBlock(body);
            if (!selectedSlotsByVenue[currentVenue]) selectedSlotsByVenue[currentVenue] = {};
            selectedSlotsByVenue[currentVenue][weekNav.currentWeek] = { day, startHour, endHour: startHour + span };
            pushHistory({ action: 'select', block: { ...selectedBlock }, venue: currentVenue.code || currentVenue, week: weekNav.currentWeek });
            updateCounter();
            return true;
        }

        let previewRange = null;

        function previewBlock(day, startHour, el) {
            clearPreview();
            if (selectedBlock) return;
            const span = BLOCK_SPAN;
            const body = document.getElementById('tableBody');
            const noSubject = !currentCourse; /* locked grid: preview explains instead of inviting */
            const ok = !noSubject && canPlaceBlock(day, startHour);
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
            const label = document.createElement('span');
            label.className = 'preview-label';
            label.textContent = ok ? 'Select these slots?' : (noSubject ? 'Pick a subject first' : 'Not enough slots');
            div.appendChild(label);
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
            clearPreview(); /* a rebuild invalidates any open hover preview */
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
            // Grid locked until a subject is picked: guide instead of selecting
            if (!currentCourse) {
                toast.show('Select a subject first to trigger the timeslots selector');
                pulseSubjectSelector();
                return;
            }
            // If clicking inside the current block, deselect it
            if (selectedBlock && di === selectedBlock.day && hi >= selectedBlock.startHour && hi < selectedBlock.endHour) {
                discardSelection();
                return;
            }
            // Otherwise try to place a new block starting at this cell
            if (el.classList.contains('cell-available')) {
                selectBlock(di, hi);
            }
        }

        /* Draw the eye to the subject dropdown (used when a locked grid cell is clicked) */
        function pulseSubjectSelector() {
            const sel = document.getElementById('subjectSelector');
            if (!sel) return;
            sel.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            sel.classList.remove('attention-pulse');
            void sel.offsetWidth; /* restart the animation */
            sel.classList.add('attention-pulse');
            setTimeout(function() { sel.classList.remove('attention-pulse'); }, 1300);
        }

        function clearSelection() {
            discardSelection();
        }

        function onVenueChange() {
            if (revertingChange) return;
            const newVenue = venueDropdown ? venueDropdown.getSelected() : 'B103';
            if (selectedBlock && newVenue !== currentVenue) {
                showConfirmModal(
                    'Change Venue?',
                    'You have selected slots on the grid. Changing the <strong>venue</strong> will clear them. Continue?',
                    function() {
                        hideConfirmModal();
                        /* keep the booking armed for the next subject pick —
                           capture BEFORE the discard clears the block */
                        const rearm = bookingIntentMemory && intentMatchesBlock(
                            currentVenue.code || currentVenue, weekNav.currentWeek, selectedBlock);
                        deselectBlock();
                        applyVenueChange(newVenue);
                        if (rearm) {
                            pendingBookingIntent = { ...bookingIntentMemory };
                            renderBookingIntent();
                        }
                    }
                );
                // Cancel path: snap the dropdown back to the current venue
                const cancelBtn = document.querySelector('#confirmModal .btn-outline');
                if (cancelBtn) {
                    cancelBtn.onclick = function(e) {
                        hideConfirmModal(e);
                        revertingChange = true;
                        venueDropdown.select(currentVenue);
                        revertingChange = false;
                    };
                }
                return;
            }
            applyVenueChange(newVenue);
        }

        function applyVenueChange(newVenue) {
            saveCurrentWeek();
            currentVenue = newVenue;
            buildTimetable();
            updateFavStar();
        }

        function toggleFavourite() {
            if (!currentVenue || !venueDropdown) return;
            venueDropdown.toggleFavourite(currentVenue, document.getElementById('favStar'));
        }

        function updateFavStar() {
            venueDropdown && venueDropdown.updateFavStar(document.getElementById('favStar'), currentVenue);
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
            // Reset Cancel button to its default behavior after any custom handler
            const cancelBtn = document.querySelector('#confirmModal .btn-outline');
            if (cancelBtn) cancelBtn.onclick = function(ev) { hideConfirmModal(ev); };
        }

        function confirmChangeWithSelection(actionLabel, proceedFn, cancelFn) {
            if (!selectedBlock) { proceedFn(); return; }
            showConfirmModal(
                'Clear Current Selection?',
                'You have selected slots on the grid. Changing the <strong>' + actionLabel + '</strong> will remove them. Continue?',
                function() {
                    hideConfirmModal();
                    /* the change must not spend the booking: if the block being
                       cleared IS the booking's pre-fill, re-arm the intent so the
                       next subject application re-selects it */
                    if (bookingIntentMemory && intentMatchesBlock(
                            currentVenue.code || currentVenue, weekNav.currentWeek, selectedBlock)) {
                        pendingBookingIntent = { ...bookingIntentMemory };
                        /* signal that the booking is armed again (N5) */
                        renderBookingIntent();
                    }
                    discardSelection();
                    proceedFn();
                }
            );
            const cancelBtn = document.querySelector('#confirmModal .btn-outline');
            if (cancelBtn) {
                cancelBtn.onclick = function(e) {
                    hideConfirmModal(e);
                    if (cancelFn) cancelFn();
                };
            }
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
            if (getGlobalTotal() === 0) {
                showConfirmModal('No Selection', 'Please select at least one timeslot before proceeding.', null);
                return;
            }
            /* multi-week model: summarize EVERY saved selection, not just the week on screen */
            const shortDayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const rows = [];
            for (const venue in selectedSlotsByVenue) {
                for (const week in selectedSlotsByVenue[venue]) {
                    const block = selectedSlotsByVenue[venue][week];
                    if (!block) continue;
                    const dayData = weekData[week].days[block.day];
                    const startStr = hours[block.startHour];
                    const endStr = add30min(hours[block.endHour - 1]);
                    const slotCount = block.endHour - block.startHour;
                    rows.push(`<div style="padding:3px 0;font-size:13px;">${venue} · (${weekData[week].label}) ${shortDayNames[block.day]}, ${dayData.date} — ${to12h(startStr)} ~ ${to12h(endStr)} · ${slotCount} slots</div>`);
                }
            }
            const listHtml = rows.join('');
            showConfirmModal(
                'Confirm Your Selection',
                `<div style="margin-bottom:12px;font-weight:500;">You are about to submit a replacement request for the following ${rows.length > 1 ? rows.length + ' blocks' : 'block'}:</div>
                 <div style="border:1px solid var(--color-outline);border-radius:var(--radius-sm);padding:10px 14px;max-height:200px;overflow-y:auto;">${listHtml}</div>`,
                function() {
                    hideConfirmModal();
                    // Save as recent slot
                    if (selectedOriginalSlot) {
                        setRecentSlot(selectedOriginalSlot);
                    }
                    const sub = buildSubmissionToastMessage();
                    deselectBlock();
                    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                    /* the request is sent — Ctrl+Z must not resurrect its slots */
                    selectionHistory.length = 0;
                    updateCounter();
                    toast.show(sub.message, null, 5000, 'View \u2192', '/my-request-history-ui', sub.details);
                }
            );
        }

        function clearAll() {
            showConfirmModal(
                'Clear All Selections',
                'Are you sure you want to clear all selections across <strong>ALL</strong> weeks? You can undo this from the toast that appears.',
                function() {
                    hideConfirmModal();
                    var savedBlock = selectedBlock ? { ...selectedBlock } : null;
                    var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                    var savedVenue = currentVenue.code || currentVenue;
                    var savedWeek = weekNav.currentWeek;
                    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                    deselectBlock();
                    updateCounter();
                    toast.show('All selections cleared.', function() {
                        Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                        /* only re-render when the view hasn't moved since the clear —
                           restoring a foreign week's block onto the current grid
                           would bake it into the wrong week on the next navigation */
                        if (savedBlock && savedVenue === (currentVenue.code || currentVenue) && savedWeek === weekNav.currentWeek) {
                            selectedBlock = savedBlock;
                            const body = document.getElementById('tableBody');
                            renderMergedBlock(body);
                        } else {
                            selectedBlock = null;
                        }
                        updateCounter();
                    });
                }
            );
        }

        function navigateTo(url) {
            if (selectedBlock || getGlobalTotal() > 0) {
                showConfirmModal(
                    'Unsaved Changes',
                    'You have selections that will be lost if you leave this page. Are you sure you want to leave?',
                    function() {
                        /* Confirmed leave → navigate at once; selections are in-memory
                           and die with the page (no clearing ceremony / undo toast).
                           allowUnload bypasses the beforeunload guard (no double prompt). */
                        hideConfirmModal();
                        allowUnload = true;
                        window.location.href = url;
                    }
                );
            } else {
                window.location.href = url;
            }
        }

        function goBack() {
            if (selectedBlock || getGlobalTotal() > 0) {
                showConfirmModal(
                    'Unsaved Changes',
                    'You have selections that will be lost if you leave this page. Are you sure you want to go back?',
                    function() {
                        /* Confirmed leave → navigate at once; selections are in-memory
                           and die with the page (no clearing ceremony / 5s undo wait).
                           allowUnload bypasses the beforeunload guard (no double prompt). */
                        hideConfirmModal();
                        allowUnload = true;
                        BackNavigator.navigate();
                    }
                );
            } else {
                BackNavigator.navigate();
            }
        }

        /* Browser-level guard for the paths the in-page modal can't reach:
           refresh, close, address-bar navigation and the browser's own
           back/forward arrows. Browser controls the dialog text ("Changes
           you made may not be saved") — we only decide whether it appears. */
        let allowUnload = false;
        window.addEventListener('beforeunload', function(e) {
            if (!allowUnload && (selectedBlock || getGlobalTotal() > 0)) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });

        /* bfcache restore (browser back/forward): Chrome resurrects the frozen page
           exactly as left — selections, undo history AND the allowUnload bypass all
           survive. The user already confirmed leaving ("selections will be lost"),
           so re-arm the guard and come back to a clean slate. */
        /* Chrome re-fills form controls on back/forward reloads: the subject dropdown
           can DISPLAY a value the page's JS never applied (currentCourse still null),
           leaving the grid locked under a filled selector. Reconcile so what is shown
           is what is applied. Runs on every load — restoration precedes pageshow. */
        function reconcileSubjectState() {
            const sel = document.getElementById('subjectSelector');
            if (sel && sel.value && !currentCourse) onSubjectChange();
        }

        window.addEventListener('pageshow', function(e) {
            reconcileSubjectState();
            if (!e.persisted) {
                /* belt & braces: form-restoration timing varies — re-check shortly */
                setTimeout(reconcileSubjectState, 120);
                return; /* normal load — nothing else to do */
            }
            allowUnload = false;
            Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
            deselectBlock();        /* removes merged block + restores green cells */
            selectionHistory = [];  /* cleared after deselectBlock (it pushes an entry) — stale Ctrl+Z can't resurrect */
            updateCounter();        /* re-sync counter/summary/footer (also covers the no-block case) */
        });

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
            const venueNow = currentVenue.code || currentVenue;

            if (last.action === 'select') {
                // Undo a block selection → deselect it, but only if the view is
                // still where the selection happened — otherwise the wrong
                // week's/venue's block would be removed
                if (selectedBlock && last.venue === venueNow && last.week === weekNav.currentWeek
                    && selectedBlock.day === last.block.day && selectedBlock.startHour === last.block.startHour) {
                    deselectBlock();
                }
            } else if (last.action === 'deselect') {
                // Undo a block deselect → re-select it into its ORIGINAL
                // week/venue; re-render only when the view still matches —
                // restoring a foreign week's block onto the current grid would
                // bake it into the wrong week on the next navigation
                if (!selectedBlock) {
                    if (!selectedSlotsByVenue[last.venue]) selectedSlotsByVenue[last.venue] = {};
                    selectedSlotsByVenue[last.venue][last.week] = { ...last.block };
                    if (last.venue === venueNow && last.week === weekNav.currentWeek) {
                        selectedBlock = { ...last.block };
                        renderMergedBlock(body);
                    }
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
                /* keep the ring visible while arrowing through a tall grid */
                td.scrollIntoView({ block: 'nearest' });
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

            /* let form controls keep their native keys — the grid must not
               hijack arrows/space/enter from the subject or week selects */
            const tag = ((e.target && e.target.tagName) || '').toLowerCase();
            if (tag === 'select' || tag === 'input' || tag === 'textarea') {
                if (e.key === 'Escape') e.target.blur();
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
                case '[':
                    e.preventDefault();
                    weekNav.prevWeek();
                    break;
                case ']':
                    e.preventDefault();
                    weekNav.nextWeek();
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
                        /* switch to the Nth venue of the (filtered) dropdown list */
                        if (venueDropdown && typeof venueDropdown.getFiltered === 'function') {
                            const list = venueDropdown.getFiltered();
                            const v = list[parseInt(e.key, 10) - 1];
                            if (v) venueDropdown.select(v.code);
                        }
                    }
                    break;
            }
        }

        // ───── Subject Dropdown + URL Param Reading ─────

        let currentCourse = null;
        let urlParams = {};

        function renderTitleSummary() {
            const el = document.getElementById('subjectInfo');
            const hasSubject = !!currentCourse;
            const hasSlot = !!selectedOriginalSlot;

            /* Grid lock affordance: free cells read as not-allowed until a subject is picked */
            const timetableEl = document.getElementById('timetable');
            if (timetableEl) timetableEl.classList.toggle('no-subject', !hasSubject);

            // Line 1: Subject
            const subjectLine = hasSubject
                ? currentCourse.code + ' — ' + currentCourse.name + ' (' + currentCourse.type + ')'
                : null;

            // Line 2: Time Slot
            let slotLine = null;
            if (hasSlot) {
                const startStr = to12h(hours[selectedOriginalSlot.start]);
                const endStr = to12h(hours[selectedOriginalSlot.end + 1] || add30min(hours[selectedOriginalSlot.end]));
                const dateParts = selectedOriginalSlot.date.split(' ');
                const shortDate = dateParts[1] + ' ' + dateParts[0];
                slotLine = 'Week ' + selectedOriginalSlot.week + ' · ' + selectedOriginalSlot.dayName + ', ' + shortDate + ' ' + dateParts[2] + ' · ' + startStr + '–' + endStr + ' @ ' + selectedOriginalSlot.venue;
            }

            // Line 3: Cohorts
            let cohortsLine = null;
            if (hasSubject) {
                cohortsLine = currentCourse.cohorts.join(' + ');
            }

            // Line 4: Total Students
            let totalLine = null;
            if (hasSubject) {
                const counts = currentCourse.cohortCounts || [];
                if (counts.length > 1) {
                    totalLine = counts.join(' + ') + ' = ' + currentCourse.studentCount;
                } else {
                    totalLine = '' + currentCourse.studentCount;
                }
            }

            el.innerHTML = `<div class="conflict-strip">
                <div class="cs-head">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Conflict Schedule
                </div>
                <div class="cs-seg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    ${subjectLine
                        ? `<span class="cs-val"><strong>${currentCourse.code}</strong> — ${currentCourse.name} (${currentCourse.type})</span>`
                        : `<span class="cs-val cs-none">Not selected${!hasSubject ? '<span class="cs-hint">— Select a Subject first</span>' : ''}</span>`}
                </div>
                <div class="cs-seg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    ${slotLine
                        ? `<span class="cs-val">${slotLine}</span>`
                        : `<span class="cs-val cs-none">Not selected${!hasSlot ? '<span class="cs-hint">— Then pick a Conflict Slot</span>' : ''}</span>`}
                </div>
                <div class="cs-seg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    ${cohortsLine
                        ? `<span class="cs-val">${cohortsLine}</span>`
                        : `<span class="cs-val cs-none">Not selected</span>`}
                </div>
                <div class="cs-badge" data-tip="Total students">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    ${totalLine ? totalLine : '0'}
                </div>
            </div>`;
        }

        function buildSubjectDropdown() {
            const sel = document.getElementById('subjectSelector');
            const courses = MockData.courses || [];
            sel.innerHTML = '<option value="">Select a subject</option>';
            courses.forEach(c => {
                if (extractSlotsForSubject(c.code).length === 0) return;
                const opt = document.createElement('option');
                opt.value = c.code;
                opt.textContent = c.code + ' — ' + c.name;
                sel.appendChild(opt);
            });
        }

        function onSubjectChange() {
            if (revertingChange) return;
            const code = document.getElementById('subjectSelector').value;

            if (selectedBlock && code !== lastSubject) {
                confirmChangeWithSelection('subject', function() {
                    lastSubject = code;
                    applySubjectChange(code);
                }, function() {
                    revertingChange = true;
                    document.getElementById('subjectSelector').value = lastSubject;
                    revertingChange = false;
                });
                return;
            }
            lastSubject = code;
            applySubjectChange(code);
        }

        /* ───── Booking intent (arrival from venue-timetable "Book" action) ─────
           The handoff carries ?venue&date&time — the venue page's "Book" click
           already confirmed that exact slot, so once a subject exists we
           auto-select it instead of making the user hunt for it again. */
        let pendingBookingIntent = null;
        let intentBannerSuppressed = false; /* sticky ×: reminder stays dismissed for this booking */
        let intentNeedsManual = false;      /* booked slot can't be auto-selected (overflow/unavailable) */
        let bookingIntentMemory = null;     /* persistent copy of the booking — survives the one-shot
                                               consume so subject/venue changes can re-arm it */
        let intentCancelled = false;        /* user discarded the auto-select — reload must not resurrect it */

        function intentKey(i) { return i.venue + '|' + i.dateStr + '|' + i.timeStr; }

        function bookingKeyFromParams() {
            return (urlParams.venue || '') + '|' + (urlParams.date || '') + '|' + (urlParams.time || '');
        }

        function resolveBookingIntent() {
            pendingBookingIntent = null;
            intentBannerSuppressed = false;
            intentNeedsManual = false;
            intentCancelled = false;
            if (!urlParams.date || !urlParams.time) return;
            const hourIndex = hours.indexOf(urlParams.time);
            if (hourIndex < 0) return;
            /* the shared week (localStorage) is usually already right; if the
               booked date lives in another week, jump to it */
            let weekIdx = weekNav.currentWeek;
            let dayIndex = (weekData[weekIdx].days || []).findIndex(function(d) { return d.date === urlParams.date; });
            if (dayIndex < 0) {
                for (let w = 0; w < weekData.length && dayIndex < 0; w++) {
                    const di = (weekData[w].days || []).findIndex(function(d) { return d.date === urlParams.date; });
                    if (di >= 0) { weekIdx = w; dayIndex = di; }
                }
                if (dayIndex >= 0 && weekIdx !== weekNav.currentWeek) weekNav.selectWeek(weekIdx);
            }
            if (dayIndex < 0) return;
            pendingBookingIntent = {
                dayIndex: dayIndex,
                hourIndex: hourIndex,
                weekIndex: weekIdx,
                venue: urlParams.venue || '',
                dateStr: urlParams.date,
                timeStr: urlParams.time,
                dayAbbr: (weekData[weekIdx].days[dayIndex] || {}).abbr || ''
            };
            bookingIntentMemory = { ...pendingBookingIntent };
            /* a dismissed reminder stays dismissed for the SAME booking across
               navigation (sessionStorage) — the auto-select feature still works */
            try { intentBannerSuppressed = sessionStorage.getItem('bookingIntentDismissed') === intentKey(pendingBookingIntent); }
            catch (e) { intentBannerSuppressed = false; }
            /* a discarded auto-select also stays discarded for the SAME booking:
               the user explicitly removed the block, so reloads don't resurrect it */
            try { intentCancelled = sessionStorage.getItem('bookingIntentCancelled') === intentKey(pendingBookingIntent); }
            catch (e) { intentCancelled = false; }
            renderBookingIntent();
        }

        function renderBookingIntent() {
            const el = document.getElementById('bookingIntent');
            if (!el) return;
            if (!pendingBookingIntent || intentBannerSuppressed || intentCancelled) { el.innerHTML = ''; return; }
            const i = pendingBookingIntent;
            /* conflict case: the slot can't be auto-selected — error-toned notice */
            if (intentNeedsManual) {
                el.innerHTML = `<div class="booking-intent booking-intent-manual">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span><strong>Your booked slot (${i.dayAbbr}, ${i.dateStr} · ${i.timeStr}) is conflicted</strong> — please select an available slot manually.</span>
                    <button class="bi-close" onclick="dismissBookingIntent()" data-tip="Dismiss booking reminder">&times;</button>
                </div>`;
                return;
            }
            const msg = currentCourse ? 'will pre-fill when you next pick a subject.' : 'pick a subject to pre-fill the slots.';
            el.innerHTML = `<div class="booking-intent">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span><strong>Booking ${i.venue} · ${i.dayAbbr}, ${i.dateStr} · ${i.timeStr}</strong> — ${msg}</span>
                <button class="bi-close" onclick="dismissBookingIntent()" data-tip="Dismiss booking reminder">&times;</button>
            </div>`;
        }

        function dismissBookingIntent() {
            if (pendingBookingIntent) {
                try { sessionStorage.setItem('bookingIntentDismissed', intentKey(pendingBookingIntent)); } catch (e) {}
            }
            intentBannerSuppressed = true;
            renderBookingIntent();
        }

        function pulseTargetCell(dayIndex, hourIndex) {
            const cell = document.querySelector(`#tableBody td[data-day="${dayIndex}"][data-hour="${hourIndex}"] .cell-content`);
            if (!cell) return;
            cell.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            cell.classList.remove('attention-pulse');
            void cell.offsetWidth; /* restart the animation */
            cell.classList.add('attention-pulse');
            setTimeout(function() { cell.classList.remove('attention-pulse'); }, 1300);
        }

        /* Whether the booked slot can be auto-selected is known once the grid sits
           on the booked venue — evaluated at the end of applyUrlParams, i.e. on
           arrival and before any user interaction. If it can't fit, the user is
           told immediately instead of after picking a subject. */
        function evaluateBookingFit() {
            if (!pendingBookingIntent) return;
            const i = pendingBookingIntent;
            intentNeedsManual = !canPlaceBlock(i.dayIndex, i.hourIndex);
            if (intentNeedsManual) {
                pulseTargetCell(i.dayIndex, i.hourIndex); /* the banner names the slot in words — no toast needed */
            }
            renderBookingIntent();
        }

        /* The user explicitly discarded a selection — remember it for THIS booking
           (sessionStorage, same key family as the banner dismissal) so a reload of
           the same URL doesn't silently re-select the block they removed. */
        function markBookingCancelled() {
            bookingIntentMemory = null; /* the booking is spent — changes must not re-arm it */
            if (!urlParams.date || !urlParams.time) return;
            try { sessionStorage.setItem('bookingIntentCancelled', bookingKeyFromParams()); } catch (e) {}
        }

        /* Explicit user discard of the current block: kill the (now stale)
           "Pre-selected…" toast, remember the booking as handled, then deselect. */
        function discardSelection() {
            toast.dismiss();
            deselectBlock();
        }

        /* the intent is only spent when the user explicitly removes THE booked
           block (clicking it / its summary card) — subject or venue changes keep
           the booking alive for the next subject pick */
        function intentMatchesBlock(venueKey, weekKey, block) {
            /* the live intent is usually already consumed at check time —
               compare against the persistent booking memory as fallback */
            const i = pendingBookingIntent || bookingIntentMemory;
            if (!i || !block) return false;
            return i.venue === venueKey && i.weekIndex === parseInt(weekKey, 10)
                && i.dayIndex === block.day && i.hourIndex === block.startHour;
        }

        function userDeselectSelectedBlock() {
            const block = selectedBlock ? { ...selectedBlock } : null;
            const venueKey = currentVenue.code || currentVenue;
            const weekKey = weekNav.currentWeek;
            discardSelection();
            if (intentMatchesBlock(venueKey, weekKey, block)) markBookingCancelled();
        }

        /* summary-card × — remove one saved block (any week/venue) */
        function removeSavedBlock(venueKey, weekKey) {
            const data = selectedSlotsByVenue[venueKey];
            if (!data || !data[weekKey]) return;
            const removed = { ...data[weekKey] };
            const isCurrentView = venueKey === (currentVenue.code || currentVenue)
                && parseInt(weekKey, 10) === weekNav.currentWeek;
            if (isCurrentView && selectedBlock) {
                discardSelection();
            } else {
                data[weekKey] = null;
                updateCounter();
            }
            if (intentMatchesBlock(venueKey, weekKey, removed)) markBookingCancelled();
        }

        /* Fires once a subject exists (URL auto-pick or user pick): */
        function consumeBookingIntent() {
            if (!pendingBookingIntent || !currentCourse) return;
            const i = pendingBookingIntent;
            const venueNow = currentVenue.code || currentVenue;
            const onBookedGrid = weekNav.currentWeek === i.weekIndex && venueNow === i.venue;

            if (intentCancelled) {
                /* the user already discarded this booking's auto-select — the intent
                   is spent: clear the banner, select nothing, stay quiet */
                pendingBookingIntent = null;
                renderBookingIntent();
                return;
            }

            /* an identical selection (restored from per-week memory) already fulfils it */
            if (selectedBlock && onBookedGrid && selectedBlock.day === i.dayIndex && selectedBlock.startHour === i.hourIndex) {
                pendingBookingIntent = null;
                renderBookingIntent();
                return;
            }

            /* browsed away from the booking's week/venue before picking a subject?
               the booking owns the context — snap the grid back (per-week memory
               keeps any selection made elsewhere); skipped while a selection is
               active so nothing is discarded behind the user's back */
            if (!onBookedGrid && !selectedBlock) {
                if (weekNav.currentWeek !== i.weekIndex) weekNav.selectWeek(i.weekIndex);
                if ((currentVenue.code || currentVenue) !== i.venue) venueDropdown.select(i.venue);
            }

            if (selectedBlock) {
                /* a different selection is in the way — guide manually instead of
                   dropping a jarring "clear first" modal mid-flow */
                intentNeedsManual = true;
                pulseTargetCell(i.dayIndex, i.hourIndex);
                renderBookingIntent();
                return;
            }

            if (intentNeedsManual || !canPlaceBlock(i.dayIndex, i.hourIndex)) {
                intentNeedsManual = true;
                pulseTargetCell(i.dayIndex, i.hourIndex);
                renderBookingIntent();
                return;
            }

            pendingBookingIntent = null;
            renderBookingIntent();
            if (selectBlock(i.dayIndex, i.hourIndex)) {
                toast.show('Pre-selected from your venue booking — click the block to adjust.');
            }
        }

        function applySubjectChange(code) {
            clearPreview(); /* subject state changed — any open hover preview is stale */
            const noteEl = document.getElementById('venueCountNote');

            if (!code) {
                currentCourse = null;
                selectedOriginalSlot = null;
                noteEl.style.display = 'none';
                renderSlotPicker([]);
                renderTitleSummary();
                buildVenueFilter();
                return;
            }

            currentCourse = (MockData.courses || []).find(c => c.code === code);
            if (!currentCourse) return;

            selectedOriginalSlot = null;
            document.getElementById('slotTriggerText').textContent = 'Select a slot to replace';

            // Extract and render conflict/cancelled slots for this subject
            const slots = extractSlotsForSubject(code);
            renderSlotPicker(slots);
            renderTitleSummary();
            buildVenueFilter();
            consumeBookingIntent();
        }

        function buildVenueFilter() {
            const noteEl = document.getElementById('venueCountNote');
            if (!venueDropdown) return;

            if (currentCourse) {
                venueDropdown.setFilter(function(v) {
                    var typeOk = v.type === 'Tutorial' || (currentCourse.type === 'L' && v.type === 'LectureHall');
                    var capOk = v.capacity >= currentCourse.studentCount;
                    return typeOk && capOk;
                });
                var count = venueDropdown.getFiltered ? venueDropdown.getFiltered().length : 0;
                noteEl.textContent = 'Showing venues that fit ' + currentCourse.studentCount + ' students';
                noteEl.style.display = '';
            } else {
                venueDropdown.setFilter(null);
                noteEl.style.display = 'none';
            }
        }

        // ───── Slot Picker (conflict/cancelled slots for selected subject) ─────

        let selectedOriginalSlot = null;
        const slotDayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        function extractSlotsForSubject(courseCode) {
            const slots = [];
            const events = (MockData.cohortTimetable || {}).events || [];
            
            events.forEach(entry => {
                const e = entry.event;
                if (!e) return;
                if (e.code === courseCode && (e.status === 'conflict' || e.status === 'cancelled')) {
                    const weekIdx = entry.week;
                    const dayIdx = e.di;
                    // Build date string from weekData
                    let dateStr = '';
                    try {
                        if (weekData[weekIdx] && weekData[weekIdx].days && weekData[weekIdx].days[dayIdx]) {
                            dateStr = weekData[weekIdx].days[dayIdx].date;
                        }
                    } catch(ex) {}
                    slots.push({
                        week: weekIdx,
                        day: dayIdx,
                        dayName: slotDayNames[dayIdx] || 'Unknown',
                        start: e.start,
                        end: e.end,
                        venue: e.venue,
                        status: e.status,
                        name: e.name,
                        type: e.type,
                        code: courseCode,
                        date: dateStr
                    });
                }
            });
            
            return slots.sort((a, b) => a.week - b.week || a.day - b.day || a.start - b.start);
        }

        function renderSlotPicker(slots) {
            const picker = document.getElementById('slotPicker');
            const panel = document.getElementById('slotPanel');
            const triggerText = document.getElementById('slotTriggerText');

            slotPickerSlots = slots || [];

            if (!slots || slots.length === 0) {
                picker.style.display = 'none';
                return;
            }
            
            picker.style.display = '';
            panel.innerHTML = '';
            selectedOriginalSlot = null;
            triggerText.textContent = 'Select a slot to replace';

            slots.forEach((slot, index) => {
                const startStr = to12h(hours[slot.start]);
                const endStr = to12h(hours[slot.end + 1] || add30min(hours[slot.end]));
                const statusLabel = slot.status === 'conflict' ? 'Conflict' : 'Cancelled';
                const statusBg = slot.status === 'conflict' ? 'var(--color-error-container)' : 'var(--color-surface-variant)';
                const statusColor = slot.status === 'conflict' ? 'var(--color-on-error-container)' : 'var(--color-on-surface-variant)';
                // Parse "18 Aug 2026" → "Aug 18"
                const dateParts = slot.date.split(' ');
                const shortDate = dateParts[1] + ' ' + dateParts[0];
                const fullLabel = 'Week ' + slot.week + ' · ' + slotDayNames[slot.day] + ', ' + slot.date + ', ' + startStr + ' - ' + endStr + ' @ ' + slot.venue;

                const item = document.createElement('div');
                item.className = 'slot-dd-item';
                item.dataset.index = index;
                item.innerHTML = `
                    <span class="slot-dd-item-star" data-index="${index}" title="Toggle favourite">★</span>
                    <span class="slot-dd-item-label">W${slot.week} · ${shortDate}, ${startStr} - ${endStr} @ ${slot.venue}</span>
                    <span class="slot-dd-item-badge" style="background:${statusBg};color:${statusColor};">${statusLabel}</span>
                `;

                // Hover tooltip
                const tooltip = document.getElementById('slotTooltip');
                item.addEventListener('mouseenter', (e) => {
                    const itemRect = item.getBoundingClientRect();
                    const pickerRect = picker.getBoundingClientRect();
                    tooltip.textContent = fullLabel;
                    tooltip.style.top = (itemRect.top - pickerRect.top) + 'px';
                    tooltip.style.left = (panel.offsetWidth - 1) + 'px';
                    tooltip.style.display = 'block';
                });
                item.addEventListener('mouseleave', () => {
                    tooltip.style.display = 'none';
                });

                // Click to select
                item.addEventListener('click', (e) => {
                    if (e.target.classList.contains('slot-dd-item-star')) return;
                    selectSlot(slot, index);
                    closeSlotPanel();
                });

                // Favourite star
                const star = item.querySelector('.slot-dd-item-star');
                const fav = getFavouriteSlot();
                if (fav && fav.code === slot.code && fav.day === slot.day && fav.start === slot.start && fav.venue === slot.venue) {
                    star.classList.add('active');
                }
                star.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleSlotFavourite(slot, star);
                });

                panel.appendChild(item);
            });

            // Apply default selection priority
            applyDefaultSlotSelection(slots);
        }

        function selectSlot(slot, index) {
            if (revertingChange) return;

            if (selectedBlock && index !== lastSlotIndex) {
                confirmChangeWithSelection('time slot', function() {
                    lastSlotIndex = index;
                    commitSlotSelection(slot, index);
                }, function() {
                    // Snap the picker back to the previously committed slot
                    revertingChange = true;
                    if (lastSlotIndex !== null && lastSlotIndex !== undefined && slotPickerSlots[lastSlotIndex]) {
                        commitSlotSelection(slotPickerSlots[lastSlotIndex], lastSlotIndex);
                    } else {
                        selectedOriginalSlot = null;
                        document.getElementById('slotTriggerText').textContent = 'Select a slot to replace';
                        document.querySelectorAll('.slot-dd-item').forEach(item => item.classList.remove('selected'));
                        renderTitleSummary();
                    }
                    revertingChange = false;
                });
                return;
            }
            lastSlotIndex = index;
            commitSlotSelection(slot, index);
        }

        function commitSlotSelection(slot, index) {
            selectedOriginalSlot = slot;
            const triggerText = document.getElementById('slotTriggerText');
            const startStr = to12h(hours[slot.start]);
            const endStr = to12h(hours[slot.end + 1] || add30min(hours[slot.end]));
            const dateParts = slot.date.split(' ');
            const shortDate = dateParts[1] + ' ' + dateParts[0];
            triggerText.textContent = 'W' + slot.week + ' · ' + shortDate + ' · ' + startStr + '–' + endStr;

            // Update selected state in panel
            document.querySelectorAll('.slot-dd-item').forEach(item => {
                item.classList.toggle('selected', parseInt(item.dataset.index) === index);
            });

            renderTitleSummary();
        }

        function toggleSlotPanel() {
            const panel = document.getElementById('slotPanel');
            const isOpen = panel.classList.contains('open');
            if (isOpen) {
                closeSlotPanel();
            } else {
                panel.classList.add('open');
                // Close on outside click
                setTimeout(() => {
                    document.addEventListener('click', closeSlotPanelOnOutside);
                }, 0);
            }
        }

        function closeSlotPanel() {
            const panel = document.getElementById('slotPanel');
            const tooltip = document.getElementById('slotTooltip');
            panel.classList.remove('open');
            tooltip.style.display = 'none';
            document.removeEventListener('click', closeSlotPanelOnOutside);
        }

        function closeSlotPanelOnOutside(e) {
            const picker = document.getElementById('slotPicker');
            if (!picker.contains(e.target)) {
                closeSlotPanel();
            }
        }

        // ── Favourite Slot (global, localStorage) ──
        const FAV_KEY = 'replacement-favourite';

        function getFavouriteSlot() {
            try {
                return JSON.parse(localStorage.getItem(FAV_KEY) || 'null');
            } catch (e) { return null; }
        }

        function toggleSlotFavourite(slot, starEl) {
            const current = getFavouriteSlot();
            const isCurrentFav = current && current.code === slot.code && current.day === slot.day && current.start === slot.start && current.venue === slot.venue;
            if (isCurrentFav) {
                localStorage.removeItem(FAV_KEY);
                starEl.classList.remove('active');
            } else {
                localStorage.setItem(FAV_KEY, JSON.stringify({ code: slot.code, day: slot.day, start: slot.start, end: slot.end, venue: slot.venue, week: slot.week }));
                // Update all stars
                document.querySelectorAll('.slot-dd-item-star').forEach(s => s.classList.remove('active'));
                starEl.classList.add('active');
            }
        }

        // ── Recent Slot (last submitted, localStorage) ──
        const RECENT_KEY = 'replacement-recent';

        function getRecentSlot() {
            try {
                return JSON.parse(localStorage.getItem(RECENT_KEY) || 'null');
            } catch (e) { return null; }
        }

        function setRecentSlot(slot) {
            localStorage.setItem(RECENT_KEY, JSON.stringify({ code: slot.code, day: slot.day, start: slot.start, end: slot.end, venue: slot.venue, week: slot.week }));
        }

        // ── Default Selection Priority ──
        function applyDefaultSlotSelection(slots) {
            // 1. URL params from Venue Timetable (already handled in applyUrlParams)
            // 2. Favourite
            const fav = getFavouriteSlot();
            if (fav) {
                const match = slots.find(s => s.code === fav.code && s.day === fav.day && s.start === fav.start && s.venue === fav.venue);
                if (match) {
                    const idx = slots.indexOf(match);
                    selectSlot(match, idx);
                    return;
                }
            }
            // 3. Recent
            const recent = getRecentSlot();
            if (recent) {
                const match = slots.find(s => s.code === recent.code && s.day === recent.day && s.start === recent.start && s.venue === recent.venue);
                if (match) {
                    const idx = slots.indexOf(match);
                    selectSlot(match, idx);
                    return;
                }
            }
            // 4. First option
            if (slots.length > 0) {
                selectSlot(slots[0], 0);
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
            /* a hand-typed/tampered venue param must not render a phantom venue:
               ignore it — and the booking that named it — and load the default */
            if (urlParams.venue && !MockData.venues.some(v => v.code === urlParams.venue)) {
                urlParams.venue = null;
                urlParams.date = null;
                urlParams.time = null;
            }
            /* Booking intent (venue-timetable "Book" handoff): resolve date+time
               into a grid target first — the shared week is already restored */
            resolveBookingIntent();
            const sel = document.getElementById('subjectSelector');
            /* venue BEFORE subject, so the booking-intent auto-select (fired by
               the subject pick below) lands on the booked venue's grid */
            if (urlParams.venue) {
                if (venueDropdown) {
                    venueDropdown.select(urlParams.venue);
                }
            }
            if (urlParams.code) {
                sel.value = urlParams.code;
                onSubjectChange();
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
                    const slotIndex = slots.indexOf(matchingSlot);
                    selectSlot(matchingSlot, slotIndex);
                }
            }
            evaluateBookingFit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            readUrlParams();
            // If the original class duration was passed in (hours), size the block
            // span from it — clamped to 0.5–4h; the multi-week BUDGET stays at
            // least the approved 4 slots so a shorter class can still book twice.
            if (urlParams.duration && !isNaN(parseFloat(urlParams.duration))) {
                const hrs = Math.min(Math.max(parseFloat(urlParams.duration), 0.5), 4);
                BLOCK_SPAN = Math.round(hrs * 2);
                MAX_SELECTION = Math.max(MAX_SELECTION, BLOCK_SPAN);
            }
            buildSubjectDropdown();
            renderTitleSummary();

            /* init venue dropdown */
            venueDropdown = new VenueDropdown(
                document.getElementById('buildingSelectorDropdown'),
                {
                    venues: MockData.venues,
                    initialCode: urlParams.venue || currentVenue,
                    onSelect: function(code) { onVenueChange(); }
                }
            );
            /* Full venue label as a data-tip (renders above) — the trigger ellipsizes at the
               single-row width cap; kept current for every trigger text change */
            const venueTrigger = document.querySelector('#buildingSelectorDropdown .venue-dd-trigger');
            const syncVenueTip = function() {
                venueTrigger.setAttribute('data-tip', venueTrigger.textContent.replace(/\s*▾\s*$/, '').trim());
            };
            new MutationObserver(syncVenueTip).observe(venueTrigger, { childList: true, characterData: true, subtree: true });
            syncVenueTip();
            updateFavStar();
            document.getElementById('favStar').addEventListener('click', toggleFavourite);
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            /* restore the previously saved week (localStorage), else falls back to week 1 */
            weekNav.load();
            populateWeekSelect('weekSelector', {
                ranges: false,
                selected: weekNav.currentWeek,
                labelFn: function(w, i, isMobile) {
                    /* Compact on all widths — year dropped (constant within a semester);
                       the full dated label lives on the select's hover tooltip */
                    const first = w.days[0].date.replace(/ \d{4}$/, '');
                    const last = w.days[w.days.length - 1].date.replace(/ \d{4}$/, '');
                    return w.label + ' \u00B7 ' + first + ' ~ ' + last;
                }
            });
            /* applyUrlParams AFTER week selector is ready (triggers onVenueChange → buildTimetable) */
            applyUrlParams();
            /* Subject tooltip: guides the first pick, then mirrors the full subject info
               (the single-row select ellipsizes long names) */
            const subjectSel = document.getElementById('subjectSelector');
            const syncSubjectTip = function() {
                subjectSel.setAttribute('data-tip', subjectSel.value
                    ? subjectSel.options[subjectSel.selectedIndex].text
                    : 'Select a subject first to trigger the timeslots selector');
            };
            subjectSel.addEventListener('change', syncSubjectTip);
            syncSubjectTip();
            /* Full dated label as a data-tip (renders above, shared tooltip system) — the
               option text is the compact single-row form. Patched onto _updateSelect so
               arrows/Today keep it current. */
            const weekSel = document.getElementById('weekSelector');
            const weekTip = function() {
                const w = weekData[weekNav.currentWeek];
                if (w) weekSel.setAttribute('data-tip', w.label + ' \u00B7 ' + w.days[0].date + ' ~ ' + w.days[w.days.length - 1].date);
            };
            const _updateSelect = weekNav._updateSelect.bind(weekNav);
            weekNav._updateSelect = function() { _updateSelect(); weekTip(); };
            weekTip();
            lastSubject = document.getElementById('subjectSelector').value;
            /* ensure grid always renders on load (applyUrlParams only triggers via venue param) */
            buildTimetable();

            document.addEventListener('keydown', handleKeyDown);
            initWeekKeyboardShortcuts();

            document.getElementById('todayBtn')?.addEventListener('click', function() {
                try {
                    saveCurrentWeek();
                    weekNav.jumpToToday();
                    weekNav.save();
                    var sel = document.getElementById('weekSelector');
                    if (sel) sel.value = weekNav.currentWeek;
                } catch (err) {
                    window.__todayBtnError = err.message + ' | ' + (err.stack || '').split('\n').slice(0,3).join(' ');
                }
            });
        });
@endsection
