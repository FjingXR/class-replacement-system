const LOGOUT_COUNTDOWN = 5;
let logoutTimer = null;

function showLogoutModal() {
    // Close mobile nav drawer if open
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    if (drawer && drawer.classList.contains('open')) {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Check "don't ask me again"
    if (localStorage.getItem('logout_no_confirm') === '1') {
        document.getElementById('logout-form').submit();
        return;
    }

    // Show modal
    const modal = document.getElementById('logoutModal');
    modal.classList.add('show');
    let remaining = LOGOUT_COUNTDOWN;
    updateCountdownUI(remaining);

    // Countdown interval
    logoutTimer = setInterval(() => {
        remaining--;
        updateCountdownUI(remaining);
        if (remaining <= 0) {
            clearInterval(logoutTimer);
            document.getElementById('logout-form').submit();
        }
    }, 1000);

    // Button handlers
    document.getElementById('logoutNow').onclick = () => {
        clearInterval(logoutTimer);
        document.getElementById('logout-form').submit();
    };
    document.getElementById('logoutCancel').onclick = () => {
        clearInterval(logoutTimer);
        modal.classList.remove('show');
    };
    document.getElementById('logoutCloseBtn').onclick = () => {
        clearInterval(logoutTimer);
        modal.classList.remove('show');
    };

    // "Don't ask me again" handler
    document.getElementById('logoutDontAsk').onchange = (e) => {
        if (e.target.checked) {
            localStorage.setItem('logout_no_confirm', '1');
        } else {
            localStorage.removeItem('logout_no_confirm');
        }
    };
}

function updateCountdownUI(seconds) {
    document.getElementById('logoutCountdown').textContent = seconds;
    const fill = document.getElementById('logoutCountdownFill');
    const pct = (seconds / LOGOUT_COUNTDOWN) * 100;
    fill.style.width = pct + '%';
}
