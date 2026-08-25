import { describe, expect, it } from 'vitest';
import { formatFullDateTime, formatSessionTime } from './formatDateTime';

describe('formatSessionTime', () => {
    it('formats an ISO datetime as day.month, hour:minute', () => {
        const formatted = formatSessionTime('2026-03-05T18:30:00');

        expect(formatted).toContain('05.03');
        expect(formatted).toContain('18:30');
    });
});

describe('formatFullDateTime', () => {
    it('includes the year in addition to day/month/time', () => {
        const formatted = formatFullDateTime('2026-03-05T18:30:00');

        expect(formatted).toContain('2026');
        expect(formatted).toContain('05.03');
        expect(formatted).toContain('18:30');
    });
});
