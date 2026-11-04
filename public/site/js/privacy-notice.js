(function () {
    'use strict';

    var notice = document.querySelector('[data-privacy-notice]');
    if (!notice) return;

    var key = 'gsf-privacy-notice-2026-11';
    var dismiss = notice.querySelector('[data-privacy-dismiss]');
    var hasAcknowledged = false;

    try { hasAcknowledged = window.localStorage.getItem(key) === 'acknowledged'; } catch (error) { /* Storage can be disabled. */ }
    if (!hasAcknowledged) notice.hidden = false;

    if (dismiss) {
        dismiss.addEventListener('click', function () {
            notice.hidden = true;
            try { window.localStorage.setItem(key, 'acknowledged'); } catch (error) { /* Continue without persistence. */ }
        });
    }

    document.querySelectorAll('[data-privacy-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            notice.hidden = false;
            if (dismiss) dismiss.focus();
        });
    });
})();
