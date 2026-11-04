import { test } from 'node:test';
import assert from 'node:assert/strict';
import { remaining, jakartaTimestamp, ServerClock } from '../../resources/js/time.js';

test('decomposes exact timestamp differences into days, hours, minutes and seconds', () => {
    assert.deepEqual(remaining(93784000, 0), { days: 1, hours: 2, minutes: 3, seconds: 4, ended: false });
    assert.equal(remaining(1001, 0).seconds, 2);
    assert.equal(remaining(1000, 0).seconds, 1);
});

test('clamps at zero and marks completion only at the target timestamp', () => {
    assert.deepEqual(remaining(0, 100000), { days: 0, hours: 0, minutes: 0, seconds: 0, ended: true });
    assert.equal(remaining(1000, 999).ended, false);
    assert.equal(remaining(1000, 1000).ended, true);
});

test('skipped ticks and refresh do not accumulate drift', () => {
    assert.equal(remaining(100000, 0).seconds, 40);
    assert.equal(remaining(100000, 65000).seconds, 35);
    assert.deepEqual(remaining(100000, 65000), remaining(100000, 65000));
});

test('WIB input is converted to UTC across midnight', () => {
    assert.equal(new Date(jakartaTimestamp('2026-11-05', '00:15')).toISOString(), '2026-11-04T17:15:00.000Z');
    assert.ok(Number.isNaN(jakartaTimestamp('', '09:00')));
});

test('server clock corrects device skew without relying on Date.now', async () => {
    const originalDateNow = Date.now;
    const originalFetch = globalThis.fetch;
    try {
        Date.now = () => 99999999999999;
        const clock = new ServerClock(100000, 0);
        assert.ok(clock.now() >= 100000 && clock.now() < 100100);
        globalThis.fetch = async () => ({ ok: true, json: async () => ({ now: 200000 }) });
        await clock.sync('/time');
        assert.ok(clock.now() >= 200000 && clock.now() < 200100);
        Date.now = () => -99999999999999;
        assert.ok(clock.now() >= 200000 && clock.now() < 200100);
        globalThis.fetch = async () => { throw new Error('offline'); };
        await assert.rejects(clock.sync('/time'));
        assert.ok(clock.now() >= 200000 && clock.now() < 200100);
    } finally {
        Date.now = originalDateNow;
        globalThis.fetch = originalFetch;
    }
});
