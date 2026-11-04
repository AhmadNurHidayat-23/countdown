import { remaining } from './time';

const dateFormatter = new Intl.DateTimeFormat('id-ID', {
    timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric',
    month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
});

export function displayDate(target) {
    return `${dateFormatter.format(target)} WIB`;
}

export function renderCountdown(element, now) {
    const target = Number(element.dataset.target);
    if (!Number.isFinite(target)) return;
    const values = remaining(target, now);
    element.querySelectorAll('[data-part]').forEach(digit => {
        const text = String(values[digit.dataset.part]).padStart(2, '0');
        if (digit.textContent !== text) digit.textContent = text;
    });
    element.querySelector('.digits').classList.toggle('has-many-days', values.days >= 1000);
    element.querySelector('[data-completion]').hidden = !values.ended;
    element.querySelector('[data-state]').textContent = values.ended ? 'Waktu telah tiba' : 'Menghitung mundur';
}

export function renderCollection(now) {
    document.querySelectorAll('[data-remaining]').forEach(element => {
        const values = remaining(Number(element.dataset.remaining), now);
        element.textContent = values.ended ? 'Acara telah dimulai.'
            : values.days > 0 ? `${values.days} hari lagi`
                : `${String(values.hours).padStart(2, '0')}:${String(values.minutes).padStart(2, '0')}:${String(values.seconds).padStart(2, '0')} lagi`;
    });
}
