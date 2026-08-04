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
        const start = new Date('2026-06-15');   // Week-1 Monday
        const end = new Date('2026-09-20');      // Week-14 Sunday (start + 13*7 + 6 days)
        return {
            label: '202605 Semester',
            startDate: '2026-06-15',
            endDate: '2026-09-20',
            weeks: 14,
            chipText: '202605 Semester · ' + fmtChip(start) + ' ~ ' + fmtChip(end),
        };
    })(),

    // ─────────────────────────────────────────────────────────────────────
    // §2.2  holidays — declarative; CONSUMED BY CohortTimetable + Student My Timetable.
    // MyTimetable has no holiday render path today (explicit non-goal).
    // Replacement-arrangement's holiday stays embedded inside `arrangementWeeks`
    // (page-specific) — NOT duplicated here, to avoid double-sourcing.
    // ─────────────────────────────────────────────────────────────────────
    holidays: [
        { week: 1, dayIndex: 0, label: 'Public Holiday' }, // Week 1 Monday
        { week: 3, dayIndex: 1, label: 'Public Holiday' }, // Week 3 Tuesday
        { week: 3, dayIndex: 3, label: 'Public Holiday' }, // Week 3 Thursday
        { week: 5, dayIndex: 2, label: 'Public Holiday' }, // Week 5 Wednesday
        { week: 7, dayIndex: 4, label: 'Public Holiday' }, // Week 7 Friday
    ],

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
        { staffId: '5425', name: 'Pn. Surayaini Binti Basri',         role: 'Programme Leader',    department: 'DCIT', isPl: true  },
        { staffId: '5516', name: 'En. Mohd Nur Rahmat Bin Mohd Taat', role: 'Programme Leader',    department: 'DCIT', isPl: true  },
        { staffId: '4288', name: 'Dr. Christopher Lazarus',           role: 'Assistant Professor', department: 'DCIT', isPl: false },
        { staffId: '3221', name: 'Pn. Lee Yee Fong',                  role: 'Senior Lecturer',     department: 'DCIT', isPl: false },
        { staffId: '3825', name: 'Pn. Teng Nga Sing',                 role: 'Senior Lecturer',     department: 'DCIT', isPl: false },
        { staffId: '4127', name: 'Pn. Patricia G Kissol',             role: 'Senior Lecturer',     department: 'DCIT', isPl: false },
        { staffId: '2873', name: 'Cik Ellis Chieng',                  role: 'Lecturer',            department: 'DCIT', isPl: false },
        { staffId: '5514', name: 'Ts. Norshikin Binti Zainal Abidin', role: 'Lecturer',            department: 'DCIT', isPl: false },
        { staffId: '5599', name: 'En. Jefther Edward',                role: 'Lecturer',            department: 'DCIT', isPl: false },
        { staffId: '5652', name: 'En. Daniel Royd Michael',           role: 'Lecturer',            department: 'DCIT', isPl: false },
        { staffId: '5770', name: 'En. Lim Jia Zheng',                 role: 'Lecturer',            department: 'DCIT', isPl: false },
        { staffId: '3799', name: 'En. Muada Bin Ojih',                role: 'Lecturer',            department: 'DSSH', isPl: false },
        { staffId: '4363', name: 'Pn. Tan Sharon',                    role: 'Senior Lecturer',     department: 'DACB', isPl: false },
        { staffId: '5254', name: 'Dr. Chang Foo Chung',               role: 'Assistant Professor', department: 'DACB', isPl: false },
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
    // §2.6  myTimetable — was MyTimetable inline `eventsData`.
    // `seedWeek` defines the one fully-populated week (11); every other week
    // copies its status==='normal' events (weekly-repeat). The page MUST derive
    // a LOCAL per-week copy before mutating (eventsByWeek[i] = ...) — never
    // mutate MockData directly (shared read-only). `.slice()` shallow suffices
    // because MyTimetable's render only reassigns array slots, never mutates
    // individual event fields.
    // ─────────────────────────────────────────────────────────────────────
    myTimetable: {
        seedWeek: 11,
        eventsByWeek: {
            3: [],  // empty week — triggers "No classes this week" empty state
            5: [
                { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 24, status: 'normal', name: 'Object-Oriented Programming', remarks: '' },
                { di: 0, start: 12, end: 14, code: 'BMIT1234', type: 'T', venue: 'B104', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 22, status: 'replacement', name: 'Data Structures', remarks: '07-Sep-2026' },
                { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', studentCount: 28, status: 'normal', name: 'Database Systems', remarks: '' },
                { di: 1, start: 14, end: 16, code: 'BMIT5678', type: 'T', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', status: 'replacement', name: 'Database Systems', remarks: '08-Sep-2026' },
                { di: 2, start: 2,  end: 5,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD3(S1)G1 + RSD3(S1)G2', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G2'], studentCounts: [16, 9], status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 2, start: 12, end: 15, code: 'BMIT9012', type: 'T', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD2 (S1)', status: 'pending', name: 'Computer Networks', remarks: '', requestedAt: '05 Sep 2026, 11:00 AM', requestedBy: 'Pn. Surayaini Basri' },
                { di: 3, start: 4,  end: 7,  code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming', cohort: 'CSF2 (S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { di: 3, start: 10, end: 12, code: 'BMIT6666', type: 'T', venue: 'B111', lecturer: 'Pn. Sarah Tan', cohort: 'CSF2 (S1)', status: 'replacement', name: 'Mobile App Development', remarks: '09-Sep-2026' },
                { di: 4, start: 0,  end: 3,  code: 'BMIT3456', type: 'L', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 4, start: 5,  end: 7,  code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 5, start: 2,  end: 5,  code: 'BMIT7890', type: 'L', venue: 'B201', lecturer: 'En. Jefther Edward', cohort: 'RBU2 (S1)', status: 'normal', name: 'Project Management', remarks: '' },
                { di: 5, start: 12, end: 14, code: 'BMIT9999', type: 'T', venue: 'B202', lecturer: 'Dr. Tan Ah Meng', cohort: 'DMF2 (S1)', status: 'normal', name: 'Machine Learning', remarks: '' },
            ],
            7: [
                { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 24, status: 'normal', name: 'Object-Oriented Programming', remarks: '' },
                { di: 0, start: 12, end: 14, code: 'BMIT1234', type: 'T', venue: 'B104', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 22, status: 'normal', name: 'Data Structures', remarks: '' },
                { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', studentCount: 28, status: 'replacement', name: 'Database Systems', remarks: '21-Sep-2026' },
                { di: 1, start: 14, end: 16, code: 'BMIT5678', type: 'T', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', status: 'replacement', name: 'Database Systems', remarks: '22-Sep-2026' },
                { di: 2, start: 2,  end: 5,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD3(S1)G1 + RSD3(S1)G2', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G2'], studentCounts: [16, 9], status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 2, start: 12, end: 15, code: 'BMIT9012', type: 'T', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD2 (S1)', status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 3, start: 4,  end: 7,  code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming', cohort: 'CSF2 (S1)', status: 'pending', name: 'Software Engineering', remarks: '', requestedAt: '19 Sep 2026, 02:30 PM', requestedBy: 'Dr. Lim Wei Ming' },
                { di: 3, start: 10, end: 12, code: 'BMIT6666', type: 'T', venue: 'B111', lecturer: 'Pn. Sarah Tan', cohort: 'CSF2 (S1)', status: 'normal', name: 'Mobile App Development', remarks: '' },
                { di: 4, start: 0,  end: 3,  code: 'BMIT3456', type: 'L', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 4, start: 5,  end: 7,  code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'replacement', name: 'Artificial Intelligence', remarks: '23-Sep-2026' },
                { di: 5, start: 2,  end: 5,  code: 'BMIT7890', type: 'L', venue: 'B201', lecturer: 'En. Jefther Edward', cohort: 'RBU2 (S1)', status: 'normal', name: 'Project Management', remarks: '' },
                { di: 5, start: 12, end: 14, code: 'BMIT9999', type: 'T', venue: 'B202', lecturer: 'Dr. Tan Ah Meng', cohort: 'DMF2 (S1)', status: 'pending', name: 'Machine Learning', remarks: '', requestedAt: '20 Sep 2026, 09:00 AM', requestedBy: 'Dr. Tan Ah Meng' },
            ],
            9: [
                { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 24, status: 'replacement', name: 'Object-Oriented Programming', remarks: '05-Oct-2026' },
                { di: 0, start: 12, end: 14, code: 'BMIT1234', type: 'T', venue: 'B104', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 22, status: 'normal', name: 'Data Structures', remarks: '' },
                { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', studentCount: 28, status: 'normal', name: 'Database Systems', remarks: '' },
                { di: 1, start: 14, end: 16, code: 'BMIT5678', type: 'T', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', status: 'pending', name: 'Database Systems', remarks: '', requestedAt: '03 Oct 2026, 10:15 AM', requestedBy: 'En. Lim Jia Zheng' },
                { di: 2, start: 2,  end: 5,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD3(S1)G1 + RSD3(S1)G2', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G2'], studentCounts: [16, 9], status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 2, start: 12, end: 15, code: 'BMIT9012', type: 'T', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD2 (S1)', status: 'replacement', name: 'Computer Networks', remarks: '06-Oct-2026' },
                { di: 3, start: 4,  end: 7,  code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming', cohort: 'CSF2 (S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { di: 3, start: 10, end: 12, code: 'BMIT6666', type: 'T', venue: 'B111', lecturer: 'Pn. Sarah Tan', cohort: 'CSF2 (S1)', status: 'normal', name: 'Mobile App Development', remarks: '' },
                { di: 4, start: 0,  end: 3,  code: 'BMIT3456', type: 'L', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'pending', name: 'Artificial Intelligence', remarks: '', requestedAt: '04 Oct 2026, 03:45 PM', requestedBy: 'Dr. Chang Foo Chung' },
                { di: 4, start: 5,  end: 7,  code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 5, start: 2,  end: 5,  code: 'BMIT7890', type: 'L', venue: 'B201', lecturer: 'En. Jefther Edward', cohort: 'RBU2 (S1)', status: 'replacement', name: 'Project Management', remarks: '07-Oct-2026' },
                { di: 5, start: 12, end: 14, code: 'BMIT9999', type: 'T', venue: 'B202', lecturer: 'Dr. Tan Ah Meng', cohort: 'DMF2 (S1)', status: 'normal', name: 'Machine Learning', remarks: '' },
            ],
            11: [
                { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 24, status: 'normal', name: 'Object-Oriented Programming', remarks: '' },
                { di: 0, start: 12, end: 14, code: 'BMIT1234', type: 'T', venue: 'B104', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 22, status: 'normal', name: 'Data Structures', remarks: '' },
                { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', studentCount: 28, status: 'normal', name: 'Database Systems', remarks: '' },
                { di: 1, start: 14, end: 16, code: 'BMIT5678', type: 'T', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', status: 'replacement', name: 'Database Systems', remarks: '31-Aug-2026' },
                { di: 2, start: 2,  end: 5,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD3(S1)G1 + RSD3(S1)G2', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G2'], studentCounts: [16, 9], status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 2, start: 12, end: 15, code: 'BMIT9012', type: 'T', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD2 (S1)', status: 'replacement', name: 'Computer Networks', remarks: '26-Aug-2026' },
                { di: 3, start: 4,  end: 7,  code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming', cohort: 'CSF2 (S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { di: 3, start: 10, end: 12, code: 'BMIT6666', type: 'T', venue: 'B111', lecturer: 'Pn. Sarah Tan', cohort: 'CSF2 (S1)', status: 'normal', name: 'Mobile App Development', remarks: '' },
                { di: 4, start: 0,  end: 3,  code: 'BMIT3456', type: 'L', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 4, start: 5,  end: 7,  code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 5, start: 2,  end: 5,  code: 'BMIT7890', type: 'L', venue: 'B201', lecturer: 'En. Jefther Edward', cohort: 'RBU2 (S1)', status: 'normal', name: 'Project Management', remarks: '' },
                { di: 5, start: 12, end: 14, code: 'BMIT9999', type: 'T', venue: 'B202', lecturer: 'Dr. Tan Ah Meng', cohort: 'DMF2 (S1)', status: 'pending', name: 'Machine Learning', remarks: '', requestedAt: '03 Sep 2026, 10:30 AM', requestedBy: 'Dr. Tan Ah Meng' },
            ],
        },
    },

    // ─────────────────────────────────────────────────────────────────────
    // §2.7  cohortTimetable — was CohortTimetable inline `facultyData` +
    // `addEvent()` calls + RSD3-G2 base+flags.
    //
    // `events` is a FLAT list of the non-rsd3g2 addEvent() calls (the page
    // rebuilds allEvents[cohortId][weekIdx] from it). The RSD3 G2 cohort is
    // reconstructed ONLY from `rsd3g2Base` + `rsd3g2Flags` — it is NOT in
    // `events` (flattening it too would double-add → 14 events/week not 7).
    // dit2s1 has no events in source (left empty); dmc2s1 has weeks 0,2 only.
    // Holiday rule `d===3 && w===3` → read from `MockData.holidays`.
    // ─────────────────────────────────────────────────────────────────────
    cohortTimetable: {
        faculties: [
            {
                id: 'focs',
                name: 'Faculty of Computing and Information Technology (FOCS)',
                cohorts: [
                    { id: 'rsd2s1',   name: 'RSD2 (S1) — Bachelor of Computer Science (Soft. Eng.)' },
                    { id: 'rsd3s1g1', name: 'RSD3 (S1) G1 — Bachelor of Computer Science (Soft. Eng.)' },
                    { id: 'rsd3s1g2', name: 'RSD3 (S1) G2 — Bachelor of Computer Science (Soft. Eng.)' },
                    { id: 'dsf2s1',   name: 'DSF2 (S1) — Diploma in Computer Science' },
                    { id: 'dft2s1',   name: 'DFT2 (S1) — Diploma in Information Technology' },
                ],
            },
            {
                id: 'fcci',
                name: 'Faculty of Creative Industries (FCCI)',
                cohorts: [
                    { id: 'dmc2s1', name: 'DMC2 (S1) — Diploma in Mass Communication' },
                    { id: 'dit2s1', name: 'DIT2 (S1) — Diploma in Interior Design' },
                ],
            },
        ],

        // Flat list: { cohortId, week, event } — non-rsd3g2 addEvent() calls verbatim.
        events: [
            // ═══ FOCS / RSD2 (S1) ═══
            { cohortId: 'rsd2s1', week: 0, event: { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', status: 'normal',       name: 'Object-Oriented Programming', remarks: '' } },
            { cohortId: 'rsd2s1', week: 0, event: { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng',      status: 'normal',       name: 'Database Systems',            remarks: '' } },
            { cohortId: 'rsd2s1', week: 0, event: { di: 2, start: 12, end: 15, code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming',        status: 'normal',       name: 'Software Engineering',         remarks: '' } },
            { cohortId: 'rsd2s1', week: 0, event: { di: 3, start: 6,  end: 9,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri',      status: 'normal',       name: 'Computer Networks',           remarks: '' } },
            { cohortId: 'rsd2s1', week: 0, event: { di: 4, start: 10, end: 13, code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung',      status: 'normal',       name: 'Artificial Intelligence',      remarks: '' } },
            { cohortId: 'rsd2s1', week: 0, event: { di: 4, start: 16, end: 19, code: 'BMIT4040', type: 'L', venue: 'B301', lecturer: 'En. Ahmad Faiz',           status: 'normal',       name: 'Web Development',              remarks: '' } },

            { cohortId: 'rsd2s1', week: 1, event: { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', status: 'normal',       name: 'Object-Oriented Programming', remarks: '' } },
            { cohortId: 'rsd2s1', week: 1, event: { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng',      status: 'replacement',  name: 'Database Systems',            remarks: '24-Aug-2026' } },
            { cohortId: 'rsd2s1', week: 1, event: { di: 2, start: 12, end: 15, code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming',        status: 'normal',       name: 'Software Engineering',         remarks: '' } },
            { cohortId: 'rsd2s1', week: 1, event: { di: 3, start: 6,  end: 9,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri',      status: 'normal',       name: 'Computer Networks',           remarks: '' } },
            { cohortId: 'rsd2s1', week: 1, event: { di: 4, start: 10, end: 13, code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung',      status: 'normal',       name: 'Artificial Intelligence',      remarks: '' } },
            { cohortId: 'rsd2s1', week: 1, event: { di: 5, start: 0,  end: 3,  code: 'BMIT4040', type: 'L', venue: 'B301', lecturer: 'En. Ahmad Faiz',           status: 'normal',       name: 'Web Development',              remarks: '' } },

            { cohortId: 'rsd2s1', week: 2, event: { di: 0, start: 4,  end: 7,  code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', status: 'normal',       name: 'Object-Oriented Programming', remarks: '' } },
            { cohortId: 'rsd2s1', week: 2, event: { di: 1, start: 0,  end: 3,  code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng',      status: 'normal',       name: 'Database Systems',            remarks: '' } },
            { cohortId: 'rsd2s1', week: 2, event: { di: 2, start: 10, end: 13, code: 'BMIT9999', type: 'T', venue: 'B202', lecturer: 'Dr. Tan Ah Meng',          status: 'pending',      name: 'Machine Learning',            remarks: '', requestedAt: '03 Sep 2026, 10:30 AM', requestedBy: 'Dr. Tan Ah Meng' } },
            { cohortId: 'rsd2s1', week: 2, event: { di: 3, start: 6,  end: 9,  code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri',      status: 'replacement',  name: 'Computer Networks',           remarks: '26-Aug-2026' } },
            { cohortId: 'rsd2s1', week: 2, event: { di: 3, start: 12, end: 15, code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming',        status: 'normal',       name: 'Software Engineering',         remarks: '' } },
            { cohortId: 'rsd2s1', week: 2, event: { di: 4, start: 10, end: 13, code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung',      status: 'normal',       name: 'Artificial Intelligence',      remarks: '' } },
            { cohortId: 'rsd2s1', week: 2, event: { di: 4, start: 16, end: 19, code: 'BMIT4040', type: 'L', venue: 'B301', lecturer: 'En. Ahmad Faiz',           status: 'normal',       name: 'Web Development',              remarks: '' } },

            // ═══ FOCS / RSD3 (S1) G1 ═══
            { cohortId: 'rsd3s1g1', week: 0, event: { di: 0, start: 8,  end: 11, code: 'BMIT7070', type: 'L', venue: 'A101', lecturer: 'Prof. Dr. Khoo Teik Huat', status: 'normal',  name: 'Advanced Software Engineering', remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 0, event: { di: 1, start: 12, end: 15, code: 'BMIT7072', type: 'L', venue: 'A104', lecturer: 'Prof. Dr. Suresh',         status: 'normal',  name: 'Capstone Project',              remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 0, event: { di: 2, start: 0,  end: 3,  code: 'BMIT8080', type: 'L', venue: 'A102', lecturer: 'Dr. Patricia Gomez',       status: 'normal',  name: 'Cloud Architecture',            remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 0, event: { di: 3, start: 4,  end: 7,  code: 'BMIT7071', type: 'T', venue: 'A103', lecturer: 'Dr. Koh Li May',           status: 'normal',  name: 'Research Methods',              remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 0, event: { di: 4, start: 0,  end: 3,  code: 'BMIT7073', type: 'L', venue: 'A105', lecturer: 'Ms. Lim Pei Shan',         status: 'normal',  name: 'IT Ethics',                      remarks: '' } },

            { cohortId: 'rsd3s1g1', week: 1, event: { di: 0, start: 8,  end: 11, code: 'BMIT7070', type: 'L', venue: 'A101', lecturer: 'Prof. Dr. Khoo Teik Huat', status: 'normal',       name: 'Advanced Software Engineering', remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 1, event: { di: 1, start: 12, end: 15, code: 'BMIT7072', type: 'L', venue: 'A104', lecturer: 'Prof. Dr. Suresh',         status: 'replacement',  name: 'Capstone Project',              remarks: '25-Aug-2026' } },
            { cohortId: 'rsd3s1g1', week: 1, event: { di: 2, start: 0,  end: 3,  code: 'BMIT8080', type: 'L', venue: 'A102', lecturer: 'Dr. Patricia Gomez',       status: 'normal',       name: 'Cloud Architecture',            remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 1, event: { di: 3, start: 4,  end: 7,  code: 'BMIT7071', type: 'T', venue: 'A103', lecturer: 'Dr. Koh Li May',           status: 'normal',       name: 'Research Methods',              remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 1, event: { di: 4, start: 0,  end: 3,  code: 'BMIT7073', type: 'L', venue: 'A105', lecturer: 'Ms. Lim Pei Shan',         status: 'normal',       name: 'IT Ethics',                      remarks: '' } },

            { cohortId: 'rsd3s1g1', week: 2, event: { di: 0, start: 8,  end: 11, code: 'BMIT7070', type: 'L', venue: 'A101', lecturer: 'Prof. Dr. Khoo Teik Huat', status: 'normal',  name: 'Advanced Software Engineering', remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 2, event: { di: 1, start: 12, end: 15, code: 'BMIT7072', type: 'L', venue: 'A104', lecturer: 'Prof. Dr. Suresh',         status: 'normal',  name: 'Capstone Project',              remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 2, event: { di: 2, start: 0,  end: 3,  code: 'BMIT8080', type: 'L', venue: 'A102', lecturer: 'Dr. Patricia Gomez',       status: 'normal',  name: 'Cloud Architecture',            remarks: '' } },
            { cohortId: 'rsd3s1g1', week: 2, event: { di: 3, start: 4,  end: 7,  code: 'BMIT7071', type: 'T', venue: 'A103', lecturer: 'Dr. Koh Li May',           status: 'pending', name: 'Research Methods',              remarks: '', requestedAt: '02 Sep 2026, 02:00 PM', requestedBy: 'Dr. Koh Li May' } },
            { cohortId: 'rsd3s1g1', week: 2, event: { di: 4, start: 0,  end: 3,  code: 'BMIT7073', type: 'L', venue: 'A105', lecturer: 'Ms. Lim Pei Shan',         status: 'normal',  name: 'IT Ethics',                      remarks: '' } },

            // ═══ FOCS / DSF2 (S1) ═══
            { cohortId: 'dsf2s1', week: 0, event: { di: 0, start: 0,  end: 3,  code: 'BMIT1010', type: 'L', venue: 'B201', lecturer: 'Ms. Nurul Aini',     status: 'normal',      name: 'Introduction to Computing', remarks: '' } },
            { cohortId: 'dsf2s1', week: 0, event: { di: 1, start: 12, end: 15, code: 'BMIT1111', type: 'L', venue: 'B204', lecturer: 'Mr. Tan Kok Wai',   status: 'normal',      name: 'Operating Systems',         remarks: '' } },
            { cohortId: 'dsf2s1', week: 0, event: { di: 2, start: 4,  end: 7,  code: 'BMIT2020', type: 'L', venue: 'B202', lecturer: 'Mr. Ravi Kumar',     status: 'normal',      name: 'Programming Fundamentals',   remarks: '' } },
            { cohortId: 'dsf2s1', week: 0, event: { di: 3, start: 0,  end: 3,  code: 'BMIT2222', type: 'L', venue: 'B205', lecturer: 'Dr. Wong Mei Ling', status: 'normal',      name: 'Mathematics for Computing', remarks: '' } },
            { cohortId: 'dsf2s1', week: 0, event: { di: 4, start: 8,  end: 11, code: 'BMIT3030', type: 'T', venue: 'B203', lecturer: 'Ms. Siti Aminah',   status: 'normal',      name: 'Data Structures',           remarks: '' } },

            { cohortId: 'dsf2s1', week: 1, event: { di: 0, start: 0,  end: 3,  code: 'BMIT1010', type: 'L', venue: 'B201', lecturer: 'Ms. Nurul Aini',     status: 'normal',       name: 'Introduction to Computing', remarks: '' } },
            { cohortId: 'dsf2s1', week: 1, event: { di: 1, start: 12, end: 15, code: 'BMIT1111', type: 'L', venue: 'B204', lecturer: 'Mr. Tan Kok Wai',   status: 'normal',       name: 'Operating Systems',         remarks: '' } },
            { cohortId: 'dsf2s1', week: 1, event: { di: 2, start: 4,  end: 7,  code: 'BMIT2020', type: 'L', venue: 'B202', lecturer: 'Mr. Ravi Kumar',     status: 'replacement',  name: 'Programming Fundamentals',   remarks: '26-Aug-2026' } },
            { cohortId: 'dsf2s1', week: 1, event: { di: 3, start: 0,  end: 3,  code: 'BMIT2222', type: 'L', venue: 'B205', lecturer: 'Dr. Wong Mei Ling', status: 'normal',       name: 'Mathematics for Computing', remarks: '' } },
            { cohortId: 'dsf2s1', week: 1, event: { di: 4, start: 8,  end: 11, code: 'BMIT3030', type: 'T', venue: 'B203', lecturer: 'Ms. Siti Aminah',   status: 'normal',       name: 'Data Structures',           remarks: '' } },

            { cohortId: 'dsf2s1', week: 2, event: { di: 0, start: 0,  end: 3,  code: 'BMIT1010', type: 'L', venue: 'B201', lecturer: 'Ms. Nurul Aini',     status: 'normal',  name: 'Introduction to Computing', remarks: '' } },
            { cohortId: 'dsf2s1', week: 2, event: { di: 1, start: 12, end: 15, code: 'BMIT1111', type: 'L', venue: 'B204', lecturer: 'Mr. Tan Kok Wai',   status: 'normal',  name: 'Operating Systems',         remarks: '' } },
            { cohortId: 'dsf2s1', week: 2, event: { di: 2, start: 4,  end: 7,  code: 'BMIT2020', type: 'L', venue: 'B202', lecturer: 'Mr. Ravi Kumar',     status: 'normal',  name: 'Programming Fundamentals',   remarks: '' } },
            { cohortId: 'dsf2s1', week: 2, event: { di: 3, start: 0,  end: 3,  code: 'BMIT2222', type: 'L', venue: 'B205', lecturer: 'Dr. Wong Mei Ling', status: 'pending', name: 'Mathematics for Computing', remarks: '', requestedAt: '01 Sep 2026, 09:15 AM', requestedBy: 'Dr. Wong Mei Ling' } },
            { cohortId: 'dsf2s1', week: 2, event: { di: 4, start: 8,  end: 11, code: 'BMIT3030', type: 'T', venue: 'B203', lecturer: 'Ms. Siti Aminah',   status: 'normal',  name: 'Data Structures',           remarks: '' } },

            // ═══ FOCS / DFT2 (S1) ═══
            { cohortId: 'dft2s1', week: 0, event: { di: 0, start: 12, end: 15, code: 'BMIT6061', type: 'T', venue: 'B304', lecturer: 'Ms. Chen Hui Xin',   status: 'normal', name: 'UI/UX Design',                remarks: '' } },
            { cohortId: 'dft2s1', week: 0, event: { di: 1, start: 2,  end: 5,  code: 'BMIT4040', type: 'L', venue: 'B301', lecturer: 'En. Ahmad Faiz',     status: 'normal', name: 'Web Development',             remarks: '' } },
            { cohortId: 'dft2s1', week: 0, event: { di: 2, start: 12, end: 15, code: 'BMIT6062', type: 'L', venue: 'B305', lecturer: 'En. Zulkifli',        status: 'normal', name: 'Networking Basics',           remarks: '' } },
            { cohortId: 'dft2s1', week: 0, event: { di: 3, start: 6,  end: 9,  code: 'BMIT5050', type: 'L', venue: 'B302', lecturer: 'Pn. Farah Hanum',     status: 'normal', name: 'Database Design',             remarks: '' } },
            { cohortId: 'dft2s1', week: 0, event: { di: 4, start: 0,  end: 3,  code: 'BMIT6060', type: 'L', venue: 'B303', lecturer: 'Dr. Lim Wei Ming',   status: 'normal', name: 'Cybersecurity Fundamentals',   remarks: '' } },

            { cohortId: 'dft2s1', week: 1, event: { di: 0, start: 12, end: 15, code: 'BMIT6061', type: 'T', venue: 'B304', lecturer: 'Ms. Chen Hui Xin',   status: 'normal', name: 'UI/UX Design',                remarks: '' } },
            { cohortId: 'dft2s1', week: 1, event: { di: 1, start: 2,  end: 5,  code: 'BMIT4040', type: 'L', venue: 'B301', lecturer: 'En. Ahmad Faiz',     status: 'normal', name: 'Web Development',             remarks: '' } },
            { cohortId: 'dft2s1', week: 1, event: { di: 2, start: 12, end: 15, code: 'BMIT6062', type: 'L', venue: 'B305', lecturer: 'En. Zulkifli',        status: 'normal', name: 'Networking Basics',           remarks: '' } },
            { cohortId: 'dft2s1', week: 1, event: { di: 3, start: 6,  end: 9,  code: 'BMIT5050', type: 'L', venue: 'B302', lecturer: 'Pn. Farah Hanum',     status: 'normal', name: 'Database Design',             remarks: '' } },
            { cohortId: 'dft2s1', week: 1, event: { di: 4, start: 0,  end: 3,  code: 'BMIT6060', type: 'L', venue: 'B303', lecturer: 'Dr. Lim Wei Ming',   status: 'pending', name: 'Cybersecurity Fundamentals', remarks: '', requestedAt: '01 Sep 2026, 09:15 AM', requestedBy: 'Dr. Lim Wei Ming' } },

            { cohortId: 'dft2s1', week: 2, event: { di: 0, start: 12, end: 15, code: 'BMIT6061', type: 'T', venue: 'B304', lecturer: 'Ms. Chen Hui Xin',   status: 'normal',      name: 'UI/UX Design',                remarks: '' } },
            { cohortId: 'dft2s1', week: 2, event: { di: 1, start: 2,  end: 5,  code: 'BMIT4040', type: 'L', venue: 'B301', lecturer: 'En. Ahmad Faiz',     status: 'replacement', name: 'Web Development',             remarks: '25-Aug-2026' } },
            { cohortId: 'dft2s1', week: 2, event: { di: 2, start: 12, end: 15, code: 'BMIT6062', type: 'L', venue: 'B305', lecturer: 'En. Zulkifli',        status: 'normal',      name: 'Networking Basics',           remarks: '' } },
            { cohortId: 'dft2s1', week: 2, event: { di: 3, start: 6,  end: 9,  code: 'BMIT5050', type: 'L', venue: 'B302', lecturer: 'Pn. Farah Hanum',     status: 'normal',      name: 'Database Design',             remarks: '' } },
            { cohortId: 'dft2s1', week: 2, event: { di: 4, start: 0,  end: 3,  code: 'BMIT6060', type: 'L', venue: 'B303', lecturer: 'Dr. Lim Wei Ming',   status: 'normal',      name: 'Cybersecurity Fundamentals',   remarks: '' } },

            // ═══ FCCI / DMC2 (S1) — weeks 0 & 2 only (week 1 empty in source) ═══
            { cohortId: 'dmc2s1', week: 0, event: { di: 0, start: 6, end: 9,  code: 'COM1001', type: 'L', venue: 'E101', lecturer: 'Ms. Elaine Chen', status: 'normal',  name: 'Introduction to Mass Comm', remarks: '' } },
            { cohortId: 'dmc2s1', week: 0, event: { di: 2, start: 2, end: 5,  code: 'COM2002', type: 'L', venue: 'E102', lecturer: 'Mr. Jason Tan',  status: 'normal',  name: 'Journalism',                remarks: '' } },
            { cohortId: 'dmc2s1', week: 0, event: { di: 4, start: 4, end: 7,  code: 'COM3003', type: 'T', venue: 'E103', lecturer: 'Ms. Karen Lim',  status: 'pending', name: 'Public Relations',          remarks: '' } },

            { cohortId: 'dmc2s1', week: 2, event: { di: 0, start: 6, end: 9,  code: 'COM1001', type: 'L', venue: 'E101', lecturer: 'Ms. Elaine Chen', status: 'normal',       name: 'Introduction to Mass Comm', remarks: '' } },
            { cohortId: 'dmc2s1', week: 2, event: { di: 2, start: 2, end: 5,  code: 'COM2002', type: 'L', venue: 'E102', lecturer: 'Mr. Jason Tan',  status: 'replacement',  name: 'Journalism',                remarks: '28-Aug-2026' } },
            { cohortId: 'dmc2s1', week: 2, event: { di: 4, start: 4, end: 7,  code: 'COM3003', type: 'T', venue: 'E103', lecturer: 'Ms. Karen Lim',  status: 'pending',       name: 'Public Relations',          remarks: '' } },

            // dit2s1: NO addEvent() calls in source — left intentionally empty (fidelity).
        ],

        // RSD3 G2 base weekly events (7) + status/remark flag overrides.
        // The page reconstructs allEvents['rsd3s1g2'][0..13] from this base,
        // then applies the flags per week — matching the original
        // `for (let w=0; w<14; w++) rsd3g2Base.forEach(c => addEvent('rsd3s1g2', w, {...c, status:'normal', remarks:''}))` loop.
        rsd3g2Base: [
            { di: 0, start: 8,  end: 11, code: 'BMIT7070', type: 'L', venue: 'A101', lecturer: 'Prof. Dr. Khoo Teik Huat', name: 'Advanced Software Engineering' },
            { di: 1, start: 4,  end: 7,  code: 'BMIT7071', type: 'T', venue: 'A103', lecturer: 'Dr. Koh Li May',           name: 'Research Methods' },
            { di: 2, start: 12, end: 15, code: 'BMIT7072', type: 'L', venue: 'A104', lecturer: 'Prof. Dr. Suresh',         name: 'Capstone Project' },
            { di: 3, start: 0,  end: 3,  code: 'BMIT8080', type: 'L', venue: 'A102', lecturer: 'Dr. Patricia Gomez',       name: 'Cloud Architecture' },
            { di: 4, start: 0,  end: 3,  code: 'BMIT7073', type: 'L', venue: 'A105', lecturer: 'Ms. Lim Pei Shan',         name: 'IT Ethics' },
            { di: 1, start: 8,  end: 11, code: 'BMIT7074', type: 'T', venue: 'A106', lecturer: 'Dr. Koh Li May',           name: 'Software Testing' },
            { di: 3, start: 12, end: 15, code: 'BMIT7075', type: 'L', venue: 'A106', lecturer: 'Ms. Lim Pei Shan',         name: 'Mobile Application Development' },
        ],
        rsd3g2Flags: {
            1:  [['BMIT7072', 'replacement', '26-Aug-2026'], ['BMIT7074', 'replacement', '25-Aug-2026']],
            2:  [['BMIT7073', 'pending', ''], ['BMIT7075', 'pending', '']],
            3:  [['BMIT7070', 'replacement', '27-Aug-2026']],
            4:  [['BMIT7071', 'pending', '']],
            7:  [['BMIT7074', 'replacement', '01-Sep-2026']],
            9:  [['BMIT7075', 'pending', '']],
            11: [['BMIT7072', 'replacement', '08-Sep-2026']],
            13: [['BMIT7071', 'pending', '']],
        },
    },

    // ─────────────────────────────────────────────────────────────────────
    // §2.8  studentTimetable — consumed by Student My Timetable page.
    // `activeCohort` is the cohort ID to render by default (matches
    // cohortTimetable faculties[].cohorts[].id).
    // `cancelledFlags` maps 0-indexed week keys to arrays of cancelled
    // course codes. Week 4 cancels ALL 7 events → triggers the empty state;
    // weeks 5–6 cancel 1 event each (partial cancellation).
    // `notificationCount` drives the nav-badge dot.
    // ─────────────────────────────────────────────────────────────────────
    studentTimetable: {
        activeCohort: 'rsd3s1g2',
        cancelledFlags: {
            3: ['BMIT7070', 'BMIT7071', 'BMIT7072', 'BMIT8080', 'BMIT7073', 'BMIT7074', 'BMIT7075'],
            4: ['BMIT8080'],
            5: ['BMIT7073'],
        },
        notificationCount: 3,
    },

    // ─────────────────────────────────────────────────────────────────────
    // §2.9  requests — was my-request-history inline `mockRequests` (verbatim,
    // 20 entries, `id` first). my-request-history aliases this as a same-name
    // page-local const so its render code is unchanged. The sibling
    // request-approval page reads its OWN `approvalRequests` global (distinct
    // dataset) — see the compatibility globals at the bottom of this file.
    // ─────────────────────────────────────────────────────────────────────
    requests: [
        { id: 1,  requestedAt: '2026-07-21T00:59:10', courseCode: 'BMIT5555', courseName: 'Software Engineering',          classType: 'L', classDate: '2026-08-31', classDay: 'Monday',    timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B104', totalStudents: 35, cohortCounts: [20, 15], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending',   rejectionReason: null, replacementDate: '2026-09-02', replacementTime: '09:00 – 11:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 2,  requestedAt: '2026-06-25T09:42:36', courseCode: 'BMIT5555', courseName: 'Software Engineering',          classType: 'T', classDate: '2026-09-02', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B105', totalStudents: 28, cohorts: ['DFT2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: '2026-09-04', replacementTime: '14:00 – 16:00', replacementVenue: 'B110', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: '2026-06-26T09:00:00', remarks: null },
        { id: 3,  requestedAt: '2026-06-21T05:08:22', courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming',   classType: 'L', classDate: '2026-09-03', classDay: 'Thursday',  timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B103', totalStudents: 24, cohorts: ['DFT2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: '2026-09-07', replacementTime: '09:00 – 11:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 4,  requestedAt: '2026-07-25T23:58:08', courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming',   classType: 'T', classDate: '2026-09-07', classDay: 'Monday',    timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B106', totalStudents: 20, cohorts: ['DSF2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: '2026-09-09', replacementTime: '11:00 – 13:00', replacementVenue: 'B201', reviewedBy: 'Dr. Lim (Dean)',    reviewedAt: '2026-07-26T16:30:00', remarks: null },
        { id: 5,  requestedAt: '2026-07-03T08:25:56', courseCode: 'BMIT5678', courseName: 'Database Systems',             classType: 'T', classDate: '2026-09-08', classDay: 'Tuesday',   timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, cohortCounts: [15, 15], cohorts: ['DSF2 (S1)', 'DFT2 (S1)'], status: 'Pending',   rejectionReason: null, replacementDate: '2026-09-10', replacementTime: '11:00 – 13:00', replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 6,  requestedAt: '2026-07-01T21:19:10', courseCode: 'BMIT9012', courseName: 'Computer Networks',            classType: 'L', classDate: '2026-09-10', classDay: 'Thursday',  timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, cohorts: ['DFT2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: '2026-09-14', replacementTime: '08:00 – 10:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: '2026-07-02T10:00:00', remarks: null },
        { id: 7,  requestedAt: '2026-06-30T20:03:33', courseCode: 'BMIT3456', courseName: 'Artificial Intelligence',      classType: 'T', classDate: '2026-09-11', classDay: 'Friday',    timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, cohorts: ['DSF2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Insufficient notice period. Requests must be submitted at least 5 working days in advance.', replacementDate: '2026-09-14', replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-07-01T08:15:00', remarks: null },
        { id: 8,  requestedAt: '2026-06-26T18:34:24', courseCode: 'BMIT7890', courseName: 'Project Management',           classType: 'L', classDate: '2026-09-14', classDay: 'Monday',    timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 20, cohortCounts: [10, 10], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending',   rejectionReason: null, replacementDate: '2026-09-16', replacementTime: '14:00 – 16:00', replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 9,  requestedAt: '2026-07-25T18:03:04', courseCode: 'BMIT7890', courseName: 'Project Management',           classType: 'T', classDate: '2026-09-15', classDay: 'Tuesday',   timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, cohorts: ['DFT2 (S1)'],                          status: 'Completed', rejectionReason: null, replacementDate: '2026-09-17', replacementTime: '08:00 – 10:00', replacementVenue: 'B103', reviewedBy: 'Dr. Ahmad (HOD)',  reviewedAt: '2026-07-26T14:00:00', remarks: 'Replacement conducted successfully.' },
        { id: 10, requestedAt: '2026-06-24T23:24:55', courseCode: 'BMIT9999', courseName: 'Machine Learning',            classType: 'T', classDate: '2026-09-16', classDay: 'Wednesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 15, cohorts: ['DSF2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: '2026-09-18', replacementTime: '10:00 – 12:00', replacementVenue: 'B105', reviewedBy: 'Dr. Lim (Dean)',    reviewedAt: '2026-06-25T11:00:00', remarks: null },
        { id: 11, requestedAt: '2026-07-22T20:24:43', courseCode: 'BMIT1234', courseName: 'Data Structures',              classType: 'L', classDate: '2026-09-18', classDay: 'Friday',    timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 30, cohorts: ['DSF2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Venue unavailable on the requested replacement date.', replacementDate: '2026-09-21', replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-07-23T09:30:00', remarks: null },
        { id: 12, requestedAt: '2026-07-25T22:56:25', courseCode: 'BMIT4567', courseName: 'Web Development',              classType: 'L', classDate: '2026-09-21', classDay: 'Monday',    timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, cohorts: ['DFT2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: '2026-09-23', replacementTime: '09:00 – 11:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 13, requestedAt: '2026-08-02T07:27:52', courseCode: 'BMIT4567', courseName: 'Web Development',              classType: 'T', classDate: '2026-09-22', classDay: 'Tuesday',   timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, cohortCounts: [12, 13], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-24', replacementTime: '14:00 – 16:00', replacementVenue: 'B106', reviewedBy: 'Dr. Lim (Dean)',   reviewedAt: '2026-08-02T15:45:00', remarks: 'Replacement completed. Student attendance recorded.' },
        { id: 14, requestedAt: '2026-07-16T11:23:53', courseCode: 'BMIT8888', courseName: 'Cloud Computing',             classType: 'T', classDate: '2026-09-23', classDay: 'Wednesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, cohorts: ['DSF2 (S1)'],                          status: 'Approved',  rejectionReason: null, replacementDate: '2026-09-25', replacementTime: '10:00 – 12:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: '2026-07-17T13:00:00', remarks: null },
        { id: 15, requestedAt: '2026-06-24T05:17:27', courseCode: 'BMIT7777', courseName: 'Cybersecurity',               classType: 'L', classDate: '2026-08-31', classDay: 'Monday',    timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, cohortCounts: [10, 8],  cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Cancelled', rejectionReason: null, replacementDate: '2026-09-02', replacementTime: '08:00 – 10:00',  replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: 'Request withdrawn by lecturer.' },
        { id: 16, requestedAt: '2026-07-18T15:58:25', courseCode: 'BMIT7777', courseName: 'Cybersecurity',               classType: 'T', classDate: '2026-09-02', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, cohorts: ['DFT2 (S1)'],                          status: 'Pending',   rejectionReason: null, replacementDate: '2026-09-04', replacementTime: '14:00 – 16:00', replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 17, requestedAt: '2026-07-10T11:34:28', courseCode: 'BMIT3344', courseName: 'Embedded Systems',            classType: 'T', classDate: '2026-09-07', classDay: 'Monday',    timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B103', totalStudents: 12, cohorts: ['DSF2 (S1)'],                          status: 'Completed', rejectionReason: null, replacementDate: '2026-09-10', replacementTime: '08:00 – 10:00', replacementVenue: 'B104', reviewedBy: 'Dr. Ahmad (HOD)',   reviewedAt: '2026-07-11T08:00:00', remarks: 'Replacement completed.' },
        { id: 18, requestedAt: '2026-06-21T13:01:46', courseCode: 'BMIT2222', courseName: 'Mobile Computing',            classType: 'L', classDate: '2026-09-09', classDay: 'Wednesday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 28, cohorts: ['DFT2 (S1)'],                          status: 'Cancelled', rejectionReason: null, replacementDate: '2026-09-11', replacementTime: '09:00 – 11:00', replacementVenue: null,  reviewedBy: null,                reviewedAt: null,                remarks: null },
        { id: 19, requestedAt: '2026-06-21T10:42:58', courseCode: 'BMIT1111', courseName: 'Human-Computer Interaction',  classType: 'L', classDate: '2026-09-15', classDay: 'Tuesday',   timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 22, cohorts: ['DSF2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Scheduling conflict with another lecturer\'s booking.', replacementDate: '2026-09-17', replacementTime: '14:00 – 16:00', replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-06-22T10:30:00', remarks: null },
        { id: 20, requestedAt: '2026-06-24T13:09:46', courseCode: 'BMIT4433', courseName: 'Information Security',        classType: 'T', classDate: '2026-09-17', classDay: 'Thursday',  timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 18, cohorts: ['DFT2 (S1)'],                          status: 'Rejected',  rejectionReason: 'Lecturer unavailable on the requested date.', replacementDate: '2026-09-21', replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-06-25T08:00:00', remarks: null },
    ],

    // ─────────────────────────────────────────────────────────────────────
    // §2.10 conflictedClasses — was replacement-home inline array (14 rows,
    // verbatim). Read-only — the page does not mutate it.
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
    // §2.11 venueSlots — was replacement-arrangement inline `venueSlotData`
    // (verbatim). 4 demo venues, each a list of [dayIndex, hourIndex,
    // statusInt] triples. Read-only — the page does not mutate it.
    // ─────────────────────────────────────────────────────────────────────
    venueSlots: {
        'B103': [
            [0, 4, 0], [0, 5, 0], [0, 6, 0], [0, 7, 0],
            [1, 0, 0], [1, 1, 0], [1, 2, 0], [1, 3, 0],
            [1, 8, 0], [1, 9, 0], [1, 10, 0], [1, 11, 0],
            [2, 12, 1], [2, 13, 1], [2, 14, 1], [2, 15, 1],
            [4, 14, 4], [4, 15, 4], [4, 16, 4], [4, 17, 4],
            [4, 18, 4], [4, 19, 4], [4, 20, 4], [4, 21, 4],
            [5, 4, 3], [5, 5, 3], [5, 6, 3], [5, 7, 3],
        ],
        'B104': [
            [0, 0, 1], [0, 1, 1], [0, 2, 1], [0, 3, 1],
            [0, 8, 0], [0, 9, 0], [0, 10, 0], [0, 11, 0],
            [1, 12, 1], [1, 13, 1], [1, 14, 1], [1, 15, 1],
            [2, 4, 0], [2, 5, 0], [2, 6, 0], [2, 7, 0],
            [2, 8, 0], [2, 9, 0],
            [3, 4, 4], [3, 5, 4], [3, 6, 4], [3, 7, 4],
            [5, 12, 3], [5, 13, 3], [5, 14, 3], [5, 15, 3],
        ],
        'B105': [
            [0, 12, 0], [0, 13, 0], [0, 14, 0], [0, 15, 0],
            [1, 4, 0], [1, 5, 0], [1, 6, 0], [1, 7, 0],
            [2, 0, 1], [2, 1, 1], [2, 2, 1], [2, 3, 1],
            [2, 16, 4], [2, 17, 4], [2, 18, 4], [2, 19, 4],
            [4, 0, 3], [4, 1, 3], [4, 2, 3], [4, 3, 3],
        ],
        'B106': [
            [0, 16, 1], [0, 17, 1], [0, 18, 1], [0, 19, 1],
            [1, 0, 1], [1, 1, 1],
            [1, 16, 0], [1, 17, 0], [1, 18, 0], [1, 19, 0],
            [2, 8, 0], [2, 9, 0], [2, 10, 0], [2, 11, 0],
            [3, 0, 4], [3, 1, 4], [3, 2, 4], [3, 3, 4],
            [3, 12, 4], [3, 13, 4], [3, 14, 4], [3, 15, 4],
            [5, 8, 3], [5, 9, 3],
            [2, 0, 0], [2, 1, 0], [2, 2, 0], [2, 3, 0],
        ],
    },

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

const approvalRequests = [
    {
        id: 1,
        lecturer: 'Kylian Mbappe',
        requestedAt: '2026-08-28T09:15:00',
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'L',
        classDate: '2026-08-31',
        classDay: 'Monday',
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
        replacementDate: '2026-09-02',
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'C201',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 2,
        lecturer: 'Dembele',
        requestedAt: '2026-08-29T14:20:00',
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'T',
        classDate: '2026-09-01',
        classDay: 'Tuesday',
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
        replacementDate: '2026-09-03',
        replacementTime: '14:00 – 15:00',
        replacementVenue: 'D103',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 3,
        lecturer: 'Hakimi',
        requestedAt: '2026-08-30T10:00:00',
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: '2026-09-07',
        classDay: 'Monday',
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
        replacementDate: '2026-09-09',
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-01T08:30:00',
        remarks: null
    },
    {
        id: 4,
        lecturer: 'Hakimi',
        requestedAt: '2026-08-30T11:30:00',
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: '2026-09-01',
        classDay: 'Tuesday',
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
        replacementDate: '2026-09-04',
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'E201',
        reviewedBy: null,
        reviewedAt: null,
        remarks: 'Students have lab session on original date'
    },
    {
        id: 5,
        lecturer: 'Neymar',
        requestedAt: '2026-08-31T08:05:00',
        courseCode: 'BMIT5504',
        courseName: 'Machine Learning Fundamentals',
        classType: 'T',
        classDate: '2026-08-31',
        classDay: 'Monday',
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
        replacementDate: '2026-09-02',
        replacementTime: '11:00 – 12:00',
        replacementVenue: 'D104',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 6,
        lecturer: 'Vinicius',
        requestedAt: '2026-09-01T10:40:00',
        courseCode: 'BMIT6605',
        courseName: 'Database Administration',
        classType: 'L',
        classDate: '2026-09-07',
        classDay: 'Monday',
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
        replacementDate: '2026-09-10',
        replacementTime: '09:00 – 12:00',
        replacementVenue: 'C202',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 7,
        lecturer: 'Kylian Mbappe',
        requestedAt: '2026-09-03T09:00:00',
        courseCode: 'BMIT7706',
        courseName: 'Information Security',
        classType: 'T',
        classDate: '2026-09-10',
        classDay: 'Thursday',
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
        replacementDate: '2026-09-15',
        replacementTime: '15:00 – 16:00',
        replacementVenue: 'E202',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 8,
        lecturer: 'Dembele',
        requestedAt: '2026-09-05T15:30:00',
        courseCode: 'BMIT8807',
        courseName: 'Distributed Systems',
        classType: 'L',
        classDate: '2026-09-14',
        classDay: 'Monday',
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
        replacementDate: '2026-09-16',
        replacementTime: '10:00 – 12:00',
        replacementVenue: 'D103',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 9,
        lecturer: 'Hakimi',
        requestedAt: '2026-09-08T12:10:00',
        courseCode: 'BMIT9908',
        courseName: 'Artificial Intelligence',
        classType: 'T',
        classDate: '2026-09-21',
        classDay: 'Monday',
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
        replacementDate: '2026-09-22',
        replacementTime: '09:00 – 10:00',
        replacementVenue: 'C201',
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 10,
        lecturer: 'Neymar',
        requestedAt: '2026-09-02T09:15:00',
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'T',
        classDate: '2026-09-08',
        classDay: 'Tuesday',
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
        replacementDate: '2026-09-10',
        replacementTime: '09:00 – 10:00',
        replacementVenue: 'D104',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-02T10:15:00',
        remarks: null
    },
    {
        id: 11,
        lecturer: 'Vinicius',
        requestedAt: '2026-09-02T14:45:00',
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'L',
        classDate: '2026-09-09',
        classDay: 'Wednesday',
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
        replacementDate: '2026-09-14',
        replacementTime: '14:00 – 17:00',
        replacementVenue: 'E202',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: '2026-09-03T09:45:00',
        remarks: 'Venue swap approved with Room E202'
    },
    {
        id: 12,
        lecturer: 'Kylian Mbappe',
        requestedAt: '2026-09-04T08:30:00',
        courseCode: 'BMIT5504',
        courseName: 'Machine Learning Fundamentals',
        classType: 'T',
        classDate: '2026-09-10',
        classDay: 'Thursday',
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
        replacementDate: '2026-09-15',
        replacementTime: '10:00 – 11:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-04T14:00:00',
        remarks: null
    },
    {
        id: 13,
        lecturer: 'Dembele',
        requestedAt: '2026-09-05T11:20:00',
        courseCode: 'BMIT6605',
        courseName: 'Database Administration',
        classType: 'L',
        classDate: '2026-09-11',
        classDay: 'Friday',
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
        replacementDate: '2026-09-16',
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'D103',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: '2026-09-04T16:30:00',
        remarks: null
    },
    {
        id: 14,
        lecturer: 'Hakimi',
        requestedAt: '2026-09-06T09:35:00',
        courseCode: 'BMIT7706',
        courseName: 'Information Security',
        classType: 'T',
        classDate: '2026-09-15',
        classDay: 'Tuesday',
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
        replacementDate: '2026-09-17',
        replacementTime: '11:00 – 12:00',
        replacementVenue: 'E201',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-06T11:00:00',
        remarks: null
    },
    {
        id: 15,
        lecturer: 'Neymar',
        requestedAt: '2026-09-07T10:05:00',
        courseCode: 'BMIT8807',
        courseName: 'Distributed Systems',
        classType: 'L',
        classDate: '2026-09-16',
        classDay: 'Wednesday',
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
        replacementDate: '2026-09-21',
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'C201',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: '2026-09-07T09:20:00',
        remarks: null
    },
    {
        id: 16,
        lecturer: 'Vinicius',
        requestedAt: '2026-09-08T13:50:00',
        courseCode: 'BMIT9908',
        courseName: 'Artificial Intelligence',
        classType: 'T',
        classDate: '2026-09-17',
        classDay: 'Thursday',
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
        replacementDate: '2026-09-22',
        replacementTime: '14:00 – 15:00',
        replacementVenue: 'D104',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-08T10:40:00',
        remarks: null
    },
    {
        id: 17,
        lecturer: 'Kylian Mbappe',
        requestedAt: '2026-09-09T16:15:00',
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'L',
        classDate: '2026-09-18',
        classDay: 'Friday',
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
        replacementDate: '2026-09-23',
        replacementTime: '09:00 – 12:00',
        replacementVenue: 'E202',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: '2026-09-09T15:10:00',
        remarks: 'Suggest contacting part-time lecturer pool'
    },
    {
        id: 18,
        lecturer: 'Dembele',
        requestedAt: '2026-09-01T09:20:00',
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'T',
        classDate: '2026-09-08',
        classDay: 'Tuesday',
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
        replacementDate: '2026-09-10',
        replacementTime: '15:00 – 16:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-02T13:25:00',
        remarks: null
    },
    {
        id: 19,
        lecturer: 'Hakimi',
        requestedAt: '2026-09-02T11:55:00',
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: '2026-09-09',
        classDay: 'Wednesday',
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
        replacementDate: '2026-09-14',
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'D103',
        reviewedBy: 'Prof. Lim (PL)',
        reviewedAt: '2026-09-03T08:50:00',
        remarks: null
    },
    {
        id: 20,
        lecturer: 'Neymar',
        requestedAt: '2026-09-03T14:30:00',
        courseCode: 'BMIT5504',
        courseName: 'Machine Learning Fundamentals',
        classType: 'T',
        classDate: '2026-09-11',
        classDay: 'Friday',
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
        replacementDate: '2026-09-15',
        replacementTime: '10:00 – 11:00',
        replacementVenue: 'E201',
        reviewedBy: null,
        reviewedAt: null,
        remarks: 'Request withdrawn by lecturer'
    }
];

const URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00');

// ─────────────────────────────────────────────────────────────────────────
// Aliasing — MockData exposes the sibling request-approval globals as
// MockData.approvalRequests / MockData.urgencyReferenceDate pointing at the
// SAME values above (no data duplication). These lines run AFTER the `const`
// declarations above, so they are TDZ-safe. Do NOT move them above the consts.
// ─────────────────────────────────────────────────────────────────────────
window.MockData.approvalRequests = approvalRequests;
window.MockData.urgencyReferenceDate = URGENCY_REFERENCE_DATE;
