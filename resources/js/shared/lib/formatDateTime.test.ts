import { describe, expect, it } from 'vitest';
import {
    formatFullDateTime,
    formatScheduleDayLabel,
    formatSessionTime,
} from './formatDateTime';

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

describe('formatScheduleDayLabel', () => {
    it('labels the first day as "Сегодня"', () => {
        expect(formatScheduleDayLabel('2026-03-05', 0)).toBe('Сегодня');
    });

    it('labels the second day as "Завтра"', () => {
        expect(formatScheduleDayLabel('2026-03-06', 1)).toBe('Завтра');
    });

    it('labels later days with a capitalized weekday and date', () => {
        const label = formatScheduleDayLabel('2026-03-07', 2);

        expect(label).toContain('7');
        expect(label[0]).toBe(label[0].toUpperCase());
    });
});
