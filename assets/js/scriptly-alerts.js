/**
 * ==========================================================================
 * Scriptly Custom Toast & SweetAlert Notification Subsystem
 * High-performance, tailored notification engine adhering to Scriptly UX/UI.
 * ==========================================================================
 */

(function (window) {
    'use strict';

    // SVG Icon Definitions
    const ICONS = {
        success: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>`,
        error: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>`,
        warning: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`,
        info: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
        close: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`
    };

    // Ensure Toast Container Exists in DOM
    function getToastContainer() {
        let container = document.getElementById('scriptly-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'scriptly-toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * ScriptlyToast Engine
     */
    const ScriptlyToast = {
        show: function (options) {
            const config = Object.assign({
                type: 'info', // 'success', 'error', 'warning', 'info'
                title: '',
                message: '',
                duration: 4000
            }, typeof options === 'string' ? { message: options } : options);

            const container = getToastContainer();
            const toast = document.createElement('div');
            toast.className = `scriptly-toast scriptly-toast-${config.type}`;

            const defaultTitles = {
                success: 'Success',
                error: 'Action Failed',
                warning: 'Notice',
                info: 'Information'
            };

            const title = config.title || defaultTitles[config.type] || '';

            toast.innerHTML = `
                <div class="scriptly-toast-icon">${ICONS[config.type] || ICONS.info}</div>
                <div class="scriptly-toast-content">
                    ${title ? `<div class="scriptly-toast-title">${escapeHTML(title)}</div>` : ''}
                    <div class="scriptly-toast-message">${escapeHTML(config.message)}</div>
                </div>
                <button type="button" class="scriptly-toast-close" aria-label="Close">${ICONS.close}</button>
                <div class="scriptly-toast-progress">
                    <div class="scriptly-toast-progress-bar" style="transition: transform ${config.duration}ms linear; transform: scaleX(1);"></div>
                </div>
            `;

            container.appendChild(toast);

            // Animate In
            requestAnimationFrame(() => {
                toast.classList.add('show');
                const bar = toast.querySelector('.scriptly-toast-progress-bar');
                if (bar) {
                    requestAnimationFrame(() => {
                        bar.style.transform = 'scaleX(0)';
                    });
                }
            });

            // Dismiss handler
            let isDismissed = false;
            const dismiss = () => {
                if (isDismissed) return;
                isDismissed = true;
                toast.classList.remove('show');
                toast.classList.add('hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            };

            // Close button click
            const closeBtn = toast.querySelector('.scriptly-toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', dismiss);
            }

            // Auto dismiss timer
            if (config.duration > 0) {
                setTimeout(dismiss, config.duration);
            }

            return { dismiss };
        },

        success: function (message, title) {
            return this.show({ type: 'success', message, title });
        },

        error: function (message, title) {
            return this.show({ type: 'error', message, title });
        },

        warning: function (message, title) {
            return this.show({ type: 'warning', message, title });
        },

        info: function (message, title) {
            return this.show({ type: 'info', message, title });
        }
    };

    /**
     * ScriptlyAlert (Custom SweetAlert Dialog Engine)
     */
    const ScriptlyAlert = {
        show: function (options) {
            return new Promise((resolve) => {
                const config = Object.assign({
                    type: 'info', // 'success', 'error', 'warning', 'info'
                    title: 'Notice',
                    text: '',
                    confirmText: 'OK',
                    cancelText: 'Cancel',
                    showCancel: false
                }, typeof options === 'string' ? { text: options } : options);

                // Remove existing dialog if any
                const existing = document.getElementById('scriptly-alert-overlay');
                if (existing) existing.remove();

                const overlay = document.createElement('div');
                overlay.id = 'scriptly-alert-overlay';

                overlay.innerHTML = `
                    <div class="scriptly-alert-card" role="dialog" aria-modal="true">
                        <div class="scriptly-alert-badge ${config.type}">
                            ${ICONS[config.type] || ICONS.info}
                        </div>
                        <h3 class="scriptly-alert-title">${escapeHTML(config.title)}</h3>
                        <p class="scriptly-alert-text">${escapeHTML(config.text)}</p>
                        <div class="scriptly-alert-actions">
                            ${config.showCancel ? `<button type="button" class="scriptly-alert-btn scriptly-alert-btn-cancel" id="scriptly-alert-btn-cancel">${escapeHTML(config.cancelText)}</button>` : ''}
                            <button type="button" class="scriptly-alert-btn scriptly-alert-btn-confirm" id="scriptly-alert-btn-confirm">${escapeHTML(config.confirmText)}</button>
                        </div>
                    </div>
                `;

                document.body.appendChild(overlay);

                // Animate In
                requestAnimationFrame(() => {
                    overlay.classList.add('show');
                });

                const cleanup = (result) => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(result);
                    }, 250);
                };

                const confirmBtn = overlay.querySelector('#scriptly-alert-btn-confirm');
                if (confirmBtn) {
                    confirmBtn.focus();
                    confirmBtn.addEventListener('click', () => cleanup(true));
                }

                const cancelBtn = overlay.querySelector('#scriptly-alert-btn-cancel');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => cleanup(false));
                }

                // Close on ESC key
                const handleKeyDown = (e) => {
                    if (e.key === 'Escape') {
                        document.removeEventListener('keydown', handleKeyDown);
                        cleanup(false);
                    }
                };
                document.addEventListener('keydown', handleKeyDown);
            });
        },

        success: function (title, text, confirmText) {
            return this.show({ type: 'success', title: title || 'Success', text: text || '', confirmText: confirmText || 'Continue' });
        },

        error: function (title, text, confirmText) {
            return this.show({ type: 'error', title: title || 'Error', text: text || '', confirmText: confirmText || 'Dismiss' });
        },

        warning: function (title, text, confirmText) {
            return this.show({ type: 'warning', title: title || 'Warning', text: text || '', confirmText: confirmText || 'Got It' });
        },

        info: function (title, text, confirmText) {
            return this.show({ type: 'info', title: title || 'Notice', text: text || '', confirmText: confirmText || 'OK' });
        },

        confirm: function (title, text, confirmText, cancelText) {
            return this.show({
                type: 'warning',
                title: title || 'Are you sure?',
                text: text || '',
                confirmText: confirmText || 'Confirm',
                cancelText: cancelText || 'Cancel',
                showCancel: true
            });
        }
    };

    // Helper: Escape HTML
    function escapeHTML(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Expose Global Objects
    window.ScriptlyToast = ScriptlyToast;
    window.ScriptlyAlert = ScriptlyAlert;

    // Backward-Compatible Global Helper (Replaces Old In-Page Banners)
    window.showAlert = function (type, message, title) {
        if (type === 'success') {
            ScriptlyToast.success(message, title || 'Success');
        } else if (type === 'error') {
            ScriptlyToast.error(message, title || 'Error');
        } else if (type === 'warning') {
            ScriptlyToast.warning(message, title || 'Notice');
        } else {
            ScriptlyToast.info(message, title || 'Information');
        }
    };

})(window);
