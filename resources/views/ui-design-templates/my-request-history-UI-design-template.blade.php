<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Request History — Class Replacement System</title>
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
            width: 320px;
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
        .toolbar-right {
            display: flex;
            align-items: center;
        }
        .result-count {
            font-size: 13px;
            color: var(--color-on-surface-variant);
            font-weight: 500;
        }

        .filter-select {
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            outline: none;
            cursor: pointer;
        }

        /* ───── Sort Hint ───── */
        .sort-hint {
            text-align: left;
            font-size: 12px;
            color: var(--color-on-surface-variant);
            opacity: 0.5;
            margin-top: 8px;
            margin-bottom: -4px;
            font-style: italic;
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
            min-width: 1350px;
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
        .col-requested-at { width: 145px; }
        .col-code { width: 200px; }
        .col-type { width: 80px; }
        .col-date { width: 100px; }
        .col-day { width: 90px; }
        .col-time { width: 140px; }
        .col-duration { width: 65px; }
        .col-venue { width: 75px; }
        .col-students { width: 80px; }
        .col-cohort { width: 120px; }
        .col-status { width: 130px; }

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

        /* ───── Status Badges ───── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .badge:hover {
            filter: brightness(1.1);
        }
        .badge-subtitle {
            display: block;
            font-size: 11px;
            font-weight: 400;
            opacity: 0.7;
            margin-top: 2px;
        }
        .status-pending {
            background: var(--color-tertiary-container);
            color: var(--color-on-tertiary-container);
        }
        .status-approved {
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
        }
        .status-rejected {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }
        .status-cancelled {
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
        }
        .status-completed {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
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

        /* ───── Summary Stat Cards ───── */
        .summary-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            margin-top: 20px;
        }
        .summary-card {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            padding: 14px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }
        .summary-value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }
        .summary-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--color-on-surface-variant);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-card.card-total .summary-value { color: var(--color-error); }
        .summary-card.card-approved .summary-value { color: var(--color-secondary); }
        .summary-card.card-pending .summary-value { color: var(--color-tertiary); }
        .summary-card.card-rejected .summary-value { color: var(--color-on-primary-container); }

        /* ───── Modal ───── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show {
            display: flex;
        }
        .modal {
            background: var(--color-surface);
            border-radius: var(--radius-md);
            max-width: 520px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            border-bottom: 1px solid var(--color-outline);
        }
        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-on-surface);
            margin: 0;
        }
        .modal-close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: var(--color-on-surface-variant);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: background 0.15s;
        }
        .modal-close:hover {
            background: var(--color-surface-variant);
        }
        .modal-body {
            padding: 20px 24px;
        }
        .modal-field {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px solid var(--color-outline);
            border-bottom-width: 0.5px;
        }
        .modal-field:last-child {
            border-bottom: none;
        }
        .modal-field-label {
            width: 130px;
            flex-shrink: 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
        }
        .modal-field-value {
            flex: 1;
            font-size: 13px;
            color: var(--color-on-surface);
        }
        .modal-footer {
            padding: 12px 24px;
            border-top: 1px solid var(--color-outline);
            text-align: right;
        }
        .btn-outline {
            padding: 8px 20px;
            border-radius: 8px;
            border: 1px solid var(--color-outline);
            background: transparent;
            color: var(--color-on-surface);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-outline:hover {
            background: var(--color-surface-variant);
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
        .empty-cta {
            margin-top: 16px;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            background: var(--color-secondary);
            color: var(--color-on-secondary);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .empty-cta:hover {
            filter: brightness(1.08);
        }

        /* ───── Responsive ───── */
        @media (max-width: 1024px) {
            .grid-scroll { overflow-x: auto; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-left { justify-content: flex-start; }
            .search-input { width: 100%; }
            .filter-select { width: auto; flex: 0 0 auto; }
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
            .filter-select { width: 100%; }
            .toolbar-right { width: 100%; justify-content: center; }
            .summary-bar { grid-template-columns: repeat(2, 1fr); }
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
            <a class="nav-item" href="/replacement-arrangement">Replacement Arrangement</a>
            <a class="nav-item active" href="/my-request-history-ui">Replacement History</a>
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
            <h1 class="page-title">My Request History</h1>
            <p class="page-desc">View and monitor all replacement requests submitted during the current semester.</p>
        </div>

        <!-- ─── Toolbar ─── -->
        <div class="toolbar">
            <div class="toolbar-left">
                <select class="filter-select" id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Completed">Completed</option>
                </select>
                <select class="filter-select" id="weekFilter">
                    <option value="all">All Weeks</option>
                </select>
            </div>
            <div class="toolbar-right">
                <div class="search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" id="searchInput" placeholder="Search course code or name...">
                </div>
                <span class="result-count" id="resultCount" style="margin-left:12px">Showing 20 of 20 results</span>
            </div>
        </div>

        <div class="sort-hint">Click column headers to sort (Requested Time, Course Code, Date)</div>

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
            <span class="pagination-info" id="paginationInfo">Showing 1-10 of 20</span>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>

        <!-- ─── Summary Stat Cards ─── -->
        <div class="summary-bar" id="summaryBar">
            <div class="summary-card card-total">
                <span class="summary-value" id="summaryTotal">0</span>
                <span class="summary-label">Total Requests</span>
            </div>
            <div class="summary-card card-approved">
                <span class="summary-value" id="summaryApproved">0</span>
                <span class="summary-label">Approved</span>
            </div>
            <div class="summary-card card-pending">
                <span class="summary-value" id="summaryPending">0</span>
                <span class="summary-label">Pending</span>
            </div>
            <div class="summary-card card-rejected">
                <span class="summary-value" id="summaryRejected">0</span>
                <span class="summary-label">Rejected</span>
            </div>
        </div>

        <!-- ─── Empty State ─── -->
        <div class="empty-state" id="emptyState" style="display:none">
            <svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <h3 class="empty-title" id="emptyTitle">You haven't submitted any replacement requests for this semester.</h3>
            <p class="empty-text" id="emptyText">Submit a replacement request for any conflicted class.</p>
            <button class="empty-cta" id="emptyCta" onclick="window.location.href='/replacement-arrangement'" style="display:none">Submit a Replacement Request</button>
        </div>

    </div>

    <!-- ═══ View Details Modal ═══ -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="detailsModal">
            <div class="modal-header">
                <h3 class="modal-title">Request Details</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // ═══════════════════════════════════════
        //  Mock Data
        // ═══════════════════════════════════════

        const mockRequests = [
            { id: 1, requestedAt: '2026-08-30T10:30:00', courseCode: 'BMIT5555', courseName: 'Software Engineering', classType: 'L', classDate: '2026-08-31', classDay: 'Monday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B104', totalStudents: 35, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 2, requestedAt: '2026-08-31T14:15:00', courseCode: 'BMIT5555', courseName: 'Software Engineering', classType: 'T', classDate: '2026-09-02', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B105', totalStudents: 28, cohorts: ['DFT2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-04', replacementTime: '14:00 – 16:00', replacementVenue: 'B110', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-01T09:00:00', remarks: null },
            { id: 3, requestedAt: '2026-09-01T08:45:00', courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming', classType: 'L', classDate: '2026-09-03', classDay: 'Thursday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B103', totalStudents: 24, cohorts: ['DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 4, requestedAt: '2026-09-02T11:20:00', courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming', classType: 'T', classDate: '2026-09-07', classDay: 'Monday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B106', totalStudents: 20, cohorts: ['DSF2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-09', replacementTime: '11:00 – 13:00', replacementVenue: 'B201', reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-03T16:30:00', remarks: null },
            { id: 5, requestedAt: '2026-09-03T09:10:00', courseCode: 'BMIT5678', courseName: 'Database Systems', classType: 'T', classDate: '2026-09-08', classDay: 'Tuesday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, cohorts: ['DSF2 (S1)', 'DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 6, requestedAt: '2026-09-04T15:00:00', courseCode: 'BMIT9012', courseName: 'Computer Networks', classType: 'L', classDate: '2026-09-10', classDay: 'Thursday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, cohorts: ['DFT2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-14', replacementTime: '08:00 – 10:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-07T10:00:00', remarks: null },
            { id: 7, requestedAt: '2026-09-05T13:30:00', courseCode: 'BMIT3456', courseName: 'Artificial Intelligence', classType: 'T', classDate: '2026-09-11', classDay: 'Friday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, cohorts: ['DSF2 (S1)'], status: 'Rejected', rejectionReason: 'Insufficient notice period. Requests must be submitted at least 5 working days in advance.', replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-08T08:15:00', remarks: null },
            { id: 8, requestedAt: '2026-09-06T10:00:00', courseCode: 'BMIT7890', courseName: 'Project Management', classType: 'L', classDate: '2026-09-14', classDay: 'Monday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 20, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 9, requestedAt: '2026-09-07T16:45:00', courseCode: 'BMIT7890', courseName: 'Project Management', classType: 'T', classDate: '2026-09-15', classDay: 'Tuesday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, cohorts: ['DFT2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-17', replacementTime: '08:00 – 10:00', replacementVenue: 'B103', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-09T14:00:00', remarks: 'Replacement conducted successfully.' },
            { id: 10, requestedAt: '2026-09-08T07:30:00', courseCode: 'BMIT9999', courseName: 'Machine Learning', classType: 'T', classDate: '2026-09-16', classDay: 'Wednesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 15, cohorts: ['DSF2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-18', replacementTime: '10:00 – 12:00', replacementVenue: 'B105', reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-10T11:00:00', remarks: null },
            { id: 11, requestedAt: '2026-09-09T12:15:00', courseCode: 'BMIT1234', courseName: 'Data Structures', classType: 'L', classDate: '2026-09-18', classDay: 'Friday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 30, cohorts: ['DSF2 (S1)'], status: 'Rejected', rejectionReason: 'Venue unavailable on the requested replacement date.', replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-11T09:30:00', remarks: null },
            { id: 12, requestedAt: '2026-09-10T14:00:00', courseCode: 'BMIT4567', courseName: 'Web Development', classType: 'L', classDate: '2026-09-21', classDay: 'Monday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, cohorts: ['DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 13, requestedAt: '2026-09-11T08:30:00', courseCode: 'BMIT4567', courseName: 'Web Development', classType: 'T', classDate: '2026-09-22', classDay: 'Tuesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-24', replacementTime: '14:00 – 16:00', replacementVenue: 'B106', reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-14T15:45:00', remarks: 'Replacement completed. Student attendance recorded.' },
            { id: 14, requestedAt: '2026-09-12T10:45:00', courseCode: 'BMIT8888', courseName: 'Cloud Computing', classType: 'T', classDate: '2026-09-23', classDay: 'Wednesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, cohorts: ['DSF2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-25', replacementTime: '10:00 – 12:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-15T13:00:00', remarks: null },
            { id: 15, requestedAt: '2026-09-13T09:00:00', courseCode: 'BMIT7777', courseName: 'Cybersecurity', classType: 'L', classDate: '2026-08-31', classDay: 'Monday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Cancelled', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: 'Request withdrawn by lecturer.' },
            { id: 16, requestedAt: '2026-09-14T11:30:00', courseCode: 'BMIT7777', courseName: 'Cybersecurity', classType: 'T', classDate: '2026-09-02', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, cohorts: ['DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 17, requestedAt: '2026-09-15T15:30:00', courseCode: 'BMIT3344', courseName: 'Embedded Systems', classType: 'T', classDate: '2026-09-07', classDay: 'Monday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B103', totalStudents: 12, cohorts: ['DSF2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-10', replacementTime: '08:00 – 10:00', replacementVenue: 'B104', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-16T08:00:00', remarks: 'Replacement completed.' },
            { id: 18, requestedAt: '2026-09-16T07:15:00', courseCode: 'BMIT2222', courseName: 'Mobile Computing', classType: 'L', classDate: '2026-09-09', classDay: 'Wednesday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 28, cohorts: ['DFT2 (S1)'], status: 'Cancelled', rejectionReason: null, replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 19, requestedAt: '2026-09-17T13:00:00', courseCode: 'BMIT1111', courseName: 'Human-Computer Interaction', classType: 'L', classDate: '2026-09-15', classDay: 'Tuesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 22, cohorts: ['DSF2 (S1)'], status: 'Rejected', rejectionReason: 'Scheduling conflict with another lecturer\'s booking.', replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-18T10:30:00', remarks: null },
            { id: 20, requestedAt: '2026-09-18T09:45:00', courseCode: 'BMIT4433', courseName: 'Information Security', classType: 'T', classDate: '2026-09-17', classDay: 'Thursday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 18, cohorts: ['DFT2 (S1)'], status: 'Rejected', rejectionReason: 'Lecturer unavailable on the requested date.', replacementDate: null, replacementTime: null, replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-19T08:00:00', remarks: null },
        ];

        // ═══════════════════════════════════════
        //  Week Range Definitions
        // ═══════════════════════════════════════

        const weekRanges = [
            { value: '1', label: 'Week 1: 31 Aug – 6 Sep', start: '2026-08-31', end: '2026-09-06' },
            { value: '2', label: 'Week 2: 7 Sep – 13 Sep', start: '2026-09-07', end: '2026-09-13' },
            { value: '3', label: 'Week 3: 14 Sep – 20 Sep', start: '2026-09-14', end: '2026-09-20' },
            { value: '4', label: 'Week 4: 21 Sep – 27 Sep', start: '2026-09-21', end: '2026-09-27' },
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

        function formatDateTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const day = d.getDate();
            const month = months[d.getMonth()];
            const year = d.getFullYear();
            let h = d.getHours();
            const m = String(d.getMinutes()).padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h === 0 ? 12 : h > 12 ? h - 12 : h;
            return day + ' ' + month + ' ' + year + ', ' + h + ':' + m + ' ' + ampm;
        }

        function statusClass(status) {
            const map = {
                'Pending': 'status-pending',
                'Approved': 'status-approved',
                'Rejected': 'status-rejected',
                'Cancelled': 'status-cancelled',
                'Completed': 'status-completed'
            };
            return map[status] || '';
        }

        function getWeekRange(weekVal) {
            const found = weekRanges.find(function(w) { return w.value === weekVal; });
            return found || null;
        }

        function isInWeek(classDate, weekVal) {
            if (weekVal === 'all') return true;
            const range = getWeekRange(weekVal);
            if (!range) return true;
            return classDate >= range.start && classDate <= range.end;
        }

        // ═══════════════════════════════════════
        //  State Variables
        // ═══════════════════════════════════════

        let currentPage = 1;
        const pageSize = 10;
        let sortState = { field: 'requestedAt', dir: 'asc' };
        let currentFiltered = [];

        // ═══════════════════════════════════════
        //  Table Builder
        // ═══════════════════════════════════════

        function renderTable() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const statusVal = document.getElementById('statusFilter').value;
            const weekVal = document.getElementById('weekFilter').value;

            let filtered = mockRequests.filter(function(r) {
                const matchesSearch = query === '' ||
                    r.courseCode.toLowerCase().includes(query) ||
                    r.courseName.toLowerCase().includes(query);
                const matchesStatus = statusVal === 'all' || r.status === statusVal;
                const matchesWeek = isInWeek(r.classDate, weekVal);
                return matchesSearch && matchesStatus && matchesWeek;
            });

            if (sortState.field) {
                filtered.sort(function(a, b) {
                    var va, vb;
                    if (sortState.field === 'requestedAt') {
                        va = a.requestedAt;
                        vb = b.requestedAt;
                    } else if (sortState.field === 'courseCode') {
                        va = a.courseCode;
                        vb = b.courseCode;
                    } else if (sortState.field === 'classDate') {
                        va = a.classDate;
                        vb = b.classDate;
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
                { label: 'Requested Time', cls: 'col-requested-at', sortable: true, field: 'requestedAt' },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'courseCode' },
                { label: 'Type', cls: 'col-type', sortable: false },
                { label: 'Date', cls: 'col-date', sortable: true, field: 'classDate' },
                { label: 'Day', cls: 'col-day', sortable: false },
                { label: 'Time', cls: 'col-time', sortable: false },
                { label: 'Hrs', cls: 'col-duration', sortable: false },
                { label: 'Venue', cls: 'col-venue', sortable: false },
                { label: 'Students', cls: 'col-students', sortable: false },
                { label: 'Cohort(s)', cls: 'col-cohort', sortable: false },
                { label: 'Status', cls: 'col-status', sortable: false },
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
                        renderTable();
                    });
                } else {
                    th.textContent = col.label;
                }
                tr.appendChild(th);
            });
            head.appendChild(tr);

            // ── Tbody ──
            const isFullyEmpty = mockRequests.length === 0;
            const isFilteredEmpty = pageData.length === 0;

            if (isFullyEmpty) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = "You haven't submitted any replacement requests for this semester.";
                document.getElementById('emptyText').textContent = 'Submit a replacement request for any conflicted class.';
                document.getElementById('emptyCta').style.display = 'inline-block';
                document.getElementById('gridWrapper').style.display = 'none';
                document.getElementById('paginationBar').style.display = 'none';
                document.getElementById('summaryBar').style.display = 'none';
            } else if (isFilteredEmpty) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'No replacement requests match your search or filter criteria.';
                document.getElementById('emptyText').textContent = 'Try adjusting your filters.';
                document.getElementById('emptyCta').style.display = 'none';
                document.getElementById('gridWrapper').style.display = 'block';
                document.getElementById('paginationBar').style.display = 'none';
                document.getElementById('summaryBar').style.display = 'block';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                document.getElementById('gridWrapper').style.display = 'block';
                document.getElementById('paginationBar').style.display = 'flex';
                document.getElementById('summaryBar').style.display = 'grid';

                pageData.forEach(function(r, i) {
                    const row = document.createElement('tr');
                    const badgeHtml = '<span class="badge ' + statusClass(r.status) + '" onclick="openModal(' + (offset + i) + ')">' + r.status + (r.status === 'Rejected' && r.rejectionReason ? '<span class="badge-subtitle">' + r.rejectionReason.substring(0, 30) + '...</span>' : '') + '</span>';
                    var cells = [
                        { html: String(offset + i + 1), cls: 'col-no' },
                        { html: formatDateTime(r.requestedAt), cls: 'col-requested-at' },
                        { html: '<span class="cell-code">' + r.courseCode + '</span><span class="cell-name">' + r.courseName + '</span>', cls: 'col-code' },
                        { html: r.classType === 'L' ? 'Lecture' : 'Tutorial', cls: 'col-type' },
                        { html: formatDate(r.classDate), cls: 'col-date' },
                        { html: r.classDay, cls: 'col-day' },
                        { html: to12h(r.timeStart) + ' – ' + to12h(r.timeEnd), cls: 'col-time' },
                        { html: String(r.duration) + '.0h', cls: 'col-duration' },
                        { html: r.venue, cls: 'col-venue' },
                        { html: String(r.totalStudents), cls: 'col-students' },
                        { html: r.cohorts.join('<br>'), cls: 'col-cohort' },
                        { html: badgeHtml, cls: 'col-status' },
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

            document.getElementById('gridWrapper').querySelector('.grid-scroll').scrollLeft = 0;
            updatePagination();
            updateResultCount();
            updateSummary();
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
                info.textContent = 'Showing ' + from + '–' + to + ' of ' + currentFiltered.length;
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
                    renderTable();
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
                        renderTable();
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
                    renderTable();
                }
            });
            controls.appendChild(next);
        }

        function updateResultCount() {
            const count = document.getElementById('resultCount');
            count.textContent = 'Showing ' + currentFiltered.length + ' of ' + mockRequests.length + ' results';
        }

        // ═══════════════════════════════════════
        //  Summary
        // ═══════════════════════════════════════

        function updateSummary() {
            const total = mockRequests.length;
            const approved = mockRequests.filter(function(r) { return r.status === 'Approved'; }).length;
            const pending = mockRequests.filter(function(r) { return r.status === 'Pending'; }).length;
            const rejected = mockRequests.filter(function(r) { return r.status === 'Rejected'; }).length;

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryApproved').textContent = approved;
            document.getElementById('summaryPending').textContent = pending;
            document.getElementById('summaryRejected').textContent = rejected;
        }

        // ═══════════════════════════════════════
        //  Modal
        // ═══════════════════════════════════════

        function openModal(index) {
            const r = currentFiltered[index];
            if (!r) return;
            const body = document.getElementById('modalBody');
            var html = '';

            function field(label, value) {
                if (value === null || value === '') return '';
                return '<div class="modal-field"><span class="modal-field-label">' + label + '</span><span class="modal-field-value">' + value + '</span></div>';
            }

            html += field('Request No.', '#' + r.id);
            html += field('Requested Time', formatDateTime(r.requestedAt));
            html += field('Status', '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>');
            html += field('Course Code', r.courseCode);
            html += field('Course Name', r.courseName);
            html += field('Class Type', r.classType === 'L' ? 'Lecture' : 'Tutorial');
            html += field('Cohort(s)', r.cohorts.join(', '));
            html += field('Original Date', formatDate(r.classDate));
            html += field('Day', r.classDay);
            html += field('Time', to12h(r.timeStart) + ' – ' + to12h(r.timeEnd));
            html += field('Duration', String(r.duration) + ' hours');
            html += field('Venue', r.venue);
            html += field('Total Students', String(r.totalStudents));
            html += field('Replacement Date', r.replacementDate ? formatDate(r.replacementDate) : null);
            html += field('Replacement Time', r.replacementTime);
            html += field('Replacement Venue', r.replacementVenue);
            html += field('Reviewed By', r.reviewedBy);
            html += field('Reviewed At', r.reviewedAt ? formatDateTime(r.reviewedAt) : null);
            html += field(r.status === 'Rejected' ? 'Rejection Reason' : 'Remarks', r.remarks);

            body.innerHTML = html;
            document.getElementById('modalOverlay').classList.add('show');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('show');
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

        // ═══════════════════════════════════════
        //  Initialization
        // ═══════════════════════════════════════

        document.addEventListener('DOMContentLoaded', function() {
            // Populate week dropdown
            const weekSel = document.getElementById('weekFilter');
            weekSel.innerHTML = '<option value="all">All Weeks</option>';
            weekRanges.forEach(function(w) {
                const opt = document.createElement('option');
                opt.value = w.value;
                opt.textContent = w.label;
                weekSel.appendChild(opt);
            });

            renderTable();
            updateIcon(document.documentElement.classList.contains('dark'));

            // Wire toolbar handlers
            document.getElementById('searchInput').addEventListener('input', function() {
                currentPage = 1;
                renderTable();
            });
            document.getElementById('statusFilter').addEventListener('change', function() {
                currentPage = 1;
                renderTable();
            });
            document.getElementById('weekFilter').addEventListener('change', function() {
                currentPage = 1;
                renderTable();
            });

            // Modal overlay click to close
            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
            // Escape key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        });
    </script>
</body>
</html>
