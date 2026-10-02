{{-- ─── Notifications panel (overlay + dialog only; self-contained) ───
    Overlay and panel toggle display via the `.open` class (see theme.css).
    No inline onclick on overlay / ✕ / mark-all — wired by ui-common.js
    initNotifPanel(). #notifList is rendered by renderNotifList() on open; at
    0 unread it is hidden and #notifEmpty is revealed (AD-5 caught-up swap). --}}
<div class="notif-overlay" id="notifOverlay"></div>

<div class="notif-panel" id="notifPanel" role="dialog" aria-label="Notifications">
    <div class="notif-head">
        <span class="notif-head-title">Notifications</span>
        <div class="notif-head-actions">
            <span class="notif-unread-pill" id="notifUnreadPill" hidden></span>
            {{-- wired by ui-common.js initNotifPanel() --}}
            <button class="notif-markall" id="notifMarkAll" type="button" hidden>Mark all as read</button>
            {{-- wired by ui-common.js initNotifPanel() --}}
            <button class="notif-close" type="button" aria-label="Close notifications" data-tip="Close" data-tip-pos="left">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="notif-list" id="notifList"></div>

    <div class="notif-empty" id="notifEmpty" hidden>
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        <span>You're all caught up</span>
    </div>
</div>
