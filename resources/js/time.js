export function remaining(target, now) {
    const total = Math.max(0, Math.ceil((target - now) / 1000));
    return {
        days: Math.floor(total / 86400),
        hours: Math.floor(total / 3600) % 24,
        minutes: Math.floor(total / 60) % 60,
        seconds: total % 60,
        ended: now >= target,
    };
}

export function jakartaTimestamp(date, time) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(date) || !/^\d{2}:\d{2}$/.test(time)) return NaN;
    return Date.parse(`${date}T${time}:00+07:00`);
}

// A monotonic clock avoids jumps when the device's wall clock is changed.
export class ServerClock {
    constructor(serverNow, elapsed = performance.now()) {
        this.serverAnchor = Number(serverNow) + elapsed;
        this.clientAnchor = performance.now();
    }

    now() {
        return this.serverAnchor + performance.now() - this.clientAnchor;
    }

    async sync(url) {
        const start = performance.now();
        const response = await fetch(url, { cache: 'no-store', signal: AbortSignal.timeout(5000) });
        if (!response.ok) throw new Error('Sinkronisasi waktu gagal.');
        const { now } = await response.json();
        if (!Number.isFinite(now)) throw new Error('Waktu server tidak valid.');
        const end = performance.now();
        this.serverAnchor = now + (end - start) / 2;
        this.clientAnchor = end;
    }
}
