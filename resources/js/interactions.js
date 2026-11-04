export function notify(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.hidden = false;
    clearTimeout(notify.timeout);
    notify.timeout = setTimeout(() => { toast.hidden = true; }, 4000);
}

function setupCopy() {
    document.querySelectorAll('[data-copy]').forEach(button => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                notify('Tautan berhasil disalin. Bagikan momenmu!');
            } catch {
                // Also supports browsers where Clipboard API is unavailable.
                window.prompt('Salin tautan countdown ini:', button.dataset.copy);
            }
        });
    });
}

function setupFullscreen() {
    document.querySelectorAll('[data-fullscreen]').forEach(button => {
        const panel = button.closest('[data-countdown]');
        const updateButton = () => {
            const active = document.fullscreenElement === panel || panel.classList.contains('fallback-fullscreen');
            button.textContent = active ? 'Keluar layar penuh ⛶' : 'Layar penuh ⛶';
            button.setAttribute('aria-pressed', String(active));
        };
        const closeFallback = () => {
            panel.classList.remove('fallback-fullscreen');
            document.body.classList.remove('has-fullscreen');
            updateButton();
        };
        button.addEventListener('click', async () => {
            if (document.fullscreenElement) {
                try { await document.exitFullscreen(); } catch { notify('Tekan Esc untuk keluar dari layar penuh.'); }
                return;
            }
            if (panel.classList.contains('fallback-fullscreen')) return closeFallback();
            try {
                await panel.requestFullscreen();
            } catch {
                panel.classList.add('fallback-fullscreen');
                document.body.classList.add('has-fullscreen');
            }
            updateButton();
        });
        document.addEventListener('fullscreenchange', updateButton);
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeFallback();
        });
    });
}

function setupDelete() {
    const dialog = document.getElementById('delete-dialog');
    let pendingForm;
    document.querySelectorAll('[data-delete]').forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            pendingForm = form;
            dialog.returnValue = '';
            dialog.showModal();
        });
    });
    dialog.addEventListener('close', () => {
        if (dialog.returnValue === 'delete' && pendingForm) pendingForm.submit();
        pendingForm = null;
    });
}

export function setupInteractions() {
    setupCopy();
    setupFullscreen();
    setupDelete();
}
