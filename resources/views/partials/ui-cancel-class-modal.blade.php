{{-- ═══ Cancel Class Confirmation Modal (cancel-class-enhancement, design §5) ═══
     Promoted partial — included by my-timetable, cohort-timetable and
     venue-timetable (3rd-duplication rule, AGENTS #7). Static shell only:
     the CancelClassModal controller (ui-common.js) fills the impact preview,
     renders the reason radios and swaps confirm ↔ success states.
     House modal classes only (modal-overlay/modal/modal-*, btn-*) — tokens. --}}
<div class="modal-overlay" id="cancelClassOverlay" onclick="if(event.target===this)CancelClassModal.dismiss()">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="cancelClassTitle">Cancel Class</span>
            <button class="modal-close" onclick="CancelClassModal.dismiss()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- ─── Confirm state (impact preview + reason gating) ─── -->
            <div id="cancelClassConfirmState">
                <div id="cancelClassImpact"></div>
                <div id="cancelReasonList"></div>
                <textarea id="cancelOtherDetail" class="modal-textarea" rows="3"
                          placeholder="Describe the reason for &quot;Other&quot;&hellip;"
                          style="display:none; margin-top:10px"></textarea>
                <p id="cancelReasonHint" class="hint-text" style="margin:8px 0 0; min-height:14px"></p>
            </div>
            <!-- ─── Success state (swapped in-place, same overlay) ─── -->
            <div id="cancelClassSuccessState" style="display:none">
                <p style="margin:0 0 10px; font-weight:600; font-size:15px;">✓ Class cancelled</p>
                <p id="cancelClassSummary" class="hint-text" style="margin:0"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-outline" id="cancelKeepClassBtn" onclick="CancelClassModal.dismiss()">Keep Class</button>
            <button class="btn-danger" id="confirmCancelClassBtn" onclick="CancelClassModal.confirm()" disabled>Yes, Cancel Class</button>
            <button class="btn-action" id="cancelArrangeNowBtn" style="display:none" onclick="CancelClassModal.arrangeNow()">Arrange Replacement Now</button>
            <button class="btn-outline" id="cancelLaterBtn" style="display:none" onclick="CancelClassModal.later()">I'll Do It Later</button>
        </div>
    </div>
</div>
