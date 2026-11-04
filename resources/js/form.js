import { jakartaTimestamp } from './time';
import { displayDate } from './countdown-view';

export function setupPreview(render) {
    const form = document.querySelector('[data-preview-form]');
    const preview = document.querySelector('.is-preview');
    if (!form || !preview) return;
    const name = form.elements.name;
    const description = form.elements.description;
    const date = form.elements.date;
    const time = form.elements.time;

    const update = () => {
        preview.querySelector('[data-event-name]').textContent = name.value.trim() || 'Momen berikutnya.';
        const text = preview.querySelector('[data-event-description]');
        text.textContent = description.value.trim();
        text.hidden = !description.value.trim();
        const target = jakartaTimestamp(date.value, time.value);
        if (Number.isFinite(target)) {
            preview.dataset.target = target;
            preview.querySelector('[data-event-date]').textContent = displayDate(target);
        }
        render();
    };
    form.addEventListener('input', update);
    form.addEventListener('submit', () => {
        const button = form.querySelector('[type="submit"]');
        button.disabled = true;
        button.textContent = 'Menyimpan…';
    });
    // Back/forward cache restores form values, including an old disabled button.
    window.addEventListener('pageshow', () => {
        const button = form.querySelector('[type="submit"]');
        button.disabled = false;
        button.textContent = form.querySelector('[name="_method"]') ? 'Simpan Perubahan ↗' : 'Buat Countdown ↗';
        update();
    });
    form.querySelector('.form-errors')?.focus();
    update();
}
