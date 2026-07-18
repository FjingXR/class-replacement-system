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
    <link rel="stylesheet" href="/css/theme.css">
    <style>

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--color-bg);
            color: var(--color-on-bg);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background var(--transition), color var(--transition);
        }

        /* ───── Top Navigation Bar ───── */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: 56px;
            display: flex;
            align-items: center;
            background: var(--color-primary-container);
            padding: 0 20px;
            gap: 0;
        }

        .top-logo {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            cursor: pointer;
            margin-right: 24px;
        }
        .top-logo img {
            height: 48px;
            width: auto;
            display: block;
        }

        .nav-items {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
            height: 100%;
        }
        .nav-item {
            height: 100%;
            display: flex;
            align-items: center;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--color-on-primary-container);
            text-decoration: none;
            position: relative;
            cursor: pointer;
            transition: background 0.15s, opacity 0.15s;
            white-space: nowrap;
            opacity: 0.75;
            border-radius: 0;
        }
        .nav-item:hover {
            opacity: 1;
            background: rgba(7,27,51,0.06);
        }
        .nav-item.active {
            opacity: 1;
            font-weight: 600;
            background: var(--color-tertiary-container);
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            padding-left: 20px;
            height: 100%;
        }

        .theme-toggle {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: background 0.15s, transform 0.15s, opacity 0.15s;
            flex-shrink: 0;
        }
        .theme-toggle:hover {
            opacity: 0.85;
            transform: scale(1.08);
        }
        .theme-toggle:active {
            transform: scale(0.95);
        }

        .notif-btn {
            position: relative;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, opacity 0.15s;
            flex-shrink: 0;
        }
        .notif-btn:hover {
            opacity: 0.85;
        }
        .notif-badge {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: var(--color-error);
            color: var(--color-on-error);
            font-size: 9px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 2px var(--color-primary-container);
        }

        .user-panel {
            display: flex;
            align-items: center;
            gap: 2px;
            background: var(--color-secondary-container);
            border-radius: 50px;
            padding: 3px 4px 3px 8px;
            border: 1px solid rgba(12,51,33,0.12);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--color-on-secondary-container);
            user-select: none;
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--color-on-secondary-container);
            color: var(--color-secondary-container);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }
        .user-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .user-name {
            font-size: 12px;
            font-weight: 600;
        }
        .user-role {
            font-size: 10px;
            opacity: 0.7;
        }

        .logout-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--color-on-secondary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .logout-btn:hover {
            background: rgba(12,51,33,0.1);
            color: var(--color-error);
        }

        /* ───── App Container ───── */
        .app-container {
            width: 100%;
            max-width: 100%;
            padding: 16px 24px;
            padding-top: 72px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ───── Page Header ───── */
        .page-header {
            margin-bottom: 20px;
        }
        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--color-on-bg);
            margin: 0;
        }
        .page-desc {
            font-size: 14px;
            color: var(--color-on-surface-variant);
            margin-top: 4px;
            margin-bottom: 0;
        }

        /* ───── Toolbar ───── */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            flex-wrap: wrap;
            gap: 12px;
        }
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-wrapper {
            position: relative;
        }
        .search-input {
            padding: 8px 12px 8px 36px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 13px;
            width: 260px;
            outline: none;
        }
        .search-input::placeholder {
            color: var(--color-on-surface-variant);
            opacity: 0.5;
        }
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: 0.4;
            color: var(--color-on-surface-variant);
        }
        .filter-dropdown {
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            outline: none;
        }
        .toolbar-right {
            display: flex;
            align-items: center;
        }
        .result-count {
            font-size: 13px;
            color: var(--color-on-surface-variant);
            font-weight: 500;
        }

        /* ───── Grid Wrapper ───── */
        .grid-wrapper {
            margin-top: 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .grid-scroll {
            overflow-x: auto;
        }
        .timetable {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        .timetable th, .timetable td {
            border: 1px solid var(--color-outline);
            text-align: left;
            vertical-align: middle;
            font-size: 13px;
            padding: 10px 12px;
            white-space: nowrap;
            transition: background var(--transition), border-color var(--transition);
        }
        .timetable th {
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .timetable th.sortable {
            cursor: pointer;
            user-select: none;
        }
        .timetable th.sortable:hover {
            background: rgba(0,0,0,0.05);
        }
        .timetable tbody tr:hover td {
            background: rgba(0,0,0,0.03);
        }
        .timetable tbody tr:nth-child(even) td {
            background: rgba(0,0,0,0.015);
        }

        /* ───── Column Widths ───── */
        .col-no { width: 50px; }
        .col-code { width: 200px; }
        .col-type { width: 90px; }
        .col-date { width: 110px; }
        .col-day { width: 80px; }
        .col-time { width: 130px; }
        .col-duration { width: 70px; }
        .col-venue { width: 70px; }
        .col-students { width: 80px; }
        .col-reason { width: 140px; }
        .col-action { width: 150px; }

        /* ───── Sort Arrow ───── */
        .sort-arrow {
            display: inline-block;
            margin-left: 4px;
            font-size: 11px;
            opacity: 0.6;
        }

        /* ───── Cell Styles ───── */
        .cell-code {
            font-weight: 700;
        }
        .cell-name {
            font-weight: 400;
            opacity: 0.7;
            display: block;
            font-size: 12px;
        }

        /* ───── Reason Badges ───── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-holiday {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }
        .badge-annual-leave {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
        }
        .badge-medical-leave {
            background: var(--color-tertiary-container);
            color: var(--color-on-tertiary-container);
        }
        .badge-official-event {
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
        }
        .badge-emergency-leave {
            background: var(--color-error);
            color: white;
        }

        /* ───── Action Button ───── */
        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            background: var(--color-secondary);
            color: var(--color-on-secondary);
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.15s, filter 0.15s;
        }
        .btn-action:hover {
            filter: brightness(1.08);
        }
        .btn-action:active {
            transform: scale(0.97);
        }

        /* ───── Pagination ───── */
        .pagination-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-top: 1px solid var(--color-outline);
        }
        .pagination-info {
            font-size: 13px;
            color: var(--color-on-surface-variant);
        }
        .pagination-controls {
            display: flex;
            gap: 4px;
        }
        .page-btn {
            min-width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .page-btn:hover {
            background: var(--color-surface-variant);
        }
        .page-btn.active {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            border-color: var(--color-primary);
            font-weight: 600;
        }
        .page-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* ───── Empty State ───── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-on-surface-variant);
        }
        .empty-icon {
            opacity: 0.25;
            margin-bottom: 16px;
        }
        .empty-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-on-surface);
            margin: 0 0 6px 0;
        }
        .empty-text {
            font-size: 14px;
            opacity: 0.7;
            margin: 0;
        }

        /* ───── Responsive ───── */
        @media (max-width: 1024px) {
            .grid-scroll { overflow-x: auto; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-left { justify-content: flex-start; }
            .search-input { width: 100%; }
        }
        @media (max-width: 768px) {
            .app-container { padding: 10px 12px; padding-top: 66px; }
            .top-logo { margin-right: 12px; }
            .nav-items { gap: 2px; }
            .nav-item { padding: 0 8px; font-size: 12px; }
            .user-info { display: none; }
            .pagination-bar { flex-direction: column; gap: 8px; align-items: center; }
            .toolbar-left { width: 100%; }
            .search-wrapper { width: 100%; }
            .search-input { width: 100%; box-sizing: border-box; }
            .filter-dropdown { width: 100%; }
            .toolbar-right { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- ═══ Top Navigation Bar ═══ -->
    <div class="top-bar">
        <div class="top-logo" onclick="navigateHome()">
            <img src="/images/logo_banner.png" alt="TAR UMT">
        </div>

        <div class="nav-items">
            <a class="nav-item" href="/dashboard">Dashboard</a>
            <a class="nav-item" href="/my-timetable-ui">My Timetable</a>
            <a class="nav-item" href="#">Cohort Timetables</a>
            <a class="nav-item active" href="/replacement-arrangement">Replacement Arrangement</a>
            <a class="nav-item" href="#">Replacement History</a>
        </div>

        <div class="top-right">
            <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
                <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            <button class="notif-btn" onclick="alert('Notifications panel')" aria-label="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="notif-badge">3</span>
            </button>

            <div class="user-panel">
                <div class="user-profile">
                    <div class="user-avatar">KL</div>
                    <div class="user-info">
                        <span class="user-name">Kylian Mbappe</span>
                        <span class="user-role">Lecturer</span>
                    </div>
                </div>

                <button class="logout-btn" onclick="alert('Logout')" aria-label="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ═══ App Container ═══ -->
    <div class="app-container">

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">Replacement Arrangement</h1>
            <p class="page-desc">The following classes require replacement arrangements. Select a class to submit a replacement request.</p>
        </div>

        <!-- ─── Toolbar ─── -->
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" id="searchInput" placeholder="Search by course code or name...">
                </div>
                <select class="filter-dropdown" id="reasonFilter">
                    <option value="all">All Reasons</option>
                    <option value="Public Holiday">Public Holiday</option>
                    <option value="Annual Leave">Annual Leave</option>
                    <option value="Medical Leave">Medical Leave</option>
                    <option value="Official Event">Official Event</option>
                    <option value="Emergency Leave">Emergency Leave</option>
                </select>
            </div>
            <div class="toolbar-right">
                <span class="result-count" id="resultCount">Showing 14 of 14 classes</span>
            </div>
        </div>

        <!-- ─── Grid Wrapper ─── -->
        <div class="grid-wrapper">
            <div class="grid-scroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- ─── Pagination ─── -->
        <div class="pagination-bar" id="paginationBar">
            <span class="pagination-info" id="paginationInfo">Showing 1-10 of 14</span>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>

        <!-- ─── Empty State ─── -->
        <div class="empty-state" id="emptyState" style="display:none">
            <svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <h3 class="empty-title">No classes currently require replacement arrangements.</h3>
            <p class="empty-text">Try adjusting your search or filter criteria.</p>
        </div>

    </div>

    <script>
        // ═══════════════════════════════════════
        //  Mock Data
        // ═══════════════════════════════════════

        const conflictedClasses = [
            { id: 1, code: 'BMIT5555', name: 'Software Engineering', type: 'L', date: '2026-09-04', day: 'Thursday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 35, conflictReason: 'Public Holiday' },
            { id: 2, code: 'BMIT6666', name: 'Mobile App Development', type: 'T', date: '2026-09-04', day: 'Thursday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B111', totalStudents: 28, conflictReason: 'Public Holiday' },
            { id: 3, code: 'BMIT6767', name: 'Object-Oriented Programming', type: 'L', date: '2026-09-10', day: 'Thursday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B103', totalStudents: 24, conflictReason: 'Annual Leave' },
            { id: 4, code: 'BMIT5678', name: 'Database Systems', type: 'T', date: '2026-09-10', day: 'Thursday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, conflictReason: 'Medical Leave' },
            { id: 5, code: 'BMIT9012', name: 'Computer Networks', type: 'L', date: '2026-09-11', day: 'Friday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, conflictReason: 'Official Event' },
            { id: 6, code: 'BMIT3456', name: 'Artificial Intelligence', type: 'T', date: '2026-09-12', day: 'Saturday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, conflictReason: 'Annual Leave' },
            { id: 7, code: 'BMIT7890', name: 'Project Management', type: 'L', date: '2026-09-15', day: 'Tuesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 20, conflictReason: 'Medical Leave' },
            { id: 8, code: 'BMIT9999', name: 'Machine Learning', type: 'T', date: '2026-09-15', day: 'Tuesday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, conflictReason: 'Emergency Leave' },
            { id: 9, code: 'BMIT1234', name: 'Data Structures', type: 'L', date: '2026-09-18', day: 'Friday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 30, conflictReason: 'Public Holiday' },
            { id: 10, code: 'BMIT2345', name: 'Operating Systems', type: 'T', date: '2026-09-18', day: 'Friday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, conflictReason: 'Public Holiday' },
            { id: 11, code: 'BMIT4567', name: 'Web Development', type: 'L', date: '2026-09-20', day: 'Sunday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, conflictReason: 'Official Event' },
            { id: 12, code: 'BMIT8888', name: 'Cloud Computing', type: 'T', date: '2026-09-22', day: 'Tuesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, conflictReason: 'Annual Leave' },
            { id: 13, code: 'BMIT7777', name: 'Cybersecurity', type: 'L', date: '2026-09-25', day: 'Friday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, conflictReason: 'Medical Leave' },
            { id: 14, code: 'BMIT3333', name: 'Embedded Systems', type: 'T', date: '2026-09-28', day: 'Monday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, conflictReason: 'Emergency Leave' },
        ];

        // ═══════════════════════════════════════
        //  Helper Functions
        // ═══════════════════════════════════════

        function to12h(t) {
            const [hStr, m] = t.split(':');
            const h = parseInt(hStr);
            const ampm = h >= 12 ? 'PM' : 'AM';
            const h12 = h === 0 ? 12 : h > 12 ? h - 12 : h;
            return h12 + ':' + m + ' ' + ampm;
        }

        function formatDate(iso) {
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const parts = iso.split('-');
            return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];
        }

        function badgeClass(reason) {
            const map = {
                'Public Holiday': 'badge-holiday',
                'Annual Leave': 'badge-annual-leave',
                'Medical Leave': 'badge-medical-leave',
                'Official Event': 'badge-official-event',
                'Emergency Leave': 'badge-emergency-leave'
            };
            return map[reason] || '';
        }

        // ═══════════════════════════════════════
        //  State Variables
        // ═══════════════════════════════════════

        let currentPage = 1;
        const pageSize = 10;
        let sortState = { field: '', dir: 'asc' };
        let currentFiltered = [];

        // ═══════════════════════════════════════
        //  Table Builder
        // ═══════════════════════════════════════

        function buildTable() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const reason = document.getElementById('reasonFilter').value;

            let filtered = conflictedClasses.filter(function(c) {
                const matchesSearch = query === '' ||
                    c.code.toLowerCase().includes(query) ||
                    c.name.toLowerCase().includes(query);
                const matchesReason = reason === 'all' || c.conflictReason === reason;
                return matchesSearch && matchesReason;
            });

            if (sortState.field) {
                filtered.sort(function(a, b) {
                    var va, vb;
                    if (sortState.field === 'date') {
                        va = a.date;
                        vb = b.date;
                    } else if (sortState.field === 'code') {
                        va = a.code;
                        vb = b.code;
                    }
                    if (va < vb) return sortState.dir === 'asc' ? -1 : 1;
                    if (va > vb) return sortState.dir === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            currentFiltered = filtered;

            const offset = (currentPage - 1) * pageSize;
            const pageData = filtered.slice(offset, offset + pageSize);

            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            // ── Thead ──
            const tr = document.createElement('tr');
            const columns = [
                { label: '#', cls: 'col-no', sortable: false },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'code' },
                { label: 'Type', cls: 'col-type', sortable: false },
                { label: 'Date', cls: 'col-date', sortable: true, field: 'date' },
                { label: 'Day', cls: 'col-day', sortable: false },
                { label: 'Time', cls: 'col-time', sortable: false },
                { label: 'Hrs', cls: 'col-duration', sortable: false },
                { label: 'Venue', cls: 'col-venue', sortable: false },
                { label: 'Students', cls: 'col-students', sortable: false },
                { label: 'Conflict Reason', cls: 'col-reason', sortable: false },
                { label: 'Action', cls: 'col-action', sortable: false },
            ];
            columns.forEach(function(col) {
                const th = document.createElement('th');
                th.className = col.cls;
                if (col.sortable) {
                    th.classList.add('sortable');
                    var arrow = '';
                    if (sortState.field === col.field) {
                        arrow = '<span class="sort-arrow">' + (sortState.dir === 'asc' ? '&#9650;' : '&#9660;') + '</span>';
                    }
                    th.innerHTML = col.label + arrow;
                    th.addEventListener('click', function() {
                        if (sortState.field === col.field) {
                            sortState.dir = sortState.dir === 'asc' ? 'desc' : 'asc';
                        } else {
                            sortState.field = col.field;
                            sortState.dir = 'asc';
                        }
                        currentPage = 1;
                        buildTable();
                    });
                } else {
                    th.textContent = col.label;
                }
                tr.appendChild(th);
            });
            head.appendChild(tr);

            // ── Tbody ──
            if (pageData.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                pageData.forEach(function(c, i) {
                    const row = document.createElement('tr');
                    var cells = [
                        { html: String(offset + i + 1), cls: 'col-no' },
                        { html: '<span class="cell-code">' + c.code + '</span><span class="cell-name">' + c.name + '</span>', cls: 'col-code' },
                        { html: c.type === 'L' ? 'Lecture' : 'Tutorial', cls: 'col-type' },
                        { html: formatDate(c.date), cls: 'col-date' },
                        { html: c.day, cls: 'col-day' },
                        { html: to12h(c.timeStart) + ' - ' + to12h(c.timeEnd), cls: 'col-time' },
                        { html: String(c.duration) + 'h', cls: 'col-duration' },
                        { html: c.venue, cls: 'col-venue' },
                        { html: String(c.totalStudents), cls: 'col-students' },
                        { html: '<span class="badge ' + badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>', cls: 'col-reason' },
                        { html: '<button class="btn-action" onclick="goToReplacementWith(\'' + c.code + '\',\'' + c.date + '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Arrange Replacement</button>', cls: 'col-action' },
                    ];
                    cells.forEach(function(cell) {
                        const td = document.createElement('td');
                        td.className = cell.cls;
                        td.innerHTML = cell.html;
                        row.appendChild(td);
                    });
                    body.appendChild(row);
                });
            }

            updatePagination();
            updateResultCount();
        }

        // ═══════════════════════════════════════
        //  Pagination
        // ═══════════════════════════════════════

        function updatePagination() {
            const totalPages = Math.ceil(currentFiltered.length / pageSize);
            const info = document.getElementById('paginationInfo');
            if (currentFiltered.length === 0) {
                info.textContent = 'Showing 0 of 0';
            } else {
                const from = (currentPage - 1) * pageSize + 1;
                const to = Math.min(currentPage * pageSize, currentFiltered.length);
                info.textContent = 'Showing ' + from + '-' + to + ' of ' + currentFiltered.length;
            }

            const controls = document.getElementById('paginationControls');
            controls.innerHTML = '';

            const prev = document.createElement('button');
            prev.className = 'page-btn';
            prev.textContent = '\u2039';
            prev.disabled = currentPage <= 1;
            prev.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    buildTable();
                }
            });
            controls.appendChild(prev);

            for (var p = 1; p <= totalPages; p++) {
                (function(page) {
                    const btn = document.createElement('button');
                    btn.className = 'page-btn';
                    if (page === currentPage) btn.classList.add('active');
                    btn.textContent = String(page);
                    btn.addEventListener('click', function() {
                        currentPage = page;
                        buildTable();
                    });
                    controls.appendChild(btn);
                })(p);
            }

            const next = document.createElement('button');
            next.className = 'page-btn';
            next.textContent = '\u203A';
            next.disabled = currentPage >= totalPages;
            next.addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    buildTable();
                }
            });
            controls.appendChild(next);
        }

        function updateResultCount() {
            const count = document.getElementById('resultCount');
            count.textContent = 'Showing ' + currentFiltered.length + ' of ' + conflictedClasses.length + ' classes';
        }

        // ═══════════════════════════════════════
        //  Theme
        // ═══════════════════════════════════════

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

        function navigateHome() {
            window.location.href = '/';
        }

        function goToReplacement() {
            window.location.href = '/replacement-arrangement';
        }

        function goToReplacementWith(code, date) {
            window.location.href = '/replacement-arrangement?code=' + encodeURIComponent(code) + '&date=' + encodeURIComponent(date);
        }

        // ═══════════════════════════════════════
        //  Initialization
        // ═══════════════════════════════════════

        document.addEventListener('DOMContentLoaded', function() {
            buildTable();
            updateIcon(document.documentElement.classList.contains('dark'));
            document.getElementById('searchInput').addEventListener('input', function() {
                currentPage = 1;
                buildTable();
            });
            document.getElementById('reasonFilter').addEventListener('change', function() {
                currentPage = 1;
                buildTable();
            });
        });
    </script>
</body>
</html>
