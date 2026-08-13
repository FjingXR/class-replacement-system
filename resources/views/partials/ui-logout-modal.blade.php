<style>
/* Logout modal vertical spacing overrides */
#logoutModal .modal-header {
    padding: 24px 24px 0;
}
#logoutModal .modal-body {
    padding: 16px 24px 24px;
}
#logoutModal .modal-body p {
    margin: 0 0 1rem;
}
#logoutModal .modal-footer {
    padding: 0 24px 24px;
    gap: 12px;
    justify-content: flex-end;
}

/* Logout countdown bar */
.logout-countdown-bar {
    width: 100%;
    height: 6px;
    background: var(--color-surface-variant);
    border-radius: var(--radius-xs);
    overflow: hidden;
    margin: 0 0 1.25rem;
}
.logout-countdown-fill {
    height: 100%;
    background: var(--color-primary);
    border-radius: var(--radius-xs);
    transition: width 1s linear;
}

/* Logout dont-ask checkbox */
.logout-dont-ask {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--color-on-surface-variant);
    cursor: pointer;
}
</style>

<div class="modal-overlay" id="logoutModal" role="alertdialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="logoutModalTitle">Confirm Logout</h3>
            <button class="modal-close" id="logoutCloseBtn" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Your session will end in <span id="logoutCountdown">5</span> seconds.</p>
            <div class="logout-countdown-bar">
                <div class="logout-countdown-fill" id="logoutCountdownFill"></div>
            </div>
            <label class="logout-dont-ask">
                <input type="checkbox" id="logoutDontAsk"> Don't ask me again
            </label>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-outline" id="logoutCancel">Cancel</button>
            <button type="button" class="btn-danger" id="logoutNow">Logout now</button>
        </div>
    </div>
</div>
