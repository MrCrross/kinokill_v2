import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import type { Seat } from '../model/types';
import SeatCell from './SeatCell.vue';

function makeSeat(overrides: Partial<Seat> = {}): Seat {
    return {
        id: 1,
        row: 2,
        column: 3,
        type: 'standard',
        price: '250.00',
        available: true,
        ...overrides,
    };
}

describe('SeatCell', () => {
    it('is disabled and shows an occupied label when unavailable', () => {
        const wrapper = mount(SeatCell, {
            props: { seat: makeSeat({ available: false }), selected: false },
        });

        expect(wrapper.attributes('disabled')).toBeDefined();
        expect(wrapper.attributes('aria-label')).toContain('занято');
    });

    it('emits toggle with the seat id when clicked while available', async () => {
        const wrapper = mount(SeatCell, {
            props: { seat: makeSeat({ id: 42 }), selected: false },
        });

        await wrapper.trigger('click');

        expect(wrapper.emitted('toggle')).toEqual([[42]]);
    });

    it('does not emit toggle when clicked while unavailable', async () => {
        const wrapper = mount(SeatCell, {
            props: { seat: makeSeat({ available: false }), selected: false },
        });

        await wrapper.trigger('click');

        expect(wrapper.emitted('toggle')).toBeUndefined();
    });

    it('reflects the selected state via aria-pressed and the label', () => {
        const wrapper = mount(SeatCell, {
            props: { seat: makeSeat(), selected: true },
        });

        expect(wrapper.attributes('aria-pressed')).toBe('true');
        expect(wrapper.attributes('aria-label')).toContain('выбрано');
    });
});
