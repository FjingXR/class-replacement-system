@extends('layouts.ui-template', [
    'activeNav' => 'upcoming-replacements',
    'pageKey' => 'upcomingReplacements',
    'navItems' => [
        ['key'=>'my-timetable','label'=>'Student My Timetable','href'=>'/student-my-timetable-ui'],
        ['key'=>'upcoming-replacements','label'=>'Upcoming Replacements','href'=>'/upcoming-replacements-ui'],
    ],
    'notifCount' => 3,
])

@section('title', 'Upcoming Replacements')

@section('page-styles')
@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'Upcoming Replacements', 'description' => 'Replacement classes confirmed or pending for your cohort.', 'chips' => [['label' => 'RSD3(S1)G2']]])

        <!-- ─── Stub placeholder — replace with the real UI per
             page-changelogs/todo list/upcoming-replacements-ui-plan.md ─── -->
        @include('partials.ui-empty-state', ['title' => 'UI design pending', 'text' => 'The Upcoming Replacements interface will appear here once designed (Sprint 3).'])

    <!-- ═══ Copy Toast ═══ -->
    <div class="copy-toast" id="copyToast"></div>

@endsection

@section('page-scripts')

        document.addEventListener('DOMContentLoaded', function() {
            const emptyState = document.getElementById('emptyState');
            if (emptyState) emptyState.style.display = 'flex';

            const chipEl = document.getElementById('semesterChip');
            if (chipEl) chipEl.textContent = MockData.semester.chipText;

            const notifBadge = document.getElementById('notifBadge');
            if (notifBadge) notifBadge.textContent = MockData.studentTimetable.notificationCount;
        });

@endsection
