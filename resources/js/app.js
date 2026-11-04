import { ServerClock } from './time';
import { renderCountdown, renderCollection } from './countdown-view';
import { setupPreview } from './form';
import { setupInteractions } from './interactions';

const clock = new ServerClock(document.body.dataset.serverNow);
const panels = document.querySelectorAll('[data-countdown]');
const render = () => {
    const now = clock.now();
    panels.forEach(panel => renderCountdown(panel, now));
    renderCollection(now);
};
let syncing = false;
const sync = async () => {
    if (syncing) return;
    syncing = true;
    try {
        await clock.sync(document.body.dataset.clockUrl);
        render();
    } catch {
        // Keep using the last server anchor while temporarily offline.
    } finally {
        syncing = false;
    }
};

setupPreview(render);
setupInteractions();
render();
sync();
setInterval(render, 250);
setInterval(() => { if (!document.hidden) sync(); }, 30000);
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) { render(); sync(); }
});
window.addEventListener('pageshow', sync);
