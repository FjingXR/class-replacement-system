<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Replacement Arrangement — Class Replacement System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved === 'light') {
                document.documentElement.className = 'light';
            }
        })();
    </script>
    <style>
        :root {
            --background: #1c1e1f;
            --on-background: #e2e4e6;
            --surface: #2a2c2e;
            --on-surface: #e2e4e6;
            --surface-variant: #3a3c3e;
            --on-surface-variant: #c4c9ce;
            --primary: #8DB5E6;
            --on-primary: #0B294C;
            --primary-container: rgba(141, 181, 230, 0.2);
            --on-primary-container: #8DB5E6;
            --secondary: #2EC27E;
            --on-secondary: #FFFFFF;
            --secondary-container: rgba(46, 194, 126, 0.2);
            --on-secondary-container: #97E6C2;
            --tertiary: #E3E6AA;
            --on-tertiary: #323315;
            --tertiary-container: rgba(227, 230, 170, 0.2);
            --on-tertiary-container: #E3E6AA;
            --error: #E69490;
            --on-error: #601410;
            --error-container: rgba(230, 148, 144, 0.2);
            --on-error-container: #E69490;
            --outline: rgba(159, 168, 179, 0.2);
            --outline-strong: rgba(159, 168, 179, 0.35);
            --shadow: 0 2px 8px rgba(0,0,0,0.25);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.35);
            --radius-sm: 8px;
            --radius-md: 12px;
            --transition: 0.2s ease;
        }
        .light {
            --background: #f2f4f5;
            --on-background: #1e1f20;
            --surface: #ffffff;
            --on-surface: #1e1f20;
            --surface-variant: #e8eaed;
            --on-surface-variant: #5a6068;
            --primary: #1A5FB4;
            --on-primary: #FFFFFF;
            --primary-container: rgba(26, 95, 180, 0.15);
            --on-primary-container: #1A5FB4;
            --secondary: #2EC27E;
            --on-secondary: #FFFFFF;
            --secondary-container: rgba(46, 194, 126, 0.15);
            --on-secondary-container: #1a7a4e;
            --tertiary: #9fa028;
            --on-tertiary: #FFFFFF;
            --tertiary-container: rgba(159, 160, 40, 0.15);
            --on-tertiary-container: #6b6c1a;
            --error: #B3261E;
            --on-error: #FFFFFF;
            --error-container: rgba(179, 38, 30, 0.12);
            --on-error-container: #b3261e;
            --outline: rgba(90, 96, 104, 0.2);
            --outline-strong: rgba(90, 96, 104, 0.35);
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background);
            color: var(--on-background);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background var(--transition), color var(--transition);
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--outline-strong); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--on-surface-variant); }
        ::selection { background: var(--primary); color: var(--on-primary); }

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
            background: var(--primary);
            color: var(--on-primary);
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
        }
        .top-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--on-surface);
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
            border: 1px solid var(--outline);
            background: var(--surface);
            color: var(--on-surface-variant);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: background var(--transition), transform 0.15s, border-color var(--transition);
            box-shadow: var(--shadow);
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
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow);
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
            background: var(--secondary-container);
            color: var(--on-secondary-container);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition);
            outline: none;
        }
        .selector-dropdown:hover { filter: brightness(1.1); }
        .selector-dropdown option { background: var(--surface); color: var(--on-surface); }

        .toolbar-center {
            text-align: center;
            flex: 1;
            min-width: 0;
        }
        .toolbar-center .toolbar-subtitle {
            font-size: 14px;
            font-weight: 500;
            color: var(--on-surface-variant);
        }
        .toolbar-center .toolbar-meta {
            font-size: 13px;
            color: var(--on-surface-variant);
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
            color: var(--on-surface-variant);
            margin-top: 12px;
            margin-bottom: -4px;
            opacity: 0.6;
            font-weight: 400;
        }

        .grid-wrapper {
            flex: 1;
            margin-top: 14px;
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow);
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
            border: 1px solid var(--outline);
            text-align: center;
            vertical-align: middle;
            font-size: 13px;
            transition: background var(--transition), border-color var(--transition);
        }

        .timetable th {
            background: var(--surface-variant);
            color: var(--on-surface-variant);
            font-weight: 600;
            position: sticky;
            z-index: 10;
        }

        .timetable thead th {
            top: 0;
            z-index: 20;
        }

        .time-header-col {
            width: 130px;
            min-width: 130px;
            left: 0;
            z-index: 30 !important;
        }
        thead .time-header-col { z-index: 40 !important; }

        .time-col {
            width: 130px;
            min-width: 130px;
            left: 0;
            position: sticky;
            z-index: 15;
            background: var(--surface);
            font-weight: 600;
        }
        .time-col .day-label {
            display: block;
            font-size: 14px;
        }
        .time-col .date-label {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--on-surface-variant);
            margin-top: 2px;
        }
        .time-col .holiday-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: var(--error);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .hour-header {
            padding: 6px 4px;
            font-size: 12px;
            min-width: 80px;
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
            height: 52px;
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
            background: var(--secondary-container);
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
            background: var(--error-container);
            cursor: not-allowed;
        }

        .cell-selected {
            background: var(--primary-container);
            border: 2px solid var(--primary);
            box-shadow: inset 0 0 0 1px var(--primary);
            cursor: pointer;
        }
        .cell-selected .sel-text {
            font-size: 10px;
            font-weight: 700;
            color: var(--on-primary-container);
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
            content: "Current\A Selection";
            white-space: pre;
        }
        .cell-selected:hover .sel-text::before {
            content: "CLICK ME\A to DESELECT";
        }

        .cell-pending {
            background: var(--tertiary-container);
            cursor: not-allowed;
        }

        .timetable tr:last-child td { border-bottom: none; }

        .footer-area {
            margin-top: 14px;
            padding: 14px 18px;
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow);
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
            color: var(--on-surface-variant);
        }
        .footer-left strong {
            color: var(--on-surface);
            font-weight: 600;
        }

        .selection-counter {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--on-surface-variant);
            padding: 10px 14px;
            background: var(--surface-variant);
            border-radius: var(--radius-sm);
            height: 40px;
        }
        .selection-counter .count-num {
            font-weight: 700;
            color: var(--primary);
        }
        .selection-counter .count-max {
            font-weight: 600;
            color: var(--on-surface-variant);
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
            background: var(--surface);
            border: 1px solid var(--outline-strong);
            color: var(--on-surface-variant);
        }
        .btn-outline:hover {
            background: var(--surface-variant);
            border-color: var(--on-surface-variant);
        }

        .btn-primary {
            background: var(--secondary);
            color: var(--on-secondary);
            box-shadow: 0 2px 8px rgba(151, 230, 194, 0.2);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(151, 230, 194, 0.3);
            filter: brightness(1.05);
        }
        .light .btn-primary { box-shadow: 0 2px 8px rgba(46, 194, 126, 0.2); }
        .light .btn-primary:hover { box-shadow: 0 4px 16px rgba(46, 194, 126, 0.3); }

        .legend {
            margin-top: 12px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            padding: 10px 18px;
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            transition: background var(--transition), border-color var(--transition);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--on-surface-variant);
        }
        .legend-swatch {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            flex-shrink: 0;
            border: 1px solid var(--outline);
        }

        @media (max-width: 768px) {
            .app-container { padding: 10px 12px; padding-top: 64px; }
            .top-title { font-size: 16px; }
            .top-logo { height: 26px; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-left, .toolbar-right { justify-content: center; }
            .footer-area { flex-direction: column; text-align: center; }
            .footer-left { justify-content: center; }
            .footer-right { justify-content: center; }
            .time-header-col, .time-col { width: 100px; min-width: 100px; }
            .hour-header, .timetable td.hour-cell { min-width: 60px; }
            .header-logo { height: 44px; }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <button class="back-btn" onclick="history.back()" aria-label="Back">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
        </button>
        <div class="top-center">
            <img src="/images/logo_icon.png" alt="Logo" class="top-logo">
            <span class="top-title">Replacement Arrangement</span>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>

    <div class="app-container">
        <div class="toolbar">
            <div class="toolbar-left">
                <select class="selector-dropdown" id="weekSelector" onchange="onWeekChange()">
                    <option value="0">Week 11 (01-Sep-2026 ~ 07-Sep-2026)</option>
                    <option value="1">Week 10 (25-Aug-2026 ~ 31-Aug-2026)</option>
                    <option value="2">Week 9 (18-Aug-2026 ~ 24-Aug-2026)</option>
                </select>
            </div>
            <div class="toolbar-center">
                <div class="toolbar-subtitle">BMIT6767 Kylian Mbappe Dembele (L)</div>
                <div class="toolbar-meta">Mon, 31-Aug-2026, 10:00 AM - 12:00 PM (2 hours)</div>
            </div>
            <div class="toolbar-right">
                <select class="selector-dropdown" id="buildingSelector">
                    <option>B103</option>
                    <option>B104</option>
                    <option>B105</option>
                    <option>B106</option>
                </select>
            </div>
        </div>

        <div class="hint-text">Select an available (green) time slot</div>

        <div class="grid-wrapper">
            <div class="grid-scroll" id="gridScroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>

        <div class="footer-area">
            <div class="footer-left">
                <span><strong>Cohort:</strong> DFT2 (S1) / DSF2 (S1) / DFT2 (S1) Jefferson Ng (2310971)</span>
            </div>
            <div class="footer-right">
                <div class="selection-counter">
                    Selected: <span class="count-num" id="selCount">0</span><span class="count-max">&nbsp;/ 4</span>
                </div>
                <button class="btn btn-outline" onclick="clearSelection()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Clear
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

        <div class="legend">
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--secondary);"></div>
                Available
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--tertiary);"></div>
                PENDING by Others
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--error);"></div>
                Occupied / Class on Public Holiday
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--primary);"></div>
                Reserved (Waiting for Approval)
            </div>
        </div>
    </div>

    <script>
        const MAX_SELECTION = 4;

        const hours = [
            '08:00', '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        const weekData = [
            {
                label: 'Week 11',
                days: [
                    { abbr: 'Mon', date: '01 Sep 2026' },
                    { abbr: 'Tue', date: '02 Sep 2026' },
                    { abbr: 'Wed', date: '03 Sep 2026' },
                    { abbr: 'Thu', date: '04 Sep 2026', holiday: true },
                    { abbr: 'Fri', date: '05 Sep 2026' },
                    { abbr: 'Sat', date: '06 Sep 2026' },
                    { abbr: 'Sun', date: '07 Sep 2026' },
                ]
            },
            {
                label: 'Week 10',
                days: [
                    { abbr: 'Mon', date: '25 Aug 2026' },
                    { abbr: 'Tue', date: '26 Aug 2026' },
                    { abbr: 'Wed', date: '27 Aug 2026' },
                    { abbr: 'Thu', date: '28 Aug 2026' },
                    { abbr: 'Fri', date: '29 Aug 2026' },
                    { abbr: 'Sat', date: '30 Aug 2026' },
                    { abbr: 'Sun', date: '31 Aug 2026' },
                ]
            },
            {
                label: 'Week 9',
                days: [
                    { abbr: 'Mon', date: '18 Aug 2026' },
                    { abbr: 'Tue', date: '19 Aug 2026' },
                    { abbr: 'Wed', date: '20 Aug 2026' },
                    { abbr: 'Thu', date: '21 Aug 2026' },
                    { abbr: 'Fri', date: '22 Aug 2026' },
                    { abbr: 'Sat', date: '23 Aug 2026' },
                    { abbr: 'Sun', date: '24 Aug 2026' },
                ]
            }
        ];

        const slotData = [
            [0, 2, 0],
            [0, 3, 0],
            [1, 0, 0],
            [1, 1, 0],
            [1, 4, 0],
            [1, 5, 0],
            [2, 6, 1],
            [2, 7, 1],
            [4, 7, 3],
            [4, 8, 3],
            [4, 9, 3],
            [4, 10, 3],
            [5, 2, 2],
            [5, 3, 2],
        ];

        let selectedCells = [];

        function getDays() {
            const idx = parseInt(document.getElementById('weekSelector').value);
            return weekData[idx].days;
        }

        function updateCounter() {
            const el = document.getElementById('selCount');
            if (el) el.textContent = selectedCells.length;
        }

        function buildTimetable() {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';
            selectedCells = [];

            const days = getDays();

            const timeHeaderRow = document.createElement('tr');
            const cornerTh = document.createElement('th');
            cornerTh.className = 'time-header-col';
            cornerTh.style.cssText = 'position: sticky; left: 0; z-index: 40;';
            cornerTh.innerHTML = '<span style="font-size:13px;font-weight:600;">Day / Time</span>';
            timeHeaderRow.appendChild(cornerTh);

            hours.forEach(h => {
                const th = document.createElement('th');
                th.className = 'hour-header';
                const [hStr] = h.split(':');
                const nextH = String(parseInt(hStr) + 1).padStart(2, '0');
                th.innerHTML = `<span class="hour-top">${h}</span><span class="hour-bottom">${nextH}:00</span>`;
                timeHeaderRow.appendChild(th);
            });
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
                    const cellData = slotData.find(d => d[0] === di && d[1] === hi);

                    if (isSunday || day.holiday) {
                        div.className += ' cell-occupied';
                    } else if (cellData) {
                        if (cellData[2] === 1) {
                            div.className += ' cell-occupied';
                        } else if (cellData[2] === 2) {
                            div.className += ' cell-selected';
                            div.innerHTML = '<span class="sel-text"></span>';
                            selectedCells.push({ day: di, hour: hi, el: div });
                            div.addEventListener('click', () => toggleCell(di, hi, div));
                        } else if (cellData[2] === 3) {
                            div.className += ' cell-pending';
                        } else {
                            div.className += ' cell-available';
                            div.addEventListener('click', () => toggleCell(di, hi, div));
                        }
                    } else {
                        div.className += ' cell-available';
                        div.addEventListener('click', () => toggleCell(di, hi, div));
                    }

                    td.appendChild(div);
                    tr.appendChild(td);
                });

                body.appendChild(tr);
            });

            updateCounter();
        }

        function toggleCell(di, hi, el) {
            if (el.classList.contains('cell-selected')) {
                el.classList.remove('cell-selected');
                el.classList.add('cell-available');
                el.innerHTML = '';
                selectedCells = selectedCells.filter(c => !(c.day === di && c.hour === hi));
                updateCounter();
                return;
            }

            if (el.classList.contains('cell-available')) {
                if (selectedCells.length >= MAX_SELECTION) {
                    alert(`You can only select up to ${MAX_SELECTION} slots.`);
                    return;
                }
                el.classList.remove('cell-available');
                el.classList.add('cell-selected');
                el.innerHTML = '<span class="sel-text"></span>';
                selectedCells.push({ day: di, hour: hi, el });
                updateCounter();
            }
        }

        function clearSelection() {
            selectedCells.forEach(c => {
                c.el.classList.remove('cell-selected');
                c.el.classList.add('cell-available');
                c.el.innerHTML = '';
            });
            selectedCells = [];
            updateCounter();
        }

        function proceed() {
            if (selectedCells.length === 0) {
                alert('Please select at least one timeslot.');
                return;
            }
            alert(`Proceeding with ${selectedCells.length} selected slot(s).`);
        }

        function onWeekChange() {
            buildTimetable();
        }

        function updateIcon(isDark) {
            const icon = document.getElementById('theme-icon');
            if (!icon) return;
            icon.innerHTML = isDark
                ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>'
                : '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        }

        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            html.classList.toggle('light');
            html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
            updateIcon(!isDark);
        }

        document.addEventListener('DOMContentLoaded', function() {
            buildTimetable();
            updateIcon(document.documentElement.classList.contains('dark'));
        });
    </script>
</body>
</html>
