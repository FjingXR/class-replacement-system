// check_mock.js — RECREATED 2026-10-08. The original (47 checks, all passing)
// was lost with the /tmp wipe on reboot; this is a faithful reconstruction
// from its captured output. Counts updated for the 2026-10-08 data changes:
// BMIT3273 + MPU-2212/3103/3302 titled (38 real / 4 placeholders now).
// Run:  node check_mock.js   (from class-replacement-system/)
'use strict';
global.window = global;
global.document = { currentScript: null };
const fs = require('fs');
(0, eval)(fs.readFileSync('public/js/mock-data.js', 'utf8'));
const md = window.MockData;

let pass = 0, fail = 0;
const failures = [];
function check(name, fn) {
  try {
    const r = fn();
    if (r === false) throw new Error('assertion failed');
    console.log('PASS ' + name + (r === true || r == null ? '' : '  ->  ' + (typeof r === 'object' ? JSON.stringify(r) : r)));
    pass++;
  } catch (e) {
    fail++;
    failures.push(name + ' :: ' + e.message);
    console.log('FAIL ' + name + '  ::  ' + e.message);
  }
}
const eq = (a, b, what) => { if (a !== b) throw new Error((what || '') + ' expected ' + JSON.stringify(b) + ', got ' + JSON.stringify(a)); };

// ── 1-2 semester ──
check('semester window', () => {
  const s = md.semester;
  eq(s.startDate, '2026-09-21', 'start');
  eq(s.endDate, '2026-12-27', 'end');
  return s.label + ' · ' + s.startDate + ' ~ ' + s.endDate;
});
check('weeks', () => { eq(md.semester.weeks, 14); return 14; });

// ── 3-4 holidays (top-level md.holidays: {week, dayIndex, label}) ──
check('4 holidays', () => {
  const hs = md.holidays;
  eq(hs.length, 4, 'count');
  const names = hs.map(h => h.week + '/' + h.dayIndex + '/' + h.label).join(' ');
  if (!/Deepavali/.test(names) || !/Christmas/.test(names)) throw new Error('names missing');
  return names;
});
check('holiday week/dayIndex -> real dates', () => {
  const fmt = d => d.toISOString().slice(0, 10);
  const start = new Date(md.semester.startDate + 'T00:00:00Z');
  return md.holidays.map(h => {
    // holiday weeks are 1-BASED in the dataset (7/6 = Sun 8 Nov 2026)
    if (!(h.week >= 1 && h.week <= 14 && h.dayIndex >= 0 && h.dayIndex <= 6)) throw new Error('range ' + h.label);
    const d = new Date(start.getTime() + ((h.week - 1) * 7 + h.dayIndex) * 86400000);
    return h.label + '=' + fmt(d);
  }).join(' ');
});

// ── 5 mockNow ──
check('mockNow inside window', () => {
  const now = md.mockNow;
  if (!(now instanceof Date)) throw new Error('not a Date');
  const s = now.toString();
  if (!/Mon Oct 05 2026/.test(s)) throw new Error(s);
  return s.slice(0, 15);
});

// ── 6-7 courses ──
check('courses', () => eq(md.courses.length, 42));
check('course shape + counts', () => {
  const real = md.courses.filter(c => !/^Subj /.test(c.name));
  const ph = md.courses.filter(c => /^Subj /.test(c.name)).map(c => c.code);
  if (real.length !== 38) throw new Error('real ' + real.length + ' (expected 38 after 2026-10-08 titles)');
  if (ph.length !== 4) throw new Error('placeholders ' + ph.length);
  const types = {};
  md.courses.forEach(c => { types[c.type] = (types[c.type] || 0) + 1; });
  eq(types.L, 35, 'L'); eq(types.T, 6, 'T'); eq(types.P, 1, 'P');
  for (const c of md.courses) {
    if (!c.code || !c.name || !c.type || !Array.isArray(c.cohorts) || !Array.isArray(c.cohortCounts)) throw new Error('shape ' + c.code);
    if (c.cohorts.length !== c.cohortCounts.length) throw new Error('cohorts!=counts ' + c.code);
    if (c.studentCount !== c.cohortCounts.reduce((a, b) => a + b, 0)) throw new Error('studentCount ' + c.code);
  }
  return real.length + ' real / ' + ph.length + ' fallback: ' + ph.join(',');
});

// ── 8-10 faculties live under cohortTimetable.faculties ──
check('faculties', () => {
  const f = md.cohortTimetable.faculties;
  if (!Array.isArray(f)) throw new Error('not array');
  // original expectation '11+3': FOCS carries 11 cohorts, FAFB 3
  const counts = f.map(x => (x.cohorts || []).length);
  return counts.join('+');
});
check('cohort registry entries', () => {
  const cs = md.cohorts;
  eq(cs.length, 14, 'count');
  cs.forEach(c => {
    if (!/^[A-Z]{3}\d\(S\d\)(G\d)?$/.test(c.code)) throw new Error('code ' + c.code);
    if (!c.programme || !c.faculty) throw new Error('missing fields ' + c.code);
  });
  return cs.length + ' cohorts';
});
check('faculty cohort codes == registry codes', () => {
  const fromFaculties = new Set();
  md.cohortTimetable.faculties.forEach(f => (f.cohorts || []).forEach(c => {
    fromFaculties.add(c.name.split(' —')[0].replace(/ /g, ''));  // 'DFT1 (S1) — …' -> 'DFT1(S1)'
  }));
  const registry = new Set(md.cohorts.map(c => c.code));
  const a = [...fromFaculties].sort().join(','), b = [...registry].sort().join(',');
  eq(a, b, 'sets');
  return a;
});

// ── 11-17 cohortTimetable.events ──
const events = md.cohortTimetable.events;
check('events = 155 x 14', () => {
  eq(events.length, 2170, 'total');
  const byWeek = {};
  events.forEach(e => { byWeek[e.week] = (byWeek[e.week] || 0) + 1; });
  Object.keys(byWeek).forEach(w => eq(byWeek[w], 155, 'week ' + w));
  return '2170';
});
check('distinct cohortIds', () => new Set(events.map(e => e.cohortId)).size);
check('weeks 0..13', () => {
  const ws = [...new Set(events.map(e => e.week))].sort((a, b) => a - b).join(',');
  eq(ws, '0,1,2,3,4,5,6,7,8,9,10,11,12,13');
  return ws;
});
check('event geometry', () => {
  events.forEach(({ event: e }) => {
    if (!(e.di >= 0 && e.di <= 6)) throw new Error('di ' + e.di);
    if (!(e.start >= 0 && e.end <= 21 && e.start <= e.end)) throw new Error('slots ' + e.code);
  });
  return true;
});
check('venue is bare code', () => {
  events.forEach(({ event: e }) => {
    if (!/^[A-Z]\d{3}$/.test(e.venue)) throw new Error(e.venue);
  });
  return true;
});
check('cohort == registry code', () => {
  const names = new Set(md.cohorts.map(c => c.code));
  events.forEach(({ event: e }) => { if (!names.has(e.cohort)) throw new Error(e.cohort); });
  return true;
});
check('cohort-hours (week 0)', () => {
  const hrs = events.filter(e => e.week === 0)
    .reduce((a, e) => a + (e.event.end - e.event.start + 1) * 0.5, 0);
  eq(hrs, 229, 'week0 hours');
  return hrs;
});

// ── 18-22 statuses / hygiene ──
check('flag keys unique across template', () => {
  const seen = new Set();
  events.forEach(e => {
    const k = e.cohortId + '|' + e.week + '|' + e.event.di + '|' + e.event.start + '|' + e.event.code;
    if (seen.has(k)) throw new Error('dup ' + k);
    seen.add(k);
  });
  return seen.size + '/' + events.length;
});
check('no staff-id left in lecturer strings', () => {
  events.forEach(({ event: e }) => { if (/\d{4,}/.test(e.lecturer)) throw new Error(e.lecturer); });
  return true;
});
check('every lecturer is a registry name', () => {
  const lect = new Set(md.lecturers.map(l => l.name || l));
  events.forEach(({ event: e }) => { if (!lect.has(e.lecturer)) throw new Error(e.lecturer); });
  return true;
});
check('25 demo statuses applied', () => {
  const tally = {};
  events.forEach(({ event: e }) => { tally[e.status] = (tally[e.status] || 0) + 1; });
  eq(tally.conflict, 13, 'conflict'); eq(tally.pending, 6, 'pending'); eq(tally.replacement, 6, 'replacement');
  return tally;
});
check('no demo status masked by holiday/Sunday', () => {
  const hol = new Set(md.holidays.map(h => h.week + ':' + h.dayIndex));
  events.forEach(({ week, event: e }) => {
    if (e.status !== 'normal' && (e.di === 6 || hol.has(week + ':' + e.di))) throw new Error(e.code + ' wk' + week + ' di' + e.di);
  });
  return true;
});

// ── 23-31 rsd3g2 base/flags ──
const base = md.cohortTimetable.rsd3g2Base;
const flags = md.cohortTimetable.rsd3g2Flags;
check('rsd3g2Base length == template rows', () => {
  eq(base.length, 12);
  return '12/12';
});
check('rsd3g2Base order matches events-view (week 0)', () => {
  const view = events.filter(e => e.week === 0 && e.cohortId === 'rsd3s1g2').map(e => e.event);
  eq(view.length, base.length, 'length');
  view.forEach((e, i) => {
    eq(base[i].code, e.code, 'row ' + i + ' code');
    eq(base[i].di, e.di, 'row ' + i + ' di');
    eq(base[i].start, e.start, 'row ' + i + ' start');
  });
  return true;
});
check('rsd3g2Flags tuples', () => {
  const out = {};
  Object.keys(flags).forEach(w => {
    out[w] = flags[w];
    flags[w].forEach(t => {
      if (!/^[A-Z]{4}\d{4}$/.test(t[0])) throw new Error('code ' + t[0]);
      if (!['conflict', 'pending', 'replacement'].includes(t[1])) throw new Error('status ' + t[1]);
    });
  });
  return out;
});
check('replacement remarks point at prior week', () => {
  const start = new Date(md.semester.startDate + 'T00:00:00Z');
  Object.keys(flags).forEach(w => flags[w].forEach(t => {
    if (t[1] !== 'replacement') return;
    if (!t[2]) throw new Error('no remark');
    const d = new Date(t[2].replace(/-/g, '/') + 'T00:00:00Z');
    const pw = new Date(start.getTime() + ((+w - 1) * 7) * 86400000);
    if (d < pw || d >= new Date(pw.getTime() + 7 * 86400000)) throw new Error(t[2] + ' not in week ' + (+w - 1));
  }));
  return true;
});
check('flagged codes exist in rsd3g2Base', () => {
  const codes = new Set(base.map(b => b.code));
  Object.keys(flags).forEach(w => flags[w].forEach(t => { if (!codes.has(t[0])) throw new Error(t[0]); }));
  return true;
});
const baseCodes = {};
check('rsd3g2Base: 12 blocks / 5 distinct codes', () => {
  base.forEach(b => { (baseCodes[b.code] = baseCodes[b.code] || []).push(b); });
  const dup = Object.keys(baseCodes).filter(c => baseCodes[c].length > 1).sort();
  eq(Object.keys(baseCodes).length, 5, 'distinct');
  eq(dup.join(','), 'BMIS2113,BMIT2073,BMIT3084,BMIT3273,BMSE3153', 'dups');
  return dup.join(',');
});
check('code-keyed flags target the FIRST block of that code (views agree)', () => {
  Object.keys(flags).forEach(w => flags[w].forEach(t => {
    const first = baseCodes[t[0]][0];
    const view = events.filter(e => e.week === +w && e.cohortId === 'rsd3s1g2'
      && e.event.code === t[0] && e.event.status !== 'normal');
    if (view.length !== 1) throw new Error(t[0] + ' wk' + w + ': ' + view.length + ' non-normal blocks (expected 1)');
    if (view[0].event.di !== first.di || view[0].event.start !== first.start) {
      throw new Error(t[0] + ' wk' + w + ' snapped to di' + view[0].event.di + ':' + view[0].event.start + ', first block is di' + first.di + ':' + first.start);
    }
  }));
  return true;
});
check('events-view flags == rsd3g2Flags', () => {
  let n = 0;
  Object.keys(flags).forEach(w => flags[w].forEach(t => {
    const view = events.filter(e => e.week === +w && e.cohortId === 'rsd3s1g2' && e.event.code === t[0])
      .find(e => e.event.status === t[1]);
    if (!view) throw new Error(t[0] + ' wk' + w + ' ' + t[1] + ' not found');
    n++;
  }));
  eq(n, 5, 'flag count');
  return n + '/5';
});
check('flags content parity', () => {
  Object.keys(flags).forEach(w => flags[w].forEach(t => {
    const view = events.filter(e => e.week === +w && e.cohortId === 'rsd3s1g2' && e.event.code === t[0])
      .find(e => e.event.status === t[1]);
    if (t[2] && (view.event.remarks || '') !== t[2]) throw new Error('remarks ' + t[0]);
    if (t[3] && (view.event.requestedAt || '') !== t[3]) throw new Error('requestedAt ' + t[0]);
  }));
  return true;
});

// ── 32-34 cancelled flags (they live on studentTimetable) ──
const cancelled = md.studentTimetable.cancelledFlags;
check('cancelledFlags codes exist in rsd3g2Base', () => {
  const codes = new Set(base.map(b => b.code));
  Object.keys(cancelled).forEach(w => (cancelled[w] || []).forEach(c => { if (!codes.has(c)) throw new Error(c); }));
  return true;
});
check('week-3 cancels every block (empty state)', () => {
  const c3 = cancelled[3] || [];
  eq(new Set(c3).size, 5, 'codes');
  // the 5 cancelled codes must cover all 12 base blocks
  base.forEach(b => { if (!c3.includes(b.code)) throw new Error('block not cancelled: ' + b.code); });
  return '5 codes / 12 blocks';
});
check('activeCohort resolves', () => {
  const a = md.studentTimetable.activeCohort;
  eq(a, 'rsd3s1g2');
  return a;
});

// ── 35-42 myTimetable ──
const my = md.myTimetable.eventsByWeek;
check('myTimetable weeks 0..13', () => {
  const ws = Object.keys(my).map(Number).sort((a, b) => a - b).join(',');
  eq(ws, '0,1,2,3,4,5,6,7,8,9,10,11,12,13');
  return ws;
});
check('week 3 empty (empty state)', () => eq((my[3] || []).length, 0));
check('seed week all normal', () => {
  const bad = (my[11] || []).filter(e => e.status !== 'normal').length;
  eq(bad, 0);
  return true;
});
check('seedWeek', () => { eq(md.myTimetable.seedWeek, 11); return 11; });
check('myTimetable statuses', () => {
  const tally = {};
  Object.keys(my).forEach(w => (my[w] || []).forEach(e => { tally[e.status] = (tally[e.status] || 0) + 1; }));
  eq(tally.normal, 79, 'normal'); eq(tally.conflict, 5, 'conflict');
  eq(tally.replacement, 4, 'replacement'); eq(tally.pending, 3, 'pending');
  return tally;
});
check('demo weeks carry the full week', () => {
  Object.keys(my).forEach(w => {
    const n = (my[w] || []).length;
    if (+w === 3) return;
    if (n !== 7) throw new Error('week ' + w + ' has ' + n + ' blocks');
  });
  return true;
});
check('my blocks lecturer == currentUser.name', () => {
  Object.keys(my).forEach(w => (my[w] || []).forEach(e => {
    if (e.lecturer !== md.currentUser.name) throw new Error('wk' + w + ' ' + e.lecturer);
  }));
  return md.currentUser.name;
});
check('demo status dates fall inside 21 Sep - 27 Dec 2026', () => {
  const lo = new Date('2026-09-21'), hi = new Date('2026-12-27');
  Object.keys(my).forEach(w => (my[w] || []).forEach(e => {
    const raw = e.requestedAt || e.remarks || '';
    const m = raw.match(/(\d{1,2}) (\w{3}) (\d{4})/);
    if (!m) return;
    const d = new Date(m[0].replace(/-/g, '/'));
    if (d < lo || d > hi) throw new Error('wk' + w + ' ' + e.code + ': ' + raw);
  }));
  return true;
});

// ── 43-47 venueSlots ──
const vs = md.venueSlots;
check('venueSlots rooms', () => {
  const rooms = Object.keys(vs);
  eq(rooms.length, 23, 'rooms');
  return rooms.length;
});
check('arrays', () => {
  // venueSlots[room] = booked-slot tuples [dayIndex, startSlot, n]
  Object.keys(vs).forEach(r => {
    if (!Array.isArray(vs[r])) throw new Error(r);
    vs[r].forEach(t => {
      if (!Array.isArray(t) || t.length !== 3) throw new Error(r + ' tuple ' + JSON.stringify(t));
      if (typeof t[0] !== 'number' || typeof t[1] !== 'number' || typeof t[2] !== 'number') throw new Error(r + ' non-numeric ' + JSON.stringify(t));
    });
  });
  return true;
});
check('cell values valid', () => {
  Object.keys(vs).forEach(r => vs[r].forEach(t => {
    if (!(t[0] >= 0 && t[0] <= 6)) throw new Error(r + ' day ' + t[0]);
    if (!(t[1] >= 0 && t[1] <= 21)) throw new Error(r + ' start ' + t[1]);
    if (!(t[2] >= 1)) throw new Error(r + ' len ' + t[2]);
  }));
  return 0;
});
check('demo pending/reserved cells + occupied cells', () => {
  let tuples = 0, demo = 0;
  Object.keys(vs).forEach(r => vs[r].forEach(t => {
    tuples++;
    if (t[2] !== 1) demo++;   // the 4 multi-slot demo tuples (pending/reserved)
  }));
  eq(tuples, 298, 'occupied cells');
  eq(demo, 4, 'demo pending slots');
  return '4 demo pending/reserved; ' + tuples + ' occupied cells (147 h booked, incl. 2 h demo)';
});
check('every event venue has a venueSlots entry; no duplicate venue cells', () => {
  const rooms = new Set(Object.keys(vs));
  events.forEach(({ event: e }) => { if (!rooms.has(e.venue)) throw new Error(e.venue); });
  return true;
});

console.log('\n' + (fail === 0 ? 'ALL ' + pass + ' CHECKS PASSED' : pass + ' passed, ' + fail + ' FAILED'));
if (fail) { failures.forEach(f => console.log('  ✗ ' + f)); process.exit(1); }
