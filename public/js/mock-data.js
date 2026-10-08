// ═══════════════════════════════════════════════════════════════════════════
// public/js/mock-data.js — single source of truth for all UI design templates
// (5 pages + the request-approval page's compatibility globals below).
//
// THIS FILE IS THROWAWAY-BY-DESIGN: when Sprint 3 wires real Livewire/DB data,
// pages swap `MockData.*` references for backend props and this file is deleted.
//
// READ-ONLY: pages MUST treat `MockData` as immutable shared state. Derive
// local copies (slice()/spread) before mutating — mutating MockData directly
// contaminates other pages. See `.sdd/changes/centralized-mock-data/design.md`
// §2.6 for the one page (MyTimetable) that previously mutated an inline array.
//
// Sections mirror the real seed datasets (dataset/cohorts.md, dataset/lecturers.md,
// CodingMAIN.md §3 venue matrix + §8 fixed student counts) so names align with
// the Sprint-1 PostgreSQL seed. Per-page event/request `cohort`/`lecturer`/`venue`
// fields are FREE-TEXT display strings (some reference names not in the registries,
// e.g. venue `A101`, cohort `CSF2 (S1)`) — they are NOT joined to the registries.
// ═══════════════════════════════════════════════════════════════════════════

window.MockData = {

    // ─────────────────────────────────────────────────────────────────────
    // §2.1  semester — canonical week-1 Monday (fixes the 2026-06-15 vs
    // 2026-08-31 drift across MyTimetable/CohortTimetable/ReplacementHome).
    // `chipText` is precomputed here in hyphen-format (matches the existing
    // static chip on all 5 pages); pages render it verbatim — do NOT use the
    // page-local `fmt`/`formatDate` helpers for the chip (they produce spaces).
    // ─────────────────────────────────────────────────────────────────────
    semester: (function () {
        const fmtChip = d =>
            String(d.getDate()).padStart(2, '0') + '-' +
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][d.getMonth()] + '-' +
            d.getFullYear();
        const start = new Date(2026, 8, 21);   // Week-1 Monday (local time)
        const end = new Date(2026, 11, 27);      // Week-14 Sunday (start + 13*7 + 6 days)
        return {
            label: '202605 Semester',
            startDate: '2026-09-21',
            endDate: '2026-12-27',
            weeks: 14,
            chipText: '202605 Semester · ' + fmtChip(start) + ' ~ ' + fmtChip(end),
        };
    })(),

    // ─────────────────────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────
    // §2.2c  mockNow — fixed "today" anchor for demo stability.
    // DateHelper.getTodayMs() (and therefore the Today button, the week
    // generation's `today` flags and the 3-working-day lead-time rule)
    // derives from THIS date, not the real clock: a real clock would
    // progressively push every week into the lead-time blackout until
    // nothing is selectable. Mon 5 Oct 2026 = the Monday of Week 3 in the
    // current semester data (2026-09-21 → 2026-12-27) — update together with
    // `semester.startDate` / `semester.weeks` if the demo window moves.
    //
    // TODO(backend): when the real system lands, DELETE this anchor —
    // DateHelper.getTodayMs() already falls back to the real clock
    // (`new Date()`) whenever mockNow is absent, so removing this one
    // line switches the whole app to the live date/today with no other
    // code changes.
    // ─────────────────────────────────────────────────────────────────────
    mockNow: new Date(2026, 9, 5),

    // §2.2  holidays — declarative; CONSUMED BY CohortTimetable + Student My Timetable.
    // MyTimetable has no holiday render path today (explicit non-goal).
    // Replacement-arrangement's holiday stays embedded inside `arrangementWeeks`
    // (page-specific) — NOT duplicated here, to avoid double-sourcing.
    // ─────────────────────────────────────────────────────────────────────
    // Week = 1-based label (`Week 1` = 2026-09-21..09-27), dayIndex 0 = Mon .. 6 = Sun.
    holidays: [
        { week: 7, dayIndex: 6, label: 'Deepavali' },                    // Sun 08 Nov 2026
        { week: 8, dayIndex: 0, label: 'Deepavali Holiday (In Lieu)' },  // Mon 09 Nov 2026
        { week: 14, dayIndex: 3, label: 'Christmas Eve' },               // Thu 24 Dec 2026 (Sabah)
        { week: 14, dayIndex: 4, label: 'Christmas Day' },               // Fri 25 Dec 2026
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.2b  currentUser — logged-in staff (mock phase).
    // ─────────────────────────────────────────────────────────────────────
    currentUser: { name: 'En. Lim Jia Zheng', staffId: '5770' },

    // ─────────────────────────────────────────────────────────────────────
    // §2.3  cohorts — registry mirroring dataset/cohorts.md + CodingMAIN §8
    // fixed student counts (mirrors DatabaseSeeder::STUDENT_COUNTS).
    // Codes use the dataset's own display format (DFT/DSF omit G1, RSD/RAF/RBU
    // include it). Registry only — page event `cohort` fields are not joined.
    // ─────────────────────────────────────────────────────────────────────
    cohorts: [
        { code: 'DFT1(S1)',   programme: 'Diploma in Information Technology',                       year: 1, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2025', faculty: 'FOCS', studentCount: 30 },
        { code: 'DFT2(S1)',   programme: 'Diploma in Information Technology',                       year: 2, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2024', faculty: 'FOCS', studentCount: 28 },
        { code: 'DSF1(S1)',   programme: 'Diploma in Software Engineering',                        year: 1, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2025', faculty: 'FOCS', studentCount: 24 },
        { code: 'DSF2(S1)',   programme: 'Diploma in Software Engineering',                        year: 2, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2024', faculty: 'FOCS', studentCount: 22 },
        { code: 'RSD1(S1)G1', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 1, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2025', faculty: 'FOCS', studentCount: 18 },
        { code: 'RSD2(S1)G1', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 2, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2024', faculty: 'FOCS', studentCount: 16 },
        { code: 'RSD2(S1)G2', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 2, semester: 1, group: 2, academicYear: '2025/26', intake: 'June 2024', faculty: 'FOCS', studentCount: 16 },
        { code: 'RSD2(S1)G3', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 2, semester: 1, group: 3, academicYear: '2025/26', intake: 'June 2024', faculty: 'FOCS', studentCount: 15 },
        { code: 'RSD3(S1)G1', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 3, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2023', faculty: 'FOCS', studentCount: 14 },
        { code: 'RSD3(S1)G2', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 3, semester: 1, group: 2, academicYear: '2025/26', intake: 'June 2023', faculty: 'FOCS', studentCount: 14 },
        { code: 'RSD3(S1)G3', programme: 'Bachelor in IT (Hons) Software Systems Development',   year: 3, semester: 1, group: 3, academicYear: '2025/26', intake: 'June 2023', faculty: 'FOCS', studentCount: 13 },
        { code: 'RAF2(S3)G2', programme: 'Bachelor in Accountancy',                                year: 2, semester: 3, group: 2, academicYear: '2025/26', intake: 'June 2024', faculty: 'FAFB', studentCount: 12 },
        { code: 'RAF2(S3)G4', programme: 'Bachelor in Accountancy',                                year: 2, semester: 3, group: 4, academicYear: '2025/26', intake: 'June 2024', faculty: 'FAFB', studentCount: 10 },
        { code: 'RBU1(S1)G1', programme: 'Bachelor in Business Administration',                    year: 1, semester: 1, group: 1, academicYear: '2025/26', intake: 'June 2025', faculty: 'FAFB', studentCount: 20 },
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.4  lecturers — registry mirroring dataset/lecturers.md (14 staff).
    // `isPl` = role === 'Programme Leader' → rows 5425 and 5516.
    // Registry only — page event `lecturer` fields (free-text, e.g.
    // 'Dr. Christopher Lazarus', 'Dr. Tan Ah Meng') are NOT joined.
    // ─────────────────────────────────────────────────────────────────────
    lecturers: [
        { staffId: '5425', name: 'Pn. Surayaini Binti Basri',         role: 'Programme Leader',    department: 'DCIT', isPl: true,  email: 'surayaini@tarumt.edu.my' },
        { staffId: '5516', name: 'En. Mohd Nur Rahmat Bin Mohd Taat', role: 'Programme Leader',    department: 'DCIT', isPl: true,  email: 'rahmat@tarumt.edu.my' },
        { staffId: '4288', name: 'Dr. Christopher Lazarus',           role: 'Assistant Professor', department: 'DCIT', isPl: false, email: 'christopher@tarumt.edu.my' },
        { staffId: '3221', name: 'Pn. Lee Yee Fong',                  role: 'Senior Lecturer',     department: 'DCIT', isPl: false, email: 'leeyf@tarumt.edu.my' },
        { staffId: '3825', name: 'Pn. Teng Nga Sing',                 role: 'Senior Lecturer',     department: 'DCIT', isPl: false, email: 'tengns@tarumt.edu.my' },
        { staffId: '4127', name: 'Pn. Patricia G Kissol',             role: 'Senior Lecturer',     department: 'DCIT', isPl: false, email: 'patricia@tarumt.edu.my' },
        { staffId: '2873', name: 'Cik Ellis Chieng',                  role: 'Lecturer',            department: 'DCIT', isPl: false, email: 'ellis@tarumt.edu.my' },
        { staffId: '5514', name: 'Ts. Norshikin Binti Zainal Abidin', role: 'Lecturer',            department: 'DCIT', isPl: false, email: 'norshikin@tarumt.edu.my' },
        { staffId: '5599', name: 'En. Jefther Edward',                role: 'Lecturer',            department: 'DCIT', isPl: false, email: 'jefther@tarumt.edu.my' },
        { staffId: '5652', name: 'En. Daniel Royd Michael',           role: 'Lecturer',            department: 'DCIT', isPl: false, email: 'daniel@tarumt.edu.my' },
        { staffId: '5770', name: 'En. Lim Jia Zheng',                 role: 'Lecturer',            department: 'DCIT', isPl: false, email: 'limjz@tarumt.edu.my' },
        { staffId: '3799', name: 'En. Muada Bin Ojih',                role: 'Lecturer',            department: 'DSSH', isPl: false, email: 'muada@tarumt.edu.my' },
        { staffId: '4363', name: 'Pn. Tan Sharon',                    role: 'Senior Lecturer',     department: 'DACB', isPl: false, email: 'sharon@tarumt.edu.my' },
        { staffId: '5254', name: 'Dr. Chang Foo Chung',               role: 'Assistant Professor', department: 'DACB', isPl: false, email: 'changfc@tarumt.edu.my' },
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.5  venues — registry of the 23 Block B rooms (CodingMAIN §3).
    // capacities: Tutorial ≤35, LectureHall >35 (80 placeholder), Lab 28,
    // CiscoLab 32. Registry only — page event `venue` fields (free-text,
    // e.g. A101–A106, B201–B205, B301–B305, C201–C202) are NOT joined.
    // ─────────────────────────────────────────────────────────────────────
    venues: [
        // Tutorial Rooms (capacity 35, allowed L/T)
        { code: 'B002', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B014', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B015', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B016', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B017', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B018', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B100', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B101', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B102', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B103', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B104', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B105', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B106', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B107', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B108', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        { code: 'B109', type: 'Tutorial',   capacity: 35, allowedSessions: ['L', 'T'] },
        // Lecture Halls (capacity >35, large multi-cohort assemblies → L)
        { code: 'B110', type: 'LectureHall', capacity: 80, allowedSessions: ['L'] },
        { code: 'B111', type: 'LectureHall', capacity: 80, allowedSessions: ['L'] },
        // Computer Labs (capacity 28, Practical-only 'P')
        { code: 'B005', type: 'Lab',         capacity: 28, allowedSessions: ['P'] },
        { code: 'B009', type: 'Lab',         capacity: 28, allowedSessions: ['P'] },
        { code: 'B010', type: 'Lab',         capacity: 28, allowedSessions: ['P'] },
        { code: 'B011', type: 'Lab',         capacity: 28, allowedSessions: ['P'] },
        // Cisco Specialized Lab (capacity 32, priority Networking/IoT)
        { code: 'B006', type: 'CiscoLab',    capacity: 32, allowedSessions: ['P'] },
    ],

    // ─────────────────────────────────────────────────────────────────────
// §2.5a  courses — all 42 modules printed for the 14 in-scope cohorts
    // (semester-202505 Programme PDF). `name` comes from
    // reference/past sem pdf/Schedule ds/py script/course_titles.py; an unknown
    // title prints as 'Subj <code>'. `type` = the most permissive session the
    // module runs (L > T > P) and drives replacement-arrangement's venue
    // filter; `cohorts` uses the registry's display codes and both counts come
    // from the MockData.cohorts registry.
    // ─────────────────────────────────────────────────────────────────────
    courses: [
        { code: 'AMCS1013', name: 'Problem Solving and Programming', type: 'L', cohorts: ['DFT1(S1)', 'DSF1(S1)'], cohortCounts: [30, 24], studentCount: 54 },
        { code: 'AMCS1043', name: 'Database Development and Applications', type: 'L', cohorts: ['DFT1(S1)'], cohortCounts: [30], studentCount: 30 },
        { code: 'AMCS2093', name: 'Operating Systems', type: 'L', cohorts: ['DFT2(S1)', 'DSF2(S1)'], cohortCounts: [28, 22], studentCount: 50 },
        { code: 'AMIS1003', name: 'Introduction to Cybersecurity', type: 'L', cohorts: ['DFT1(S1)', 'DSF1(S1)'], cohortCounts: [30, 24], studentCount: 54 },
        { code: 'AMIS1012', name: 'Ethics in Computing', type: 'L', cohorts: ['DFT2(S1)', 'DSF2(S1)'], cohortCounts: [28, 22], studentCount: 50 },
        { code: 'AMIT2014', name: 'Web and Mobile Systems', type: 'L', cohorts: ['DFT2(S1)'], cohortCounts: [28], studentCount: 28 },
        { code: 'AMIT2033', name: 'Networking Essentials', type: 'L', cohorts: ['DFT2(S1)'], cohortCounts: [28], studentCount: 28 },
        { code: 'AMIT2034', name: 'Fundamentals of Computer Networks', type: 'L', cohorts: ['DFT2(S1)', 'DSF2(S1)'], cohortCounts: [28, 22], studentCount: 50 },
        { code: 'AMMS1623', name: 'Calculus and Algebra', type: 'T', cohorts: ['DSF1(S1)'], cohortCounts: [24], studentCount: 24 },
        { code: 'AMMS3653', name: 'Discrete Mathematics', type: 'L', cohorts: ['DSF2(S1)'], cohortCounts: [22], studentCount: 22 },
        { code: 'AMSE1003', name: 'Software Engineering', type: 'L', cohorts: ['DSF1(S1)'], cohortCounts: [24], studentCount: 24 },
        { code: 'AMSE2002', name: 'Subj AMSE2002', type: 'L', cohorts: ['DSF2(S1)'], cohortCounts: [22], studentCount: 22 },
        { code: 'AMSE2003', name: 'Subj AMSE2003', type: 'L', cohorts: ['DSF2(S1)'], cohortCounts: [22], studentCount: 22 },
        { code: 'AMSE2013', name: 'Subj AMSE2013', type: 'L', cohorts: ['DSF2(S1)'], cohortCounts: [22], studentCount: 22 },
        { code: 'BBBE1033', name: 'Economics', type: 'L', cohorts: ['RBU1(S1)G1'], cohortCounts: [20], studentCount: 20 },
        { code: 'BMCS1013', name: 'Problem Solving and Programming', type: 'L', cohorts: ['RSD1(S1)G1'], cohortCounts: [18], studentCount: 18 },
        { code: 'BMCS1053', name: 'Database Management', type: 'P', cohorts: ['RSD1(S1)G1'], cohortCounts: [18], studentCount: 18 },
        { code: 'BMCS1113', name: 'Computer Organisation and Architecture', type: 'L', cohorts: ['RSD1(S1)G1'], cohortCounts: [18], studentCount: 18 },
        { code: 'BMCS2053', name: 'Object-Oriented Analysis and Design', type: 'L', cohorts: ['RSD2(S1)G1', 'RSD2(S1)G2', 'RSD2(S1)G3'], cohortCounts: [16, 16, 15], studentCount: 47 },
        { code: 'BMCS2063', name: 'Data Structures and Algorithms', type: 'L', cohorts: ['RSD2(S1)G1', 'RSD2(S1)G2', 'RSD2(S1)G3'], cohortCounts: [16, 16, 15], studentCount: 47 },
        { code: 'BMCS3033', name: 'Social and Professional Issues', type: 'L', cohorts: ['RSD3(S1)G1'], cohortCounts: [14], studentCount: 14 },
        { code: 'BMIS2003', name: 'Blockchain Application Development', type: 'L', cohorts: ['RSD3(S1)G1'], cohortCounts: [14], studentCount: 14 },
        { code: 'BMIS2113', name: 'Information Technology Infrastructure', type: 'L', cohorts: ['RSD2(S1)G1', 'RSD3(S1)G2', 'RSD3(S1)G3'], cohortCounts: [16, 14, 13], studentCount: 43 },
        { code: 'BMIT1173', name: 'IT Fundamentals', type: 'L', cohorts: ['RSD1(S1)G1', 'RSD2(S1)G3'], cohortCounts: [18, 15], studentCount: 33 },
        { code: 'BMIT1723', name: 'IT Fundamentals and Applications', type: 'L', cohorts: ['RBU1(S1)G1'], cohortCounts: [20], studentCount: 20 },
        { code: 'BMIT2013', name: 'Web-Based Integrated Systems', type: 'L', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], cohortCounts: [16, 15], studentCount: 31 },
        { code: 'BMIT2043', name: 'Introduction to Internet Security', type: 'L', cohorts: ['RSD2(S1)G1'], cohortCounts: [16], studentCount: 16 },
        { code: 'BMIT2073', name: 'Mobile Application Development', type: 'L', cohorts: ['RSD3(S1)G2', 'RSD3(S1)G3'], cohortCounts: [14, 13], studentCount: 27 },
        { code: 'BMIT2154', name: 'Switching and Routing Technologies', type: 'L', cohorts: ['RSD2(S1)G1'], cohortCounts: [16], studentCount: 16 },
        { code: 'BMIT2203', name: 'Human Computer Interaction', type: 'L', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], cohortCounts: [16, 15], studentCount: 31 },
        { code: 'BMIT3084', name: 'Enterprise Networking', type: 'L', cohorts: ['RSD3(S1)G2'], cohortCounts: [14], studentCount: 14 },
        { code: 'BMIT3173', name: 'Integrative Programming', type: 'L', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G3'], cohortCounts: [14, 13], studentCount: 27 },
        { code: 'BMIT3273', name: 'Subj BMIT3273', type: 'L', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G2', 'RSD3(S1)G3'], cohortCounts: [14, 14, 13], studentCount: 41 },
        { code: 'BMMS1743', name: 'Quantitative Methods', type: 'L', cohorts: ['RBU1(S1)G1'], cohortCounts: [20], studentCount: 20 },
        { code: 'BMSE2163', name: 'Software Engineering', type: 'L', cohorts: ['RSD2(S1)G1', 'RSD2(S1)G2'], cohortCounts: [16, 16], studentCount: 32 },
        { code: 'BMSE3153', name: 'Software Project Management', type: 'L', cohorts: ['RSD3(S1)G2', 'RSD3(S1)G3'], cohortCounts: [14, 13], studentCount: 27 },
        { code: 'MPU-2212', name: 'Subj MPU-2212', type: 'T', cohorts: ['DFT2(S1)'], cohortCounts: [28], studentCount: 28 },
        { code: 'MPU-2302', name: 'Subj MPU-2302', type: 'T', cohorts: ['DFT1(S1)'], cohortCounts: [30], studentCount: 30 },
        { code: 'MPU-3103', name: 'Subj MPU-3103', type: 'T', cohorts: ['RSD1(S1)G1', 'RBU1(S1)G1'], cohortCounts: [18, 20], studentCount: 38 },
        { code: 'MPU-3133', name: 'Falsafah dan Isu Semasa', type: 'T', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3', 'RAF2(S3)G2', 'RAF2(S3)G4', 'RBU1(S1)G1'], cohortCounts: [16, 15, 12, 10, 20], studentCount: 73 },
        { code: 'MPU-3232', name: 'Entrepreneurship', type: 'L', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3', 'RSD3(S1)G3'], cohortCounts: [16, 15, 13], studentCount: 44 },
        { code: 'MPU-3302', name: 'Subj MPU-3302', type: 'T', cohorts: ['RAF2(S3)G2', 'RAF2(S3)G4'], cohortCounts: [12, 10], studentCount: 22 },
    ],
    // ─────────────────────────────────────────────────────────────────────
// §2.6  myTimetable — En. Lim Jia Zheng's own week (semester-202505 lecturer
    // PDF p.41, 7 blocks / 10.0 h), written out for all 14 weeks so that
    // replacement-arrangement's checkConflict() sees the real classes too.
    // Week 11 is the all-normal seed; weeks 0, 1, 2, 4, 7, 9 carry the demo
    // replacement / pending / conflict states; week 3 is empty to exercise
    // the empty state. The page MUST copy before mutating (MockData is
    // read-only) — .slice() suffices, no individual field is ever rewritten.
    // SDD cancel-class-enhancement: events may carry `status: 'cancelled'` at
    // runtime (+ cancelledReason/cancelledDetail) via the ClassCancellation
    // ledger (sessionStorage key `classCancellationLedger`); cancelled events
    // are filtered from grids/summaries before render.
    // ─────────────────────────────────────────────────────────────────────
    myTimetable: {
        seedWeek: 11,
        eventsByWeek: {
            0: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'conflict', name: 'Web-Based Integrated Systems', remarks: 'Lab equipment failure' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'conflict', name: 'Web-Based Integrated Systems', remarks: 'Lab equipment failure' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            1: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'replacement', name: 'Web-Based Integrated Systems', remarks: '', replacedFor: '22-Sep-2026', replacedReason: 'Lab equipment failure' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'pending', name: 'Operating Systems', remarks: '', requestedAt: '28 Sep 2026, 11:00 AM', requestedBy: 'En. Lim Jia Zheng', requestId: 3 },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'conflict', name: 'Operating Systems', remarks: 'Lecturer on leave' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            2: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'replacement', name: 'Web-Based Integrated Systems', remarks: '', replacedFor: '23-Sep-2026', replacedReason: 'Lab equipment failure' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'pending', name: 'Operating Systems', remarks: '', requestedAt: '02 Oct 2026, 02:30 PM', requestedBy: 'En. Lim Jia Zheng', requestId: 8 },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            3: [],   // empty week — "No classes this week"
            4: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'conflict', name: 'Operating Systems', remarks: 'Lecturer on leave' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'replacement', name: 'Operating Systems', remarks: '', replacedFor: '01-Oct-2026', replacedReason: 'Lecturer on leave' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            5: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            6: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            7: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'conflict', name: 'Operating Systems', remarks: 'Lab equipment failure' },
            ],
            8: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            9: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'pending', name: 'Operating Systems', remarks: '', requestedAt: '01 Oct 2026, 09:15 AM', requestedBy: 'En. Lim Jia Zheng', requestId: 11 },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'replacement', name: 'Operating Systems', remarks: '', replacedFor: '13-Nov-2026', replacedReason: 'Lab equipment failure' },
            ],
            10: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            11: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            12: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ],
            13: [
                { di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1) + DSF2(S1)', cohorts: ['DFT2(S1)', 'DSF2(S1)'], studentCounts: [28, 22], status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2 + RSD2(S1)G3', cohorts: ['RSD2(S1)G2', 'RSD2(S1)G3'], studentCounts: [16, 15], status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', studentCount: 22, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
                { di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', studentCount: 28, status: 'normal', name: 'Operating Systems', remarks: '' },
            ]
        },
    },
    // ─────────────────────────────────────────────────────────────────────
// §2.7  cohortTimetable — the 14 in-scope cohorts, seeded from the
    // semester-202505 Programme PDF (155 blocks per week, 282 cohort-hours).
    //
    // `events` is a COMPUTED array: the weekly template (`base`, one entry per
    // block) expanded over all 14 weeks, so every consumer keeps reading a flat
    // { cohortId, week, event } list (CohortTimetable, venue-timetable, the
    // arrangement page's slot picker) without carrying 2,576 literal rows.
    // The PDFs carry no status — `flags` overlays the demo states, keyed by
    // cohortId|di|start|code because one code can hold L/T/P blocks.
    // RSD3 G2 is also readable as `rsd3g2Base` + `rsd3g2Flags` (the student
    // pages rebuild their 14 weeks from those); the CohortTimetable page
    // overwrites that cohort's weeks from them, so both views stay in step.
    // Holiday styling comes from MockData.holidays, not from here.
    // SDD cancel-class-enhancement: computed events may carry
    // `status: 'cancelled'` at runtime (+ cancelledReason/cancelledDetail) via
    // the ClassCancellation ledger (sessionStorage key
    // `classCancellationLedger`); cancelled events are filtered from
    // grids/summaries before render.
    // ─────────────────────────────────────────────────────────────────────
    cohortTimetable: {
        faculties: [
            {
                id: 'focs',
                name: 'Faculty of Computing and Information Technology (FOCS)',
                cohorts: [
                    { id: 'dft1s1', name: 'DFT1 (S1) — Diploma in Information Technology' },
                    { id: 'dft2s1', name: 'DFT2 (S1) — Diploma in Information Technology' },
                    { id: 'dsf1s1', name: 'DSF1 (S1) — Diploma in Software Engineering' },
                    { id: 'dsf2s1', name: 'DSF2 (S1) — Diploma in Software Engineering' },
                    { id: 'rsd1s1g1', name: 'RSD1 (S1) G1 — Bachelor in IT (Hons) Software Systems Development' },
                    { id: 'rsd2s1g1', name: 'RSD2 (S1) G1 — Bachelor in IT (Hons) Software Systems Development' },
                    { id: 'rsd2s1g2', name: 'RSD2 (S1) G2 — Bachelor in IT (Hons) Software Systems Development' },
                    { id: 'rsd2s1g3', name: 'RSD2 (S1) G3 — Bachelor in IT (Hons) Software Systems Development' },
                    { id: 'rsd3s1g1', name: 'RSD3 (S1) G1 — Bachelor in IT (Hons) Software Systems Development' },
                    { id: 'rsd3s1g2', name: 'RSD3 (S1) G2 — Bachelor in IT (Hons) Software Systems Development' },
                    { id: 'rsd3s1g3', name: 'RSD3 (S1) G3 — Bachelor in IT (Hons) Software Systems Development' }
                ],
            },
            {
                id: 'fafb',
                name: 'Faculty of Accountancy, Finance and Business (FAFB)',
                cohorts: [
                    { id: 'raf2s3g2', name: 'RAF2 (S3) G2 — Bachelor in Accountancy' },
                    { id: 'raf2s3g4', name: 'RAF2 (S3) G4 — Bachelor in Accountancy' },
                    { id: 'rbu1s1g1', name: 'RBU1 (S1) G1 — Bachelor in Business Administration' }
                ],
            }
        ],

        events: (function() {
            var base = [
                { cohortId: 'dft1s1', di: 0, start: 4, end: 5, code: 'MPU-2302', type: 'T', venue: 'B101', lecturer: 'En. Muada Bin Ojih', cohort: 'DFT1(S1)', status: 'normal', name: 'Subj MPU-2302', remarks: '' },
                { cohortId: 'dft1s1', di: 0, start: 6, end: 9, code: 'AMCS1013', type: 'L', venue: 'B111', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'DFT1(S1)', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'dft1s1', di: 0, start: 12, end: 13, code: 'AMCS1013', type: 'T', venue: 'B016', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'DFT1(S1)', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'dft1s1', di: 1, start: 2, end: 3, code: 'AMCS1013', type: 'P', venue: 'B009', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'DFT1(S1)', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'dft1s1', di: 1, start: 4, end: 7, code: 'AMIS1003', type: 'L', venue: 'B111', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'DFT1(S1)', status: 'normal', name: 'Introduction to Cybersecurity', remarks: '' },
                { cohortId: 'dft1s1', di: 1, start: 8, end: 9, code: 'AMIS1003', type: 'T', venue: 'B018', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'DFT1(S1)', status: 'normal', name: 'Introduction to Cybersecurity', remarks: '' },
                { cohortId: 'dft1s1', di: 1, start: 12, end: 15, code: 'AMCS1043', type: 'L', venue: 'B014', lecturer: 'Pn. Teng Nga Sing', cohort: 'DFT1(S1)', status: 'normal', name: 'Database Development and Applications', remarks: '' },
                { cohortId: 'dft1s1', di: 2, start: 4, end: 5, code: 'AMCS1043', type: 'T', venue: 'B011', lecturer: 'Pn. Teng Nga Sing', cohort: 'DFT1(S1)', status: 'normal', name: 'Database Development and Applications', remarks: '' },
                { cohortId: 'dft1s1', di: 2, start: 6, end: 7, code: 'AMCS1043', type: 'P', venue: 'B011', lecturer: 'Pn. Teng Nga Sing', cohort: 'DFT1(S1)', status: 'normal', name: 'Database Development and Applications', remarks: '' },
                { cohortId: 'dft2s1', di: 0, start: 2, end: 5, code: 'AMIT2014', type: 'L', venue: 'B016', lecturer: 'Cik Ellis Chieng', cohort: 'DFT2(S1)', status: 'normal', name: 'Web and Mobile Systems', remarks: '' },
                { cohortId: 'dft2s1', di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', status: 'normal', name: 'Operating Systems', remarks: '' },
                { cohortId: 'dft2s1', di: 1, start: 2, end: 5, code: 'AMIS1012', type: 'L', venue: 'B110', lecturer: 'Cik Ellis Chieng', cohort: 'DFT2(S1)', status: 'normal', name: 'Ethics in Computing', remarks: '' },
                { cohortId: 'dft2s1', di: 1, start: 8, end: 9, code: 'AMIS1012', type: 'T', venue: 'B108', lecturer: 'Cik Ellis Chieng', cohort: 'DFT2(S1)', status: 'normal', name: 'Ethics in Computing', remarks: '' },
                { cohortId: 'dft2s1', di: 1, start: 11, end: 14, code: 'AMIT2034', type: 'L', venue: 'B010', lecturer: 'En. Daniel Royd Michael', cohort: 'DFT2(S1)', status: 'normal', name: 'Fundamentals of Computer Networks', remarks: '' },
                { cohortId: 'dft2s1', di: 1, start: 15, end: 16, code: 'AMIT2034', type: 'T', venue: 'B010', lecturer: 'En. Daniel Royd Michael', cohort: 'DFT2(S1)', status: 'normal', name: 'Fundamentals of Computer Networks', remarks: '' },
                { cohortId: 'dft2s1', di: 2, start: 4, end: 7, code: 'AMIT2014', type: 'P', venue: 'B009', lecturer: 'Cik Ellis Chieng', cohort: 'DFT2(S1)', status: 'normal', name: 'Web and Mobile Systems', remarks: '' },
                { cohortId: 'dft2s1', di: 2, start: 6, end: 9, code: 'AMIT2034', type: 'P', venue: 'B005', lecturer: 'En. Daniel Royd Michael', cohort: 'DFT2(S1)', status: 'normal', name: 'Fundamentals of Computer Networks', remarks: '' },
                { cohortId: 'dft2s1', di: 2, start: 12, end: 15, code: 'AMIT2033', type: 'L', venue: 'B006', lecturer: 'En. Daniel Royd Michael', cohort: 'DFT2(S1)', status: 'normal', name: 'Networking Essentials', remarks: '' },
                { cohortId: 'dft2s1', di: 2, start: 16, end: 17, code: 'AMIT2033', type: 'T', venue: 'B006', lecturer: 'En. Daniel Royd Michael', cohort: 'DFT2(S1)', status: 'normal', name: 'Networking Essentials', remarks: '' },
                { cohortId: 'dft2s1', di: 3, start: 11, end: 12, code: 'MPU-2212', type: 'T', venue: 'B103', lecturer: 'En. Muada Bin Ojih', cohort: 'DFT2(S1)', status: 'normal', name: 'Subj MPU-2212', remarks: '' },
                { cohortId: 'dft2s1', di: 3, start: 13, end: 14, code: 'AMCS2093', type: 'T', venue: 'B107', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', status: 'normal', name: 'Operating Systems', remarks: '' },
                { cohortId: 'dft2s1', di: 4, start: 6, end: 7, code: 'AMCS2093', type: 'P', venue: 'B011', lecturer: 'En. Lim Jia Zheng', cohort: 'DFT2(S1)', status: 'normal', name: 'Operating Systems', remarks: '' },
                { cohortId: 'dft2s1', di: 4, start: 10, end: 11, code: 'AMIT2033', type: 'P', venue: 'B006', lecturer: 'En. Daniel Royd Michael', cohort: 'DFT2(S1)', status: 'normal', name: 'Networking Essentials', remarks: '' },
                { cohortId: 'dsf1s1', di: 0, start: 2, end: 5, code: 'AMSE1003', type: 'L', venue: 'B111', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'DSF1(S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'dsf1s1', di: 0, start: 6, end: 9, code: 'AMCS1013', type: 'L', venue: 'B111', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'DSF1(S1)', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'dsf1s1', di: 1, start: 4, end: 7, code: 'AMIS1003', type: 'L', venue: 'B111', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'DSF1(S1)', status: 'normal', name: 'Introduction to Cybersecurity', remarks: '' },
                { cohortId: 'dsf1s1', di: 1, start: 11, end: 12, code: 'AMCS1013', type: 'T', venue: 'B006', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'DSF1(S1)', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'dsf1s1', di: 1, start: 13, end: 14, code: 'AMCS1013', type: 'P', venue: 'B006', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'DSF1(S1)', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'dsf1s1', di: 2, start: 2, end: 3, code: 'AMSE1003', type: 'T', venue: 'B009', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'DSF1(S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'dsf1s1', di: 2, start: 6, end: 7, code: 'AMSE1003', type: 'P', venue: 'B010', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'DSF1(S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'dsf1s1', di: 3, start: 11, end: 13, code: 'AMMS1623', type: 'T', venue: 'B106', lecturer: 'Pn. Patricia G Kissol', cohort: 'DSF1(S1)', status: 'normal', name: 'Calculus and Algebra', remarks: '' },
                { cohortId: 'dsf1s1', di: 4, start: 15, end: 16, code: 'AMIS1003', type: 'T', venue: 'B010', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'DSF1(S1)', status: 'normal', name: 'Introduction to Cybersecurity', remarks: '' },
                { cohortId: 'dsf2s1', di: 0, start: 2, end: 5, code: 'AMSE2002', type: 'L', venue: 'B103', lecturer: 'En. Daniel Royd Michael', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2002', remarks: '' },
                { cohortId: 'dsf2s1', di: 0, start: 6, end: 9, code: 'AMCS2093', type: 'L', venue: 'B110', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', status: 'normal', name: 'Operating Systems', remarks: '' },
                { cohortId: 'dsf2s1', di: 1, start: 2, end: 5, code: 'AMIS1012', type: 'L', venue: 'B110', lecturer: 'Cik Ellis Chieng', cohort: 'DSF2(S1)', status: 'normal', name: 'Ethics in Computing', remarks: '' },
                { cohortId: 'dsf2s1', di: 1, start: 6, end: 7, code: 'AMIS1012', type: 'T', venue: 'B108', lecturer: 'Cik Ellis Chieng', cohort: 'DSF2(S1)', status: 'normal', name: 'Ethics in Computing', remarks: '' },
                { cohortId: 'dsf2s1', di: 1, start: 11, end: 14, code: 'AMIT2034', type: 'L', venue: 'B010', lecturer: 'En. Daniel Royd Michael', cohort: 'DSF2(S1)', status: 'normal', name: 'Fundamentals of Computer Networks', remarks: '' },
                { cohortId: 'dsf2s1', di: 1, start: 15, end: 16, code: 'AMIT2034', type: 'T', venue: 'B010', lecturer: 'En. Daniel Royd Michael', cohort: 'DSF2(S1)', status: 'normal', name: 'Fundamentals of Computer Networks', remarks: '' },
                { cohortId: 'dsf2s1', di: 2, start: 2, end: 5, code: 'AMSE2013', type: 'L', venue: 'B015', lecturer: 'En. Jefther Edward', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2013', remarks: '' },
                { cohortId: 'dsf2s1', di: 2, start: 6, end: 9, code: 'AMIT2034', type: 'P', venue: 'B005', lecturer: 'En. Daniel Royd Michael', cohort: 'DSF2(S1)', status: 'normal', name: 'Fundamentals of Computer Networks', remarks: '' },
                { cohortId: 'dsf2s1', di: 2, start: 12, end: 13, code: 'AMCS2093', type: 'T', venue: 'B106', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', status: 'normal', name: 'Operating Systems', remarks: '' },
                { cohortId: 'dsf2s1', di: 2, start: 14, end: 15, code: 'AMSE2013', type: 'T', venue: 'B106', lecturer: 'En. Jefther Edward', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2013', remarks: '' },
                { cohortId: 'dsf2s1', di: 3, start: 2, end: 5, code: 'AMSE2003', type: 'L', venue: 'B100', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2003', remarks: '' },
                { cohortId: 'dsf2s1', di: 3, start: 6, end: 7, code: 'AMSE2003', type: 'T', venue: 'B100', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2003', remarks: '' },
                { cohortId: 'dsf2s1', di: 3, start: 11, end: 12, code: 'AMCS2093', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2(S1)', status: 'normal', name: 'Operating Systems', remarks: '' },
                { cohortId: 'dsf2s1', di: 3, start: 13, end: 14, code: 'AMSE2002', type: 'P', venue: 'B011', lecturer: 'En. Jefther Edward', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2002', remarks: '' },
                { cohortId: 'dsf2s1', di: 3, start: 15, end: 16, code: 'AMSE2013', type: 'P', venue: 'B011', lecturer: 'En. Jefther Edward', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2013', remarks: '' },
                { cohortId: 'dsf2s1', di: 4, start: 2, end: 3, code: 'AMSE2003', type: 'P', venue: 'B010', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'DSF2(S1)', status: 'normal', name: 'Subj AMSE2003', remarks: '' },
                { cohortId: 'dsf2s1', di: 4, start: 4, end: 7, code: 'AMMS3653', type: 'L', venue: 'B100', lecturer: 'Pn. Patricia G Kissol', cohort: 'DSF2(S1)', status: 'normal', name: 'Discrete Mathematics', remarks: '' },
                { cohortId: 'dsf2s1', di: 4, start: 14, end: 16, code: 'AMMS3653', type: 'T', venue: 'B016', lecturer: 'Pn. Patricia G Kissol', cohort: 'DSF2(S1)', status: 'normal', name: 'Discrete Mathematics', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 0, start: 2, end: 5, code: 'BMIT1173', type: 'L', venue: 'B107', lecturer: 'En. Jefther Edward', cohort: 'RSD1(S1)G1', status: 'normal', name: 'IT Fundamentals', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 0, start: 6, end: 7, code: 'MPU-3103', type: 'T', venue: 'B017', lecturer: 'En. Muada Bin Ojih', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Subj MPU-3103', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 2, start: 2, end: 5, code: 'BMCS1013', type: 'L', venue: 'B105', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 2, start: 6, end: 7, code: 'BMCS1013', type: 'T', venue: 'B105', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 2, start: 10, end: 13, code: 'BMCS1113', type: 'L', venue: 'B102', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Computer Organisation and Architecture', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 2, start: 14, end: 15, code: 'BMCS1113', type: 'T', venue: 'B102', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Computer Organisation and Architecture', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 3, start: 2, end: 3, code: 'BMCS1113', type: 'P', venue: 'B005', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Computer Organisation and Architecture', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 3, start: 4, end: 5, code: 'BMCS1013', type: 'P', venue: 'B005', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Problem Solving and Programming', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 3, start: 8, end: 9, code: 'BMIT1173', type: 'T', venue: 'B009', lecturer: 'En. Jefther Edward', cohort: 'RSD1(S1)G1', status: 'normal', name: 'IT Fundamentals', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 3, start: 10, end: 11, code: 'BMIT1173', type: 'P', venue: 'B009', lecturer: 'En. Jefther Edward', cohort: 'RSD1(S1)G1', status: 'normal', name: 'IT Fundamentals', remarks: '' },
                { cohortId: 'rsd1s1g1', di: 3, start: 12, end: 13, code: 'BMCS1053', type: 'P', venue: 'B009', lecturer: 'Pn. Teng Nga Sing', cohort: 'RSD1(S1)G1', status: 'normal', name: 'Database Management', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 0, start: 2, end: 5, code: 'BMIT2154', type: 'L', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Switching and Routing Technologies', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 0, start: 6, end: 7, code: 'BMIT2154', type: 'T', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Switching and Routing Technologies', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 0, start: 10, end: 13, code: 'BMIT2043', type: 'L', venue: 'B106', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Introduction to Internet Security', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 1, start: 2, end: 5, code: 'BMCS2063', type: 'L', venue: 'B017', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Data Structures and Algorithms', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 1, start: 8, end: 11, code: 'BMCS2063', type: 'P', venue: 'B005', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Data Structures and Algorithms', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 1, start: 14, end: 15, code: 'BMIT2043', type: 'T', venue: 'B016', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Introduction to Internet Security', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 1, start: 16, end: 17, code: 'BMIT2043', type: 'P', venue: 'B009', lecturer: 'En. Mohd Nur Rahmat Bin Mohd Taat', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Introduction to Internet Security', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 2, start: 2, end: 5, code: 'BMIT2154', type: 'P', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Switching and Routing Technologies', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 2, start: 10, end: 13, code: 'BMSE2163', type: 'L', venue: 'B015', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 2, start: 14, end: 15, code: 'BMSE2163', type: 'T', venue: 'B015', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 3, start: 2, end: 5, code: 'BMIS2113', type: 'L', venue: 'B105', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 3, start: 6, end: 7, code: 'BMIS2113', type: 'T', venue: 'B105', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 3, start: 8, end: 9, code: 'BMSE2163', type: 'P', venue: 'B010', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 3, start: 10, end: 11, code: 'BMIS2113', type: 'P', venue: 'B005', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 4, start: 6, end: 9, code: 'BMCS2053', type: 'L', venue: 'B103', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 4, start: 12, end: 13, code: 'BMCS2053', type: 'T', venue: 'B103', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g1', di: 4, start: 14, end: 15, code: 'BMCS2053', type: 'P', venue: 'B009', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G1', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 0, start: 6, end: 9, code: 'BMIT2203', type: 'L', venue: 'B016', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Human Computer Interaction', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 0, start: 12, end: 13, code: 'MPU-3232', type: 'L', venue: 'B002', lecturer: 'Pn. Tan Sharon', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Entrepreneurship', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 1, start: 2, end: 5, code: 'BMCS2063', type: 'L', venue: 'B017', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Data Structures and Algorithms', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 1, start: 8, end: 11, code: 'BMCS2063', type: 'P', venue: 'B005', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Data Structures and Algorithms', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 2, start: 6, end: 7, code: 'BMIT2203', type: 'T', venue: 'B014', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Human Computer Interaction', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 2, start: 10, end: 13, code: 'BMSE2163', type: 'L', venue: 'B015', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 2, start: 14, end: 15, code: 'BMSE2163', type: 'T', venue: 'B015', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 3, start: 3, end: 4, code: 'BMIT2203', type: 'P', venue: 'B009', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Human Computer Interaction', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 3, start: 6, end: 7, code: 'MPU-3133', type: 'T', venue: 'B015', lecturer: 'En. Muada Bin Ojih', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Falsafah dan Isu Semasa', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 3, start: 8, end: 9, code: 'BMSE2163', type: 'P', venue: 'B010', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Software Engineering', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 4, start: 4, end: 5, code: 'MPU-3232', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Entrepreneurship', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 4, start: 6, end: 9, code: 'BMCS2053', type: 'L', venue: 'B103', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 4, start: 12, end: 13, code: 'BMCS2053', type: 'T', venue: 'B103', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g2', di: 4, start: 14, end: 15, code: 'BMCS2053', type: 'P', venue: 'B009', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G2', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 0, start: 2, end: 5, code: 'BMIT1173', type: 'L', venue: 'B107', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G3', status: 'normal', name: 'IT Fundamentals', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 0, start: 6, end: 9, code: 'BMIT2203', type: 'L', venue: 'B016', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Human Computer Interaction', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 0, start: 12, end: 13, code: 'MPU-3232', type: 'L', venue: 'B002', lecturer: 'Pn. Tan Sharon', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Entrepreneurship', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 1, start: 2, end: 5, code: 'BMCS2063', type: 'L', venue: 'B017', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Data Structures and Algorithms', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 1, start: 8, end: 11, code: 'BMCS2063', type: 'P', venue: 'B005', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Data Structures and Algorithms', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 1, start: 12, end: 15, code: 'BMIT2013', type: 'L', venue: 'B009', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 2, start: 2, end: 5, code: 'BMIT2013', type: 'P', venue: 'B010', lecturer: 'En. Lim Jia Zheng', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Web-Based Integrated Systems', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 2, start: 6, end: 7, code: 'BMIT2203', type: 'T', venue: 'B014', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Human Computer Interaction', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 3, start: 3, end: 4, code: 'BMIT2203', type: 'P', venue: 'B009', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Human Computer Interaction', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 3, start: 6, end: 7, code: 'MPU-3133', type: 'T', venue: 'B015', lecturer: 'En. Muada Bin Ojih', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Falsafah dan Isu Semasa', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 3, start: 8, end: 9, code: 'BMIT1173', type: 'T', venue: 'B009', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G3', status: 'normal', name: 'IT Fundamentals', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 3, start: 10, end: 11, code: 'BMIT1173', type: 'P', venue: 'B009', lecturer: 'En. Jefther Edward', cohort: 'RSD2(S1)G3', status: 'normal', name: 'IT Fundamentals', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 4, start: 4, end: 5, code: 'MPU-3232', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Entrepreneurship', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 4, start: 6, end: 9, code: 'BMCS2053', type: 'L', venue: 'B103', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 4, start: 12, end: 13, code: 'BMCS2053', type: 'T', venue: 'B103', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd2s1g3', di: 4, start: 14, end: 15, code: 'BMCS2053', type: 'P', venue: 'B009', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD2(S1)G3', status: 'normal', name: 'Object-Oriented Analysis and Design', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 0, start: 11, end: 14, code: 'BMCS3033', type: 'L', venue: 'B018', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Social and Professional Issues', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 1, start: 6, end: 9, code: 'BMIS2003', type: 'L', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Blockchain Application Development', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 1, start: 12, end: 15, code: 'BMIT3273', type: 'L', venue: 'B107', lecturer: 'En. Jefther Edward', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Subj BMIT3273', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 2, start: 2, end: 5, code: 'BMIT3173', type: 'L', venue: 'B014', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Integrative Programming', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 3, start: 2, end: 5, code: 'BMIS2003', type: 'P', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Blockchain Application Development', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 3, start: 12, end: 15, code: 'BMIT3173', type: 'P', venue: 'B006', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Integrative Programming', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 4, start: 3, end: 4, code: 'BMCS3033', type: 'T', venue: 'B011', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Social and Professional Issues', remarks: '' },
                { cohortId: 'rsd3s1g1', di: 4, start: 13, end: 16, code: 'BMIT3273', type: 'P', venue: 'B006', lecturer: 'En. Jefther Edward', cohort: 'RSD3(S1)G1', status: 'normal', name: 'Subj BMIT3273', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 0, start: 2, end: 5, code: 'BMSE3153', type: 'L', venue: 'B102', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Software Project Management', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 0, start: 10, end: 13, code: 'BMIT3084', type: 'L', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Enterprise Networking', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 1, start: 10, end: 11, code: 'BMSE3153', type: 'P', venue: 'B009', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Software Project Management', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 1, start: 12, end: 15, code: 'BMIT3273', type: 'L', venue: 'B107', lecturer: 'En. Jefther Edward', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Subj BMIT3273', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 2, start: 6, end: 9, code: 'BMIT2073', type: 'L', venue: 'B015', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Mobile Application Development', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 2, start: 12, end: 15, code: 'BMIT2073', type: 'P', venue: 'B010', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Mobile Application Development', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 3, start: 2, end: 5, code: 'BMIS2113', type: 'L', venue: 'B105', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 3, start: 6, end: 7, code: 'BMIS2113', type: 'T', venue: 'B105', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 3, start: 10, end: 11, code: 'BMIS2113', type: 'P', venue: 'B005', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 4, start: 4, end: 5, code: 'BMIT3084', type: 'T', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Enterprise Networking', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 4, start: 6, end: 9, code: 'BMIT3084', type: 'P', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Enterprise Networking', remarks: '' },
                { cohortId: 'rsd3s1g2', di: 4, start: 13, end: 16, code: 'BMIT3273', type: 'P', venue: 'B006', lecturer: 'En. Jefther Edward', cohort: 'RSD3(S1)G2', status: 'normal', name: 'Subj BMIT3273', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 0, start: 2, end: 5, code: 'BMSE3153', type: 'L', venue: 'B102', lecturer: 'Pn. Lee Yee Fong', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Software Project Management', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 0, start: 12, end: 13, code: 'MPU-3232', type: 'L', venue: 'B002', lecturer: 'Pn. Tan Sharon', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Entrepreneurship', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 1, start: 10, end: 11, code: 'BMSE3153', type: 'P', venue: 'B009', lecturer: 'Pn. Surayaini Binti Basri', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Software Project Management', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 1, start: 12, end: 15, code: 'BMIT3273', type: 'L', venue: 'B107', lecturer: 'En. Jefther Edward', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Subj BMIT3273', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 2, start: 2, end: 5, code: 'BMIT3173', type: 'L', venue: 'B014', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Integrative Programming', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 2, start: 6, end: 9, code: 'BMIT2073', type: 'L', venue: 'B015', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Mobile Application Development', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 2, start: 12, end: 15, code: 'BMIT2073', type: 'P', venue: 'B010', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Mobile Application Development', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 3, start: 2, end: 5, code: 'BMIS2113', type: 'L', venue: 'B105', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 3, start: 6, end: 7, code: 'BMIS2113', type: 'T', venue: 'B105', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 3, start: 10, end: 11, code: 'BMIS2113', type: 'P', venue: 'B005', lecturer: 'En. Daniel Royd Michael', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Information Technology Infrastructure', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 3, start: 12, end: 15, code: 'BMIT3173', type: 'P', venue: 'B006', lecturer: 'Ts. Norshikin Binti Zainal Abidin', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Integrative Programming', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 4, start: 4, end: 5, code: 'MPU-3232', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Entrepreneurship', remarks: '' },
                { cohortId: 'rsd3s1g3', di: 4, start: 13, end: 16, code: 'BMIT3273', type: 'P', venue: 'B006', lecturer: 'En. Jefther Edward', cohort: 'RSD3(S1)G3', status: 'normal', name: 'Subj BMIT3273', remarks: '' },
                { cohortId: 'raf2s3g2', di: 2, start: 6, end: 7, code: 'MPU-3302', type: 'T', venue: 'B106', lecturer: 'En. Muada Bin Ojih', cohort: 'RAF2(S3)G2', status: 'normal', name: 'Subj MPU-3302', remarks: '' },
                { cohortId: 'raf2s3g2', di: 3, start: 6, end: 7, code: 'MPU-3133', type: 'T', venue: 'B015', lecturer: 'En. Muada Bin Ojih', cohort: 'RAF2(S3)G2', status: 'normal', name: 'Falsafah dan Isu Semasa', remarks: '' },
                { cohortId: 'raf2s3g4', di: 2, start: 6, end: 7, code: 'MPU-3302', type: 'T', venue: 'B106', lecturer: 'En. Muada Bin Ojih', cohort: 'RAF2(S3)G4', status: 'normal', name: 'Subj MPU-3302', remarks: '' },
                { cohortId: 'raf2s3g4', di: 3, start: 6, end: 7, code: 'MPU-3133', type: 'T', venue: 'B015', lecturer: 'En. Muada Bin Ojih', cohort: 'RAF2(S3)G4', status: 'normal', name: 'Falsafah dan Isu Semasa', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 0, start: 6, end: 7, code: 'MPU-3103', type: 'T', venue: 'B017', lecturer: 'En. Muada Bin Ojih', cohort: 'RBU1(S1)G1', status: 'normal', name: 'Subj MPU-3103', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 0, start: 11, end: 14, code: 'BMMS1743', type: 'L', venue: 'B105', lecturer: 'Pn. Patricia G Kissol', cohort: 'RBU1(S1)G1', status: 'normal', name: 'Quantitative Methods', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 1, start: 11, end: 13, code: 'BMMS1743', type: 'T', venue: 'B105', lecturer: 'Pn. Patricia G Kissol', cohort: 'RBU1(S1)G1', status: 'normal', name: 'Quantitative Methods', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 2, start: 4, end: 7, code: 'BBBE1033', type: 'L', venue: 'B102', lecturer: 'Dr. Chang Foo Chung', cohort: 'RBU1(S1)G1', status: 'normal', name: 'Economics', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 2, start: 11, end: 12, code: 'BMIT1723', type: 'L', venue: 'B011', lecturer: 'Pn. Teng Nga Sing', cohort: 'RBU1(S1)G1', status: 'normal', name: 'IT Fundamentals and Applications', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 2, start: 13, end: 16, code: 'BMIT1723', type: 'P', venue: 'B011', lecturer: 'Pn. Teng Nga Sing', cohort: 'RBU1(S1)G1', status: 'normal', name: 'IT Fundamentals and Applications', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 3, start: 2, end: 4, code: 'BBBE1033', type: 'T', venue: 'B106', lecturer: 'Dr. Chang Foo Chung', cohort: 'RBU1(S1)G1', status: 'normal', name: 'Economics', remarks: '' },
                { cohortId: 'rbu1s1g1', di: 3, start: 6, end: 7, code: 'MPU-3133', type: 'T', venue: 'B015', lecturer: 'En. Muada Bin Ojih', cohort: 'RBU1(S1)G1', status: 'normal', name: 'Falsafah dan Isu Semasa', remarks: '' },
            ];
            var flags = {
                0:  [ [ 'dft1s1|0|4|MPU-2302', 'conflict', 'Lecturer on leave' ], [ 'rsd3s1g2|0|10|BMIT3084', 'conflict', 'Lab equipment failure' ] ],
                1:  [ [ 'dsf2s1|1|2|AMIS1012', 'pending', '', '29 Sep 2026, 10:00 AM', 1 ], [ 'rsd3s1g2|0|10|BMIT3084', 'replacement', '21-Sep-2026' ], [ 'rsd2s1g1|0|6|BMIT2154', 'conflict', 'Lab equipment failure' ] ],
                2:  [ [ 'rsd2s1g1|0|6|BMIT2154', 'replacement', '28-Sep-2026' ] ],
                3:  [ [ 'dft2s1|0|6|AMCS2093', 'conflict', 'Lecturer on leave' ] ],
                4:  [ [ 'rsd2s1g3|0|2|BMIT1173', 'conflict', 'Lecturer on leave' ], [ 'dsf1s1|1|11|AMCS1013', 'pending', '', '20 Oct 2026, 11:00 AM', 2 ], [ 'rsd3s1g2|2|6|BMIT2073', 'conflict', 'Lecturer on leave' ] ],
                5:  [ [ 'rsd3s1g2|2|6|BMIT2073', 'replacement', '21-Oct-2026' ] ],
                6:  [ [ 'raf2s3g2|3|6|MPU-3133', 'conflict', 'Lecturer on leave' ], [ 'rsd1s1g1|2|2|BMCS1013', 'conflict', 'Lecturer on leave' ] ],
                7:  [ [ 'dft2s1|1|2|AMIS1012', 'pending', '', '10 Nov 2026, 10:00 AM', 3 ], [ 'rsd1s1g1|2|2|BMCS1013', 'replacement', '04-Nov-2026' ] ],
                8:  [ [ 'rsd2s1g2|2|2|BMIT2013', 'conflict', 'Lab equipment failure' ], [ 'rsd3s1g2|2|6|BMIT2073', 'pending', '', '18 Nov 2026, 11:00 AM', 4 ] ],
                10:  [ [ 'rsd3s1g3|0|12|MPU-3232', 'pending', '', '30 Nov 2026, 10:00 AM', 5 ], [ 'dft1s1|1|4|AMIS1003', 'conflict', 'Lecturer on leave' ], [ 'rbu1s1g1|1|11|BMMS1743', 'conflict', 'Lecturer on leave' ] ],
                11:  [ [ 'rbu1s1g1|1|11|BMMS1743', 'replacement', '01-Dec-2026' ] ],
                12:  [ [ 'dsf1s1|0|6|AMCS1013', 'conflict', 'Lecturer on leave' ], [ 'dsf2s1|0|2|AMSE2002', 'conflict', 'Lecturer on leave' ] ],
                13:  [ [ 'rsd3s1g1|2|2|BMIT3173', 'pending', '', '23 Dec 2026, 10:00 AM', 6 ], [ 'dsf2s1|0|2|AMSE2002', 'replacement', '14-Dec-2026' ] ],
            };
            var out = [];
            for (var w = 0; w < 14; w++) {
                var byKey = {};
                (flags[w] || []).forEach(function(f) { byKey[f[0]] = f; });
                for (var i = 0; i < base.length; i++) {
                    var b = base[i];
                    var e = Object.assign({}, b);
                    var f = byKey[b.cohortId + '|' + b.di + '|' + b.start + '|' + b.code];
                    if (f) {
                        e.status = f[1];
                        e.remarks = f[2] || '';
                        if (f[3]) {
                            e.requestedAt = f[3];
                            e.requestedBy = e.lecturer;
                            e.requestId = f[4];
                        }
                    }
                    out.push({ cohortId: b.cohortId, week: w, event: e });
                }
            }
            return out;
        })(),

        // weekly template for RSD3 (S1) G2 — the pages add status/remarks from
        // rsd3g2Flags (student-my-timetable, replacement-history).
        rsd3g2Base: [
            { di: 0, start: 2, end: 5, code: 'BMSE3153', type: 'L', venue: 'B102', lecturer: 'Pn. Lee Yee Fong', name: 'Software Project Management' },
            { di: 0, start: 10, end: 13, code: 'BMIT3084', type: 'L', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', name: 'Enterprise Networking' },
            { di: 1, start: 10, end: 11, code: 'BMSE3153', type: 'P', venue: 'B009', lecturer: 'Pn. Surayaini Binti Basri', name: 'Software Project Management' },
            { di: 1, start: 12, end: 15, code: 'BMIT3273', type: 'L', venue: 'B107', lecturer: 'En. Jefther Edward', name: 'Subj BMIT3273' },
            { di: 2, start: 6, end: 9, code: 'BMIT2073', type: 'L', venue: 'B015', lecturer: 'Ts. Norshikin Binti Zainal Abidin', name: 'Mobile Application Development' },
            { di: 2, start: 12, end: 15, code: 'BMIT2073', type: 'P', venue: 'B010', lecturer: 'Ts. Norshikin Binti Zainal Abidin', name: 'Mobile Application Development' },
            { di: 3, start: 2, end: 5, code: 'BMIS2113', type: 'L', venue: 'B105', lecturer: 'En. Daniel Royd Michael', name: 'Information Technology Infrastructure' },
            { di: 3, start: 6, end: 7, code: 'BMIS2113', type: 'T', venue: 'B105', lecturer: 'En. Daniel Royd Michael', name: 'Information Technology Infrastructure' },
            { di: 3, start: 10, end: 11, code: 'BMIS2113', type: 'P', venue: 'B005', lecturer: 'En. Daniel Royd Michael', name: 'Information Technology Infrastructure' },
            { di: 4, start: 4, end: 5, code: 'BMIT3084', type: 'T', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', name: 'Enterprise Networking' },
            { di: 4, start: 6, end: 9, code: 'BMIT3084', type: 'P', venue: 'B006', lecturer: 'Dr. Christopher Lazarus', name: 'Enterprise Networking' },
            { di: 4, start: 13, end: 16, code: 'BMIT3273', type: 'P', venue: 'B006', lecturer: 'En. Jefther Edward', name: 'Subj BMIT3273' },
        ],
        rsd3g2Flags: {
            0:  [ [ 'BMIT3084', 'conflict', 'Lab equipment failure' ] ],
            1:  [ [ 'BMIT3084', 'replacement', '21-Sep-2026' ] ],
            4:  [ [ 'BMIT2073', 'conflict', 'Lecturer on leave' ] ],
            5:  [ [ 'BMIT2073', 'replacement', '21-Oct-2026' ] ],
            8:  [ [ 'BMIT2073', 'pending', '', '18 Nov 2026, 11:00 AM', 4 ] ],
        },
    },
    // ─────────────────────────────────────────────────────────────────────
// §2.8  studentTimetable — consumed by Student My Timetable page.
    // `activeCohort` is the cohort ID to render by default (matches
    // cohortTimetable faculties[].cohorts[].id).
    // `cancelledFlags` maps 0-indexed week keys to cancelled course codes taken
    // from the RSD3 G2 template: week 3 cancels ALL blocks (empty state),
    // weeks 4-5 cancel one each (partial).
    // ─────────────────────────────────────────────────────────────────────
    studentTimetable: {
        activeCohort: 'rsd3s1g2',
        cancelledFlags: {
            3: ['BMSE3153', 'BMIT3084', 'BMIT3273', 'BMIT2073', 'BMIS2113'],   // cancels every block — triggers the empty state
            4: ['BMIT3084'],   // partial cancellation
            5: ['BMIS2113'],
        },
    },
    // ─────────────────────────────────────────────────────────────────────
    // §2.9  requests — was my-request-history inline `mockRequests` (verbatim,
    // 20 entries, `id` first). my-request-history aliases this as a same-name
    // page-local const so its render code is unchanged. The sibling
    // request-approval page reads its OWN `approvalRequests` global (distinct
    // dataset) — see the compatibility globals at the bottom of this file.
    // ─────────────────────────────────────────────────────────────────────
    requests: [
        { id: 1,  requestedAt: _relDateTime(-34, 0, 59), courseCode: 'BMIT5555', courseName: 'Software Engineering',          classType: 'L', classDate: _relDate(-1), classDay: _dayName(-1), timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B104', totalStudents: 35, cohortCounts: [20, 15], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending',   rejectionReason: null, replacementDate: _relDate(1), replacementTime: '09:00 – 11:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 2,  requestedAt: _relDateTime(-40, 9, 42), courseCode: 'BMIT5555', courseName: 'Software Engineering',          classType: 'T', classDate: _relDate(2), classDay: _dayName(2), timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B105', totalStudents: 28, cohorts: ['DFT2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: _relDate(4), replacementTime: '14:00 – 16:00', replacementVenue: 'B110', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: _relDateTime(-39, 9, 0), remarks: null },
        { id: 3,  requestedAt: '2026-09-28T11:00:00', courseCode: 'AMCS2093', courseName: 'Operating Systems',           classType: 'P', classDate: '2026-10-01', classDay: 'Thursday',   timeStart: '13:30', timeEnd: '14:30', duration: 1, venue: 'B010', totalStudents: 22, cohorts: ['DSF2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: '2026-10-05', replacementTime: '13:30 – 14:30',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 4,  requestedAt: _relDateTime(-30, 23, 58), courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming',   classType: 'T', classDate: _relDate(7), classDay: _dayName(7), timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B106', totalStudents: 20, cohorts: ['DSF2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: _relDate(9), replacementTime: '11:00 – 13:00', replacementVenue: 'B201', reviewedBy: 'Dr. Lim (Dean)',    reviewedAt: _relDateTime(-29, 16, 30), remarks: null },
        { id: 5,  requestedAt: _relDateTime(-52, 8, 25), courseCode: 'BMIT5678', courseName: 'Database Systems',             classType: 'T', classDate: _relDate(8), classDay: _dayName(8), timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, cohortCounts: [15, 15], cohorts: ['DSF2 (S1)', 'DFT2 (S1)'], status: 'Pending',   rejectionReason: null, replacementDate: _relDate(10), replacementTime: '11:00 – 13:00', replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 6,  requestedAt: _relDateTime(-54, 21, 19), courseCode: 'BMIT9012', courseName: 'Computer Networks',            classType: 'L', classDate: _relDate(10), classDay: _dayName(10), timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, cohorts: ['DFT2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: _relDate(14), replacementTime: '08:00 – 10:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: _relDateTime(-53, 10, 0), remarks: null },
        { id: 7,  requestedAt: _relDateTime(-55, 20, 3), courseCode: 'BMIT3456', courseName: 'Artificial Intelligence',      classType: 'T', classDate: _relDate(11), classDay: _dayName(11), timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, cohorts: ['DSF2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Insufficient notice period. Requests must be submitted at least 5 working days in advance.', replacementDate: _relDate(14), replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: _relDateTime(-54, 8, 15), remarks: null },
        { id: 8,  requestedAt: '2026-10-02T14:30:00', courseCode: 'AMCS2093', courseName: 'Operating Systems',           classType: 'T', classDate: '2026-10-08', classDay: 'Thursday',   timeStart: '14:30', timeEnd: '15:30', duration: 1, venue: 'B107', totalStudents: 28, cohorts: ['DFT2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: '2026-10-12', replacementTime: '14:30 – 15:30',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 9,  requestedAt: _relDateTime(-30, 18, 3), courseCode: 'BMIT7890', courseName: 'Project Management',           classType: 'T', classDate: _relDate(15), classDay: _dayName(15), timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, cohorts: ['DFT2 (S1)'],                          status: 'Completed', rejectionReason: null, replacementDate: _relDate(17), replacementTime: '08:00 – 10:00', replacementVenue: 'B103', reviewedBy: 'Dr. Ahmad (HOD)',  reviewedAt: _relDateTime(-29, 14, 0), remarks: 'Replacement conducted successfully.' },
        { id: 10, requestedAt: _relDateTime(-41, 23, 24), courseCode: 'BMIT9999', courseName: 'Machine Learning',            classType: 'T', classDate: _relDate(16), classDay: _dayName(16), timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 15, cohorts: ['DSF2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: _relDate(18), replacementTime: '10:00 – 12:00', replacementVenue: 'B105', reviewedBy: 'Dr. Lim (Dean)',    reviewedAt: _relDateTime(-40, 11, 0), remarks: null },
        { id: 11, requestedAt: '2026-10-01T09:15:00', courseCode: 'AMCS2093', courseName: 'Operating Systems',              classType: 'T', classDate: '2026-11-25', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '15:00', duration: 1, venue: 'B106', totalStudents: 22, cohorts: ['DSF2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: '2026-11-30', replacementTime: '14:00 – 15:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 12, requestedAt: _relDateTime(-30, 22, 56), courseCode: 'BMIT4567', courseName: 'Web Development',              classType: 'L', classDate: _relDate(21), classDay: _dayName(21), timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, cohorts: ['DFT2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: _relDate(23), replacementTime: '09:00 – 11:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 13, requestedAt: _relDateTime(-23, 7, 27), courseCode: 'BMIT4567', courseName: 'Web Development',              classType: 'T', classDate: _relDate(22), classDay: _dayName(22), timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, cohortCounts: [12, 13], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: _relDate(24), replacementTime: '14:00 – 16:00', replacementVenue: 'B106', reviewedBy: 'Dr. Lim (Dean)',   reviewedAt: _relDateTime(-23, 15, 45), remarks: 'Replacement completed. Student attendance recorded.' },
        { id: 14, requestedAt: _relDateTime(-39, 11, 23), courseCode: 'BMIT8888', courseName: 'Cloud Computing',             classType: 'T', classDate: _relDate(23), classDay: _dayName(23), timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, cohorts: ['DSF2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: _relDate(25), replacementTime: '10:00 – 12:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: _relDateTime(-38, 13, 0), remarks: null },
        { id: 15, requestedAt: _relDateTime(-41, 5, 17), courseCode: 'BMIT7777', courseName: 'Cybersecurity',               classType: 'L', classDate: _relDate(-1), classDay: _dayName(-1), timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, cohortCounts: [10, 8],  cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Approved',  rejectionReason: null, replacementDate: _relDate(1), replacementTime: '08:00 – 10:00',  replacementVenue: 'B110', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: _relDateTime(-40, 9, 0), remarks: null },
        { id: 16, requestedAt: _relDateTime(-37, 15, 58), courseCode: 'BMIT7777', courseName: 'Cybersecurity',               classType: 'T', classDate: _relDate(2), classDay: _dayName(2), timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, cohorts: ['DFT2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: _relDate(4), replacementTime: '14:00 – 16:00', replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 17, requestedAt: _relDateTime(-43, 11, 34), courseCode: 'BMIT3344', courseName: 'Embedded Systems',            classType: 'T', classDate: _relDate(7), classDay: _dayName(7), timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B103', totalStudents: 12, cohorts: ['DSF2 (S1)'],                          status: 'Completed', rejectionReason: null, replacementDate: _relDate(10), replacementTime: '08:00 – 10:00', replacementVenue: 'B104', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: _relDateTime(-42, 8, 0), remarks: 'Replacement completed.' },
        { id: 18, requestedAt: _relDateTime(-44, 13, 1), courseCode: 'BMIT2222', courseName: 'Mobile Computing',            classType: 'L', classDate: _relDate(9), classDay: _dayName(9), timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 28, cohorts: ['DFT2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: _relDate(11), replacementTime: '09:00 – 11:00', replacementVenue: 'B201', reviewedBy: 'Dr. Lim (Dean)',    reviewedAt: _relDateTime(-43, 10, 30), remarks: null },
        { id: 19, requestedAt: _relDateTime(-44, 10, 42), courseCode: 'BMIT1111', courseName: 'Human-Computer Interaction',  classType: 'L', classDate: _relDate(15), classDay: _dayName(15), timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 22, cohorts: ['DSF2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Scheduling conflict with another lecturer\'s booking.', replacementDate: _relDate(17), replacementTime: '14:00 – 16:00', replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: _relDateTime(-43, 10, 30), remarks: null },
        { id: 20, requestedAt: _relDateTime(-41, 13, 9), courseCode: 'BMIT4433', courseName: 'Information Security',        classType: 'T', classDate: _relDate(17), classDay: _dayName(17), timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 18, cohorts: ['DFT2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Lecturer unavailable on the requested date.', replacementDate: _relDate(21), replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: _relDateTime(-40, 8, 0), remarks: null },
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.10 conflictedClasses — was replacement-home inline array (14 rows,
    // verbatim). Read-only — the page does not mutate it.
    // SDD cancel-class-enhancement: cancelled classes are APPENDED to this
    // array at runtime via the ClassCancellation ledger (sessionStorage key
    // `classCancellationLedger`), keeping the same row shape as the seeded
    // rows below (conflictReason = the mandatory cancellation reason).
    // ─────────────────────────────────────────────────────────────────────
    conflictedClasses: [
        { id: 1,  code: 'BMIT5555', name: 'Software Engineering',         type: 'L', date: '2026-09-04', day: 'Thursday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 35, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Public Holiday' },
        { id: 2,  code: 'BMIT5555', name: 'Software Engineering',         type: 'T', date: '2026-09-04', day: 'Thursday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B111', totalStudents: 28, cohorts: ['DFT2 (S1)'],              conflictReason: 'Public Holiday' },
        { id: 3,  code: 'BMIT6767', name: 'Object-Oriented Programming', type: 'L', date: '2026-09-10', day: 'Thursday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B103', totalStudents: 24, cohorts: ['DFT2 (S1)'],              conflictReason: 'Annual Leave' },
        { id: 4,  code: 'BMIT5678', name: 'Database Systems',            type: 'T', date: '2026-09-10', day: 'Thursday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, cohorts: ['DSF2 (S1)', 'DFT2 (S1)'], conflictReason: 'Medical Leave' },
        { id: 5,  code: 'BMIT9012', name: 'Computer Networks',           type: 'L', date: '2026-09-11', day: 'Friday',   timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, cohorts: ['DFT2 (S1)'],              conflictReason: 'Official Event' },
        { id: 6,  code: 'BMIT3456', name: 'Artificial Intelligence',     type: 'T', date: '2026-09-12', day: 'Saturday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, cohorts: ['DSF2 (S1)'],              conflictReason: 'Annual Leave' },
        { id: 7,  code: 'BMIT7890', name: 'Project Management',          type: 'L', date: '2026-09-15', day: 'Tuesday',  timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 20, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Medical Leave' },
        { id: 8,  code: 'BMIT9999', name: 'Machine Learning',            type: 'T', date: '2026-09-15', day: 'Tuesday',  timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, cohorts: ['DFT2 (S1)'],              conflictReason: 'Emergency Leave' },
        { id: 9,  code: 'BMIT1234', name: 'Data Structures',             type: 'L', date: '2026-09-18', day: 'Friday',   timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 30, cohorts: ['DSF2 (S1)'],              conflictReason: 'Public Holiday' },
        { id: 10, code: 'BMIT1234', name: 'Data Structures',             type: 'T', date: '2026-09-18', day: 'Friday',   timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Public Holiday' },
        { id: 11, code: 'BMIT4567', name: 'Web Development',             type: 'L', date: '2026-09-20', day: 'Sunday',   timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, cohorts: ['DFT2 (S1)'],              conflictReason: 'Official Event' },
        { id: 12, code: 'BMIT8888', name: 'Cloud Computing',            type: 'T', date: '2026-09-22', day: 'Tuesday',  timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, cohorts: ['DSF2 (S1)'],              conflictReason: 'Annual Leave' },
        { id: 13, code: 'BMIT7777', name: 'Cybersecurity',               type: 'L', date: '2026-09-25', day: 'Friday',   timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Medical Leave' },
        { id: 14, code: 'BMIT3333', name: 'Embedded Systems',           type: 'T', date: '2026-09-28', day: 'Monday',    timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, cohorts: ['DSF2 (S1)'],              conflictReason: 'Emergency Leave' },
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.10 arrangementWeeks — was replacement-arrangement inline `weekData`
    // (3 weeks, Week 11/10/9 DESCENDING). PAGE-SPECIFIC — NOT standardised
    // to `MockData.semester.startDate` (this page shows a fixed demo window).
    // Day-label quirks (e.g. '01 Sep' labelled 'Mon' which is actually a
    // Tuesday) are PRESERVED verbatim per the fidelity rule — fixing them is
    // out of scope. The 04-Sep holiday flag stays here (NOT in `holidays`).
    // ─────────────────────────────────────────────────────────────────────
    arrangementWeeks: [
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
            ],
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
            ],
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
            ],
        },
    ],

    // ─────────────────────────────────────────────────────────────────────
// §2.11 venueSlots — the 23 Block-B rooms, seeded from the classroom PDF:
    // [dayIndex, hourIndex, status] with 1 = occupied; a slot that is absent is
    // free. Two demo-only pairs (3 pending / 4 reserved, placed on slots that
    // are genuinely free in B103/B104) keep those two states reachable — the
    // PDFs record occupancy only, so they cannot be seeded.
    // Read-only — the arrangement page does not mutate it.
    // ─────────────────────────────────────────────────────────────────────
    venueSlots: {
        'B002': [
            [0, 12, 1], [0, 13, 1]
        ],
        'B014': [
            [1, 12, 1], [1, 13, 1], [1, 14, 1], [1, 15, 1],
            [2, 2, 1], [2, 3, 1], [2, 4, 1], [2, 5, 1],
            [2, 6, 1], [2, 7, 1]
        ],
        'B015': [
            [2, 2, 1], [2, 3, 1], [2, 4, 1], [2, 5, 1],
            [2, 6, 1], [2, 7, 1], [2, 8, 1], [2, 9, 1],
            [2, 10, 1], [2, 11, 1], [2, 12, 1], [2, 13, 1],
            [2, 14, 1], [2, 15, 1], [3, 6, 1], [3, 7, 1]
        ],
        'B016': [
            [0, 2, 1], [0, 3, 1], [0, 4, 1], [0, 5, 1],
            [0, 6, 1], [0, 7, 1], [0, 8, 1], [0, 9, 1],
            [0, 12, 1], [0, 13, 1], [1, 14, 1], [1, 15, 1],
            [4, 14, 1], [4, 15, 1], [4, 16, 1]
        ],
        'B017': [
            [0, 6, 1], [0, 7, 1], [1, 2, 1], [1, 3, 1],
            [1, 4, 1], [1, 5, 1]
        ],
        'B018': [
            [0, 11, 1], [0, 12, 1], [0, 13, 1], [0, 14, 1],
            [1, 8, 1], [1, 9, 1]
        ],
        'B100': [
            [3, 2, 1], [3, 3, 1], [3, 4, 1], [3, 5, 1],
            [3, 6, 1], [3, 7, 1], [4, 4, 1], [4, 5, 1],
            [4, 6, 1], [4, 7, 1]
        ],
        'B101': [
            [0, 4, 1], [0, 5, 1]
        ],
        'B102': [
            [0, 2, 1], [0, 3, 1], [0, 4, 1], [0, 5, 1],
            [2, 4, 1], [2, 5, 1], [2, 6, 1], [2, 7, 1],
            [2, 10, 1], [2, 11, 1], [2, 12, 1], [2, 13, 1],
            [2, 14, 1], [2, 15, 1]
        ],
        'B103': [
            [0, 2, 1], [0, 3, 1], [0, 4, 1], [0, 5, 1],
            [0, 6, 3], [0, 7, 3], [3, 11, 1], [3, 12, 1],
            [4, 4, 1], [4, 5, 1], [4, 6, 1], [4, 7, 1],
            [4, 8, 1], [4, 9, 1], [4, 12, 1], [4, 13, 1]
        ],
        'B104': [
            [3, 2, 4], [3, 3, 4]
        ],
        'B105': [
            [0, 11, 1], [0, 12, 1], [0, 13, 1], [0, 14, 1],
            [1, 11, 1], [1, 12, 1], [1, 13, 1], [2, 2, 1],
            [2, 3, 1], [2, 4, 1], [2, 5, 1], [2, 6, 1],
            [2, 7, 1], [3, 2, 1], [3, 3, 1], [3, 4, 1],
            [3, 5, 1], [3, 6, 1], [3, 7, 1]
        ],
        'B106': [
            [0, 10, 1], [0, 11, 1], [0, 12, 1], [0, 13, 1],
            [2, 6, 1], [2, 7, 1], [2, 12, 1], [2, 13, 1],
            [2, 14, 1], [2, 15, 1], [3, 2, 1], [3, 3, 1],
            [3, 4, 1], [3, 11, 1], [3, 12, 1], [3, 13, 1]
        ],
        'B107': [
            [0, 2, 1], [0, 3, 1], [0, 4, 1], [0, 5, 1],
            [1, 12, 1], [1, 13, 1], [1, 14, 1], [1, 15, 1],
            [3, 13, 1], [3, 14, 1]
        ],
        'B108': [
            [1, 6, 1], [1, 7, 1], [1, 8, 1], [1, 9, 1]
        ],
        // B109 — no in-scope class in 202505
        'B109': [],
        'B110': [
            [0, 6, 1], [0, 7, 1], [0, 8, 1], [0, 9, 1],
            [1, 2, 1], [1, 3, 1], [1, 4, 1], [1, 5, 1]
        ],
        'B111': [
            [0, 2, 1], [0, 3, 1], [0, 4, 1], [0, 5, 1],
            [0, 6, 1], [0, 7, 1], [0, 8, 1], [0, 9, 1],
            [1, 4, 1], [1, 5, 1], [1, 6, 1], [1, 7, 1]
        ],
        'B005': [
            [1, 8, 1], [1, 9, 1], [1, 10, 1], [1, 11, 1],
            [2, 6, 1], [2, 7, 1], [2, 8, 1], [2, 9, 1],
            [3, 2, 1], [3, 3, 1], [3, 4, 1], [3, 5, 1],
            [3, 10, 1], [3, 11, 1]
        ],
        'B009': [
            [1, 2, 1], [1, 3, 1], [1, 10, 1], [1, 11, 1],
            [1, 12, 1], [1, 13, 1], [1, 14, 1], [1, 15, 1],
            [1, 16, 1], [1, 17, 1], [2, 2, 1], [2, 3, 1],
            [2, 4, 1], [2, 5, 1], [2, 6, 1], [2, 7, 1],
            [3, 3, 1], [3, 4, 1], [3, 8, 1], [3, 9, 1],
            [3, 10, 1], [3, 11, 1], [3, 12, 1], [3, 13, 1],
            [4, 14, 1], [4, 15, 1]
        ],
        'B010': [
            [1, 11, 1], [1, 12, 1], [1, 13, 1], [1, 14, 1],
            [1, 15, 1], [1, 16, 1], [2, 2, 1], [2, 3, 1],
            [2, 4, 1], [2, 5, 1], [2, 6, 1], [2, 7, 1],
            [2, 12, 1], [2, 13, 1], [2, 14, 1], [2, 15, 1],
            [3, 8, 1], [3, 9, 1], [3, 11, 1], [3, 12, 1],
            [4, 2, 1], [4, 3, 1], [4, 15, 1], [4, 16, 1]
        ],
        'B011': [
            [2, 4, 1], [2, 5, 1], [2, 6, 1], [2, 7, 1],
            [2, 11, 1], [2, 12, 1], [2, 13, 1], [2, 14, 1],
            [2, 15, 1], [2, 16, 1], [3, 13, 1], [3, 14, 1],
            [3, 15, 1], [3, 16, 1], [4, 3, 1], [4, 4, 1],
            [4, 6, 1], [4, 7, 1]
        ],
        'B006': [
            [0, 2, 1], [0, 3, 1], [0, 4, 1], [0, 5, 1],
            [0, 6, 1], [0, 7, 1], [0, 10, 1], [0, 11, 1],
            [0, 12, 1], [0, 13, 1], [1, 6, 1], [1, 7, 1],
            [1, 8, 1], [1, 9, 1], [1, 11, 1], [1, 12, 1],
            [1, 13, 1], [1, 14, 1], [2, 2, 1], [2, 3, 1],
            [2, 4, 1], [2, 5, 1], [2, 12, 1], [2, 13, 1],
            [2, 14, 1], [2, 15, 1], [2, 16, 1], [2, 17, 1],
            [3, 2, 1], [3, 3, 1], [3, 4, 1], [3, 5, 1],
            [3, 12, 1], [3, 13, 1], [3, 14, 1], [3, 15, 1],
            [4, 4, 1], [4, 5, 1], [4, 6, 1], [4, 7, 1],
            [4, 8, 1], [4, 9, 1], [4, 10, 1], [4, 11, 1],
            [4, 13, 1], [4, 14, 1], [4, 15, 1], [4, 16, 1]
        ],
    },
    // ─────────────────────────────────────────────────────────────────────
    // §2.12 replacementHistory — consumed by the student Replacement
    // History page (/replacement-history-ui; renamed from
    // upcoming-replacements-ui 2026-10-06 — see page-changelogs/todo
    // list/replacement-history-ui-plan.md).
    // Rows are keyed to real rsd3g2Base courses + rsd3g2Flags weeks so the
    // timetable grid and this list stay consistent.
    //   Original slot: di/start/end in the same 30-min index space as
    //     rsd3g2Base (hours map in ui-common.js).
    //   Replacement slot: newDi/newStart/newEnd + display strings;
    //     null when still awaiting PL approval (`status:'pending'`).
    //   status: 'replacement' = approved & upcoming (blue,
    //     .badge-replacement) | 'pending' = awaiting PL (yellow,
    //     .badge-pending).
    // Read-only — slice()/spread before mutating.
    // ─────────────────────────────────────────────────────────────────────
    replacementHistory: [
        {
            id: 1, code: 'BMIT7074', name: 'Software Testing', type: 'T',
            lecturer: 'Dr. Koh Li May', cohort: 'RSD3(S1)G2',
            week: 1, status: 'replacement', requestedAt: '25-Aug-2026',
            di: 1, start: 8, end: 11, originalDay: 'Tuesday',  originalTime: '12:00 – 14:00', originalVenue: 'A106',
            newDi: 3, newStart: 4, newEnd: 7, newDay: 'Thursday', newTime: '10:00 – 12:00', newVenue: 'B005',
        },
        {
            id: 2, code: 'BMIT2233', name: 'Data Structures', type: 'T',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 2, status: 'pending', requestedAt: '',
            di: 4, start: 8, end: 10, originalDay: 'Friday', originalTime: '12:00 – 13:30', originalVenue: 'B103',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 3, code: 'BMIT5678', name: 'Database Systems', type: 'L',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 7, status: 'replacement', requestedAt: '01-Sep-2026',
            di: 2, start: 2, end: 5, originalDay: 'Wednesday', originalTime: '09:00 – 11:00', originalVenue: 'B106',
            newDi: 0, newStart: 12, newEnd: 15, newDay: 'Monday', newTime: '14:00 – 16:00', newVenue: 'B110',
        },
        {
            id: 4, code: 'BMIT7073', name: 'IT Ethics', type: 'L',
            lecturer: 'Ms. Lim Pei Shan', cohort: 'RSD3(S1)G2',
            week: 2, status: 'pending', requestedAt: '',
            di: 4, start: 0, end: 3, originalDay: 'Friday', originalTime: '08:00 – 10:00', originalVenue: 'A105',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 5, code: 'BMIT7075', name: 'Mobile Application Development', type: 'L',
            lecturer: 'Ms. Lim Pei Shan', cohort: 'RSD3(S1)G2',
            week: 2, status: 'pending', requestedAt: '',
            di: 3, start: 12, end: 15, originalDay: 'Thursday', originalTime: '14:00 – 16:00', originalVenue: 'A106',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 6, code: 'BMIT7071', name: 'Research Methods', type: 'T',
            lecturer: 'Dr. Koh Li May', cohort: 'RSD3(S1)G2',
            week: 4, status: 'pending', requestedAt: '',
            di: 1, start: 4, end: 7, originalDay: 'Tuesday', originalTime: '10:00 – 12:00', originalVenue: 'A103',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 7, code: 'BMIT5678', name: 'Database Systems', type: 'L',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 4, status: 'pending', requestedAt: '',
            di: 2, start: 2, end: 5, originalDay: 'Wednesday', originalTime: '09:00 – 11:00', originalVenue: 'B106',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 8, code: 'BMIT7075', name: 'Mobile Application Development', type: 'L',
            lecturer: 'Ms. Lim Pei Shan', cohort: 'RSD3(S1)G2',
            week: 9, status: 'pending', requestedAt: '',
            di: 3, start: 12, end: 15, originalDay: 'Thursday', originalTime: '14:00 – 16:00', originalVenue: 'A106',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 9, code: 'BMIT2233', name: 'Data Structures', type: 'T',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 9, status: 'pending', requestedAt: '',
            di: 4, start: 8, end: 10, originalDay: 'Friday', originalTime: '12:00 – 13:30', originalVenue: 'B103',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 10, code: 'BMIT7071', name: 'Research Methods', type: 'T',
            lecturer: 'Dr. Koh Li May', cohort: 'RSD3(S1)G2',
            week: 13, status: 'pending', requestedAt: '',
            di: 1, start: 4, end: 7, originalDay: 'Tuesday', originalTime: '10:00 – 12:00', originalVenue: 'A103',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 11, code: 'BMIT2233', name: 'Data Structures', type: 'T',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 13, status: 'pending', requestedAt: '',
            di: 4, start: 8, end: 10, originalDay: 'Friday', originalTime: '12:00 – 13:30', originalVenue: 'B103',
            newDi: null, newStart: null, newEnd: null, newDay: null, newTime: null, newVenue: null,
        },
        {
            id: 12, code: 'BMIT7072', name: 'Capstone Project', type: 'L',
            lecturer: 'Prof. Dr. Suresh', cohort: 'RSD3(S1)G2',
            week: 1, status: 'replacement', requestedAt: '26-Aug-2026',
            di: 2, start: 12, end: 15, originalDay: 'Wednesday', originalTime: '14:00 – 16:00', originalVenue: 'A104',
            newDi: 3, newStart: 8, newEnd: 11, newDay: 'Thursday', newTime: '12:00 – 14:00', newVenue: 'B101',
        },
        {
            id: 13, code: 'BMIT5678', name: 'Database Systems', type: 'L',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 1, status: 'replacement', requestedAt: '24-Aug-2026',
            di: 2, start: 2, end: 5, originalDay: 'Wednesday', originalTime: '09:00 – 11:00', originalVenue: 'B106',
            newDi: 0, newStart: 12, newEnd: 15, newDay: 'Monday', newTime: '14:00 – 16:00', newVenue: 'B110',
        },
        {
            id: 14, code: 'BMIT7070', name: 'Advanced Software Engineering', type: 'L',
            lecturer: 'Prof. Dr. Khoo Teik Huat', cohort: 'RSD3(S1)G2',
            week: 3, status: 'replacement', requestedAt: '27-Aug-2026',
            di: 0, start: 8, end: 11, originalDay: 'Monday', originalTime: '12:00 – 14:00', originalVenue: 'A101',
            newDi: 2, newStart: 8, newEnd: 11, newDay: 'Wednesday', newTime: '12:00 – 14:00', newVenue: 'B102',
        },
        {
            id: 15, code: 'BMIT2233', name: 'Data Structures', type: 'T',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 5, status: 'replacement', requestedAt: '07-Sep-2026',
            di: 4, start: 8, end: 10, originalDay: 'Friday', originalTime: '12:00 – 13:30', originalVenue: 'B103',
            newDi: 3, newStart: 8, newEnd: 11, newDay: 'Thursday', newTime: '12:00 – 14:00', newVenue: 'B103',
        },
        {
            id: 16, code: 'BMIT7074', name: 'Software Testing', type: 'T',
            lecturer: 'Dr. Koh Li May', cohort: 'RSD3(S1)G2',
            week: 7, status: 'replacement', requestedAt: '01-Sep-2026',
            di: 1, start: 8, end: 11, originalDay: 'Tuesday', originalTime: '12:00 – 14:00', originalVenue: 'A106',
            newDi: 2, newStart: 8, newEnd: 11, newDay: 'Wednesday', newTime: '12:00 – 14:00', newVenue: 'B104',
        },
        {
            id: 17, code: 'BMIT7072', name: 'Capstone Project', type: 'L',
            lecturer: 'Prof. Dr. Suresh', cohort: 'RSD3(S1)G2',
            week: 11, status: 'replacement', requestedAt: '08-Sep-2026',
            di: 2, start: 12, end: 15, originalDay: 'Wednesday', originalTime: '14:00 – 16:00', originalVenue: 'A104',
            newDi: 3, newStart: 4, newEnd: 7, newDay: 'Thursday', newTime: '10:00 – 12:00', newVenue: 'B101',
        },
        {
            id: 18, code: 'BMIT5678', name: 'Database Systems', type: 'L',
            lecturer: 'En. Lim Jia Zheng', cohort: 'RSD3(S1)G2',
            week: 11, status: 'replacement', requestedAt: '08-Sep-2026',
            di: 2, start: 2, end: 5, originalDay: 'Wednesday', originalTime: '09:00 – 11:00', originalVenue: 'B106',
            newDi: 0, newStart: 12, newEnd: 15, newDay: 'Monday', newTime: '14:00 – 16:00', newVenue: 'B110',
        },
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.13 notifications — mock notifications panel seed (frozen design
    // §5 of the notifications-panel SDD change). 12 rows, 4 per role
    // (`pl` / `lecturer` / `student`). Single-string seeds are pre-split
    // at their first "·" (or "—" for n-pl-4) into title/desc for the
    // .notif-row-title + .notif-row-desc renderer. `read: true` appears
    // ONLY on the six pre-seed-read rows; unread rows omit the key.
    // Read-only — slice()/spread before mutating.
    // ─────────────────────────────────────────────────────────────────────
    notifications: [
        {
            id: 'n-pl-1', role: 'pl', type: 'submitted',
            title: 'New replacement request',
            desc: 'BMIT5678 Database Systems · submitted by En. Lim Jia Zheng',
            minutesAgo: 25, link: '/request-approval-ui',
        },
        {
            id: 'n-pl-2', role: 'pl', type: 'submitted',
            title: 'BMIT7075 Mobile App Development',
            desc: 'submitted by Ms. Lim Pei Shan',
            minutesAgo: 90, link: '/request-approval-ui', read: true,
        },
        {
            id: 'n-pl-3', role: 'pl', type: 'awaiting',
            title: 'Approval still pending',
            desc: '2 requests awaiting your decision this week',
            minutesAgo: 240, link: '/request-approval-ui',
        },
        {
            id: 'n-pl-4', role: 'pl', type: 'awaiting',
            title: '1 request awaiting approval',
            desc: 'BMIT2233, Week 10',
            minutesAgo: 420, link: '/request-approval-ui', read: true,
        },
        {
            id: 'n-lec-1', role: 'lecturer', type: 'approved',
            title: 'Request approved',
            desc: 'BMIT5678 · replacement to Mon 14:00 – 16:00 @ B110 approved',
            minutesAgo: 12, link: '/my-request-history-ui',
        },
        {
            id: 'n-lec-2', role: 'lecturer', type: 'approved',
            title: 'BMIT2233',
            desc: 'replacement to Thu 12:00 – 14:00 @ B103 approved',
            minutesAgo: 150, link: '/my-request-history-ui', read: true,
        },
        {
            id: 'n-lec-3', role: 'lecturer', type: 'rejected',
            title: 'Request rejected',
            desc: 'BMIT7071 Research Methods · PL asked for an alternative slot',
            minutesAgo: 1440, link: '/my-request-history-ui',
        },
        {
            id: 'n-lec-4', role: 'lecturer', type: 'rejected',
            title: 'BMIT7073 IT Ethics',
            desc: 'PL asked for an alternative slot',
            minutesAgo: 2880, link: '/my-request-history-ui', read: true,
        },
        {
            id: 'n-stu-1', role: 'student', type: 'update',
            title: 'BMIT5678',
            desc: 'replacement class Mon 14:00 – 16:00 @ B110',
            minutesAgo: 30, link: '/replacement-history-ui',
        },
        {
            id: 'n-stu-2', role: 'student', type: 'update',
            title: 'BMIT2233',
            desc: 'replacement class Thu 12:00 – 14:00 @ B103',
            minutesAgo: 120, link: '/replacement-history-ui',
        },
        {
            id: 'n-stu-3', role: 'student', type: 'update',
            title: 'BMIT7074',
            desc: 'replacement moved to Thu 10:00 – 12:00 @ B005',
            minutesAgo: 1560, link: '/replacement-history-ui', read: true,
        },
        {
            id: 'n-stu-4', role: 'student', type: 'update',
            title: 'BMIT7070',
            desc: 'replacement class Wed 12:00 – 14:00 @ B102',
            minutesAgo: 4320, link: '/replacement-history-ui', read: true,
        },

        // ─── TEMP dummy rows — UI testing only, DELETE BEFORE SUBMISSION ───
        {
            id: 'n-tmp-1', role: 'student', type: 'update',
            title: 'BMIT7071',
            desc: 'replacement moved to Week 11 Wed 10:00 – 12:00 @ B104',
            minutesAgo: 5, link: '/replacement-history-ui',
        },
        {
            id: 'n-tmp-2', role: 'student', type: 'update',
            title: 'BMIT5678',
            desc: 'replacement class Tue 09:00 – 11:00 @ B201',
            minutesAgo: 65, link: '/replacement-history-ui',
        },
        {
            id: 'n-tmp-3', role: 'pl', type: 'submitted',
            title: 'New replacement request',
            desc: 'BMIT7073 IT Ethics · submitted by En. Lim Jia Zheng',
            minutesAgo: 45, link: '/request-approval-ui',
        },
        {
            id: 'n-tmp-4', role: 'lecturer', type: 'approved',
            title: 'Request approved',
            desc: 'BMIT7074 · replacement to Tue 09:00 – 11:00 @ B201 approved',
            minutesAgo: 100, link: '/my-request-history-ui', read: true,
        },
        {
            id: 'n-tmp-5', role: 'pl', type: 'awaiting',
            title: 'Approval still pending',
            desc: '1 request awaiting approval — BMIT5678, Week 10',
            minutesAgo: 300, link: '/request-approval-ui', read: true,
        },
        {
            id: 'n-tmp-6', role: 'student', type: 'update',
            title: 'BMIT2233',
            desc: 'replacement moved to Thu 08:00 – 10:00 @ B202',
            minutesAgo: 2, link: '/replacement-history-ui',
        },
        {
            id: 'n-tmp-7', role: 'pl', type: 'submitted',
            title: 'BMIT7070 Network Programming',
            desc: 'submitted by Ms. Lim Pei Shan',
            minutesAgo: 320, link: '/request-approval-ui', read: true,
        },
        {
            id: 'n-tmp-8', role: 'lecturer', type: 'rejected',
            title: 'Request rejected',
            desc: 'BMIT5678 · PL asked for an alternative slot',
            minutesAgo: 700, link: '/my-request-history-ui',
        },
        {
            id: 'n-tmp-10', role: 'lecturer', type: 'approved',
            title: 'Request approved',
            desc: 'BMIT7071 · replacement to Fri 14:00 – 16:00 @ B105 approved',
            minutesAgo: 500, link: '/my-request-history-ui', read: true,
        },
    ],

};

// ─────────────────────────────────────────────────────────────────────────
// Compatibility globals — OWNED BY THE FROZEN request-approval SDD CHANGE.
// DO NOT redeclare in a page script (throws "Identifier already declared").
// `approvalRequests` (20 entries) + `URGENCY_REFERENCE_DATE` are defined
// BELOW this block (preserved verbatim from the sibling change's apply).
// The aliasing assignments (MockData.approvalRequests / .urgencyReferenceDate)
// are appended at the very END of this file so they execute AFTER the `const`
// declarations (TDZ-safe). Look further down for them.
// ─────────────────────────────────────────────────────────────────────────

// Helper: generate ISO datetime string relative to today
function _relDateTime(daysOffset, hours, minutes) {
    var d = new Date();
    d.setDate(d.getDate() + daysOffset);
    d.setHours(hours || 9, minutes || 0, 0, 0);
    return d.toISOString().slice(0, 19);
}
function _relDate(daysOffset) {
    var d = new Date();
    d.setDate(d.getDate() + daysOffset);
    return d.toISOString().slice(0, 10);
}
function _dayName(daysOffset) {
    return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][new Date(Date.now() + daysOffset * 86400000).getDay()];
}

const approvalRequests = [
    {
        id: 1,
        lecturer: 'Dr. Christopher Lazarus',
        requestedAt: _relDateTime(-1, 9, 15),
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'L',
        classDate: _relDate(1),
        classDay: _dayName(1),
        timeStart: '09:00',
        timeEnd: '11:00',
        duration: 2,
        venue: 'C201',
        totalStudents: 40,
        cohortCounts: [22, 18],
        cohorts: ['DIT2 (S1)', 'DSE2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(3),
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'C201',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(-1, 14, 30),
        remarks: null
    },
    {
        id: 2,
        lecturer: 'En. Lim Jia Zheng',
        requestedAt: _relDateTime(-2, 14, 20),
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'T',
        classDate: _relDate(2),
        classDay: _dayName(2),
        timeStart: '14:00',
        timeEnd: '15:00',
        duration: 1,
        venue: 'D103',
        totalStudents: 25,
        cohorts: ['DCS2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(4),
        replacementTime: '14:00 – 15:00',
        replacementVenue: 'D103',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(-2, 16, 0),
        remarks: null
    },
    {
        id: 3,
        lecturer: 'En. Jefther Edward',
        requestedAt: _relDateTime(-3, 10, 0),
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: _relDate(5),
        classDay: _dayName(5),
        timeStart: '10:00',
        timeEnd: '13:00',
        duration: 3,
        venue: 'E201',
        totalStudents: 50,
        cohortCounts: [25, 25],
        cohorts: ['DAI2 (S1)', 'DNE2 (S1)'],
        status: 'Approved',
        rejectionReason: null,
        slotValidity: 'conflict',
        conflictReason: 'Room C202 already occupied',
        replacementDate: _relDate(7),
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: _relDateTime(-2, 8, 30),
        viewedAt: _relDateTime(-3, 14, 0),
        remarks: null
    },
    {
        id: 4,
        lecturer: 'En. Jefther Edward',
        requestedAt: _relDateTime(-3, 11, 30),
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: _relDate(2),
        classDay: _dayName(2),
        timeStart: '10:00',
        timeEnd: '13:00',
        duration: 3,
        venue: 'E201',
        totalStudents: 50,
        cohortCounts: [25, 25],
        cohorts: ['DAI2 (S1)', 'DNE2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'conflict',
        conflictReason: 'Room E201 already occupied',
        replacementDate: _relDate(5),
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'E201',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(-3, 15, 0),
        remarks: 'Students have lab session on original date'
    },
    {
        id: 5,
        lecturer: 'Dr. Chang Foo Chung',
        requestedAt: _relDateTime(0, 8, 5),
        courseCode: 'BMIT5504',
        courseName: 'Machine Learning Fundamentals',
        classType: 'T',
        classDate: _relDate(1),
        classDay: _dayName(1),
        timeStart: '11:00',
        timeEnd: '12:00',
        duration: 1,
        venue: 'D104',
        totalStudents: 30,
        cohorts: ['DDA2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(3),
        replacementTime: '11:00 – 12:00',
        replacementVenue: 'D104',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(0, 10, 0),
        remarks: null
    },
    {
        id: 6,
        lecturer: 'Pn. Surayaini Binti Basri',
        requestedAt: _relDateTime(1, 10, 40),
        courseCode: 'BMIT6605',
        courseName: 'Database Administration',
        classType: 'L',
        classDate: _relDate(5),
        classDay: _dayName(5),
        timeStart: '09:00',
        timeEnd: '12:00',
        duration: 3,
        venue: 'C202',
        totalStudents: 45,
        cohortCounts: [25, 20],
        cohorts: ['DIT2 (S1)', 'DCS2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(8),
        replacementTime: '09:00 – 12:00',
        replacementVenue: 'C202',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(1, 14, 0),
        remarks: null
    },
    {
        id: 7,
        lecturer: 'Dr. Christopher Lazarus',
        requestedAt: _relDateTime(2, 9, 0),
        courseCode: 'BMIT7706',
        courseName: 'Information Security',
        classType: 'T',
        classDate: _relDate(8),
        classDay: _dayName(8),
        timeStart: '15:00',
        timeEnd: '16:00',
        duration: 1,
        venue: 'E202',
        totalStudents: 28,
        cohorts: ['DSE2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'conflict',
        conflictReason: 'Room E202 already occupied',
        replacementDate: _relDate(11),
        replacementTime: '15:00 – 16:00',
        replacementVenue: 'E202',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(2, 11, 0),
        remarks: null
    },
    {
        id: 8,
        lecturer: 'En. Lim Jia Zheng',
        requestedAt: _relDateTime(4, 15, 30),
        courseCode: 'BMIT8807',
        courseName: 'Distributed Systems',
        classType: 'L',
        classDate: _relDate(10),
        classDay: _dayName(10),
        timeStart: '10:00',
        timeEnd: '12:00',
        duration: 2,
        venue: 'D103',
        totalStudents: 42,
        cohortCounts: [22, 20],
        cohorts: ['DNE2 (S1)', 'DDA2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(12),
        replacementTime: '10:00 – 12:00',
        replacementVenue: 'D103',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(4, 17, 0),
        remarks: null
    },
    {
        id: 9,
        lecturer: 'En. Jefther Edward',
        requestedAt: _relDateTime(7, 12, 10),
        courseCode: 'BMIT9908',
        courseName: 'Artificial Intelligence',
        classType: 'T',
        classDate: _relDate(18),
        classDay: _dayName(18),
        timeStart: '09:00',
        timeEnd: '10:00',
        duration: 1,
        venue: 'C201',
        totalStudents: 35,
        cohorts: ['DAI2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(19),
        replacementTime: '09:00 – 10:00',
        replacementVenue: 'C201',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: _relDateTime(7, 14, 0),
        remarks: null
    },
    {
        id: 10,
        lecturer: 'Dr. Chang Foo Chung',
        requestedAt: _relDateTime(2, 9, 15),
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'T',
        classDate: _relDate(7),
        classDay: _dayName(7),
        timeStart: '09:00',
        timeEnd: '10:00',
        duration: 1,
        venue: 'D104',
        totalStudents: 22,
        cohorts: ['DIT2 (S1)'],
        status: 'Approved',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(8),
        replacementTime: '09:00 – 10:00',
        replacementVenue: 'D104',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: _relDateTime(2, 10, 15),
        viewedAt: _relDateTime(2, 9, 45),
        remarks: null
    },
    {
        id: 11,
        lecturer: 'Pn. Surayaini Binti Basri',
        requestedAt: _relDateTime(2, 14, 45),
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'L',
        classDate: _relDate(7),
        classDay: _dayName(7),
        timeStart: '14:00',
        timeEnd: '17:00',
        duration: 3,
        venue: 'E202',
        totalStudents: 48,
        cohortCounts: [25, 23],
        cohorts: ['DCS2 (S1)', 'DDA2 (S1)'],
        status: 'Approved',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(10),
        replacementTime: '14:00 – 17:00',
        replacementVenue: 'E202',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: _relDateTime(3, 9, 45),
        viewedAt: _relDateTime(2, 16, 0),
        remarks: 'Venue swap approved with Room E202'
    },
    {
        id: 12,
        lecturer: 'Dr. Christopher Lazarus',
        requestedAt: _relDateTime(3, 8, 30),
        courseCode: 'BMIT5504',
        courseName: 'Machine Learning Fundamentals',
        classType: 'T',
        classDate: _relDate(9),
        classDay: _dayName(9),
        timeStart: '10:00',
        timeEnd: '11:00',
        duration: 1,
        venue: 'C202',
        totalStudents: 26,
        cohorts: ['DSE2 (S1)'],
        status: 'Approved',
        rejectionReason: null,
        slotValidity: 'conflict',
        conflictReason: 'Room C202 already occupied',
        replacementDate: _relDate(11),
        replacementTime: '10:00 – 11:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: _relDateTime(3, 14, 0),
        viewedAt: _relDateTime(3, 9, 30),
        remarks: null
    },
    {
        id: 13,
        lecturer: 'En. Lim Jia Zheng',
        requestedAt: _relDateTime(4, 11, 20),
        courseCode: 'BMIT6605',
        courseName: 'Database Administration',
        classType: 'L',
        classDate: _relDate(10),
        classDay: _dayName(10),
        timeStart: '09:00',
        timeEnd: '11:00',
        duration: 2,
        venue: 'D103',
        totalStudents: 38,
        cohortCounts: [20, 18],
        cohorts: ['DNE2 (S1)', 'DCS2 (S1)'],
        status: 'Approved',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(12),
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'D103',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: _relDateTime(3, 16, 30),
        viewedAt: _relDateTime(4, 13, 0),
        remarks: null
    },
    {
        id: 14,
        lecturer: 'En. Jefther Edward',
        requestedAt: _relDateTime(5, 9, 35),
        courseCode: 'BMIT7706',
        courseName: 'Information Security',
        classType: 'T',
        classDate: _relDate(12),
        classDay: _dayName(12),
        timeStart: '11:00',
        timeEnd: '12:00',
        duration: 1,
        venue: 'E201',
        totalStudents: 24,
        cohorts: ['DAI2 (S1)'],
        status: 'Rejected',
        rejectionReason: 'Replacement venue not available',
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(14),
        replacementTime: '11:00 – 12:00',
        replacementVenue: 'E201',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: _relDateTime(5, 11, 0),
        viewedAt: _relDateTime(5, 10, 0),
        remarks: null
    },
    {
        id: 15,
        lecturer: 'Dr. Chang Foo Chung',
        requestedAt: _relDateTime(6, 10, 5),
        courseCode: 'BMIT8807',
        courseName: 'Distributed Systems',
        classType: 'L',
        classDate: _relDate(13),
        classDay: _dayName(13),
        timeStart: '10:00',
        timeEnd: '13:00',
        duration: 3,
        venue: 'C201',
        totalStudents: 44,
        cohortCounts: [24, 20],
        cohorts: ['DIT2 (S1)', 'DCS2 (S1)'],
        status: 'Rejected',
        rejectionReason: 'Proposed slot conflicts with another class',
        slotValidity: 'conflict',
        conflictReason: 'Time slot overlaps with another replacement class',
        replacementDate: _relDate(16),
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'C201',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: _relDateTime(6, 9, 20),
        viewedAt: _relDateTime(6, 11, 0),
        remarks: null
    },
    {
        id: 16,
        lecturer: 'Pn. Surayaini Binti Basri',
        requestedAt: _relDateTime(7, 13, 50),
        courseCode: 'BMIT9908',
        courseName: 'Artificial Intelligence',
        classType: 'T',
        classDate: _relDate(14),
        classDay: _dayName(14),
        timeStart: '14:00',
        timeEnd: '15:00',
        duration: 1,
        venue: 'D104',
        totalStudents: 30,
        cohorts: ['DSE2 (S1)'],
        status: 'Rejected',
        rejectionReason: 'Insufficient notice for replacement',
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(17),
        replacementTime: '14:00 – 15:00',
        replacementVenue: 'D104',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: _relDateTime(7, 10, 40),
        viewedAt: _relDateTime(7, 14, 30),
        remarks: null
    },
    {
        id: 17,
        lecturer: 'Dr. Christopher Lazarus',
        requestedAt: _relDateTime(8, 16, 15),
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'L',
        classDate: _relDate(15),
        classDay: _dayName(15),
        timeStart: '09:00',
        timeEnd: '12:00',
        duration: 3,
        venue: 'E202',
        totalStudents: 40,
        cohortCounts: [22, 18],
        cohorts: ['DNE2 (S1)', 'DDA2 (S1)'],
        status: 'Rejected',
        rejectionReason: 'Replacement lecturer unavailable',
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(18),
        replacementTime: '09:00 – 12:00',
        replacementVenue: 'E202',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: _relDateTime(8, 15, 10),
        viewedAt: _relDateTime(8, 17, 0),
        remarks: 'Suggest contacting part-time lecturer pool'
    },
    {
        id: 18,
        lecturer: 'En. Lim Jia Zheng',
        requestedAt: _relDateTime(1, 9, 20),
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'T',
        classDate: _relDate(7),
        classDay: _dayName(7),
        timeStart: '15:00',
        timeEnd: '16:00',
        duration: 1,
        venue: 'C202',
        totalStudents: 25,
        cohorts: ['DCS2 (S1)'],
        status: 'Completed',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(8),
        replacementTime: '15:00 – 16:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: _relDateTime(2, 13, 25),
        viewedAt: _relDateTime(1, 10, 0),
        remarks: null
    },
    {
        id: 19,
        lecturer: 'En. Jefther Edward',
        requestedAt: _relDateTime(2, 11, 55),
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: _relDate(7),
        classDay: _dayName(7),
        timeStart: '09:00',
        timeEnd: '11:00',
        duration: 2,
        venue: 'D103',
        totalStudents: 50,
        cohortCounts: [25, 25],
        cohorts: ['DAI2 (S1)', 'DNE2 (S1)'],
        status: 'Completed',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(10),
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'D103',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: _relDateTime(3, 8, 50),
        viewedAt: _relDateTime(2, 14, 0),
        remarks: null
    },
    {
        id: 20,
        lecturer: 'Dr. Chang Foo Chung',
        requestedAt: _relDateTime(3, 14, 30),
        courseCode: 'BMIT5504',
        courseName: 'Machine Learning Fundamentals',
        classType: 'T',
        classDate: _relDate(10),
        classDay: _dayName(10),
        timeStart: '10:00',
        timeEnd: '11:00',
        duration: 1,
        venue: 'E201',
        totalStudents: 26,
        cohorts: ['DAI2 (S1)'],
        status: 'Cancelled',
        rejectionReason: null,
        slotValidity: 'valid',
        conflictReason: null,
        replacementDate: _relDate(11),
        replacementTime: '10:00 – 11:00',
        replacementVenue: 'E201',
        reviewedBy: null,
        reviewedAt: null,
        viewedAt: null,
        remarks: 'Request withdrawn by lecturer'
    }
];

const URGENCY_REFERENCE_DATE = new Date();

// ─────────────────────────────────────────────────────────────────────────
// Aliasing — MockData exposes the sibling request-approval globals as
// MockData.approvalRequests / MockData.urgencyReferenceDate pointing at the
// SAME values above (no data duplication). These lines run AFTER the `const`
// declarations above, so they are TDZ-safe. Do NOT move them above the consts.
// ─────────────────────────────────────────────────────────────────────────
window.MockData.approvalRequests = approvalRequests;
window.MockData.urgencyReferenceDate = URGENCY_REFERENCE_DATE;

/* §2.9 requester contract — sweep-fixes-round-1 (F-10). `requests` rows are
   the CURRENT persona's own submissions (my-request-history renders them as
   "my requests"; the approval-page dataset is separate — zero row overlap,
   verified in the sweep). requester is derived here, in one place, instead
   of 20 inline literals; rows are copied (read-only convention). Backend day
   the API returns ownership-scoped rows with their real requester identity
   — drop this block and let my-request-history's
   `requester === MockData.currentUser.name` filter (kept for exactly that
   transition) scope the payload. */
window.MockData.requests = window.MockData.requests.map(function(r) {
    return Object.assign({}, r, { requester: MockData.currentUser.name });
});

/* §2.13 recipient contract — sweep-fixes-round-1 (F-2 pre-wire). Every mock
   notification belongs to the logged-in persona's ONE mailbox (they demo all
   three role categories); `role` stays the category, `recipientId` is the
   mailbox owner. Read-state is keyed per user (ui-common). Backend day the
   API returns the user's own rows with real recipient ids — drop this block. */
window.MockData.notifications = window.MockData.notifications.map(function(n) {
    return Object.assign({}, n, { recipientId: MockData.currentUser.staffId });
});
