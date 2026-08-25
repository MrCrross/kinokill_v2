import type { Seat } from '@/entities/seat/model/types';
import type { Hall } from '@/entities/session/model/types';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import SeatMap from './SeatMap.vue';

const hall: Hall = { id: 1, name: 'Зал 1', rowsCount: 1, columnsCount: 2 };

const seats: Seat[] = [
    {
        id: 1,
        row: 1,
        column: 1,
        type: 'standard',
        price: '250.00',
        available: true,
    },
    {
        id: 2,
        row: 1,
        column: 2,
        type: 'standard',
        price: '250.00',
        available: false,
    },
];

describe('SeatMap', () => {
    it('emits toggle-seat with the seat id when an available seat is clicked', async () => {
        const wrapper = mount(SeatMap, {
            props: { hall, seats, selectedSeatIds: [] },
        });

        await wrapper.findAll('button')[0].trigger('click');

        expect(wrapper.emitted('toggle-seat')).toEqual([[1]]);
    });

    it('does not emit toggle-seat when an occupied seat is clicked', async () => {
        const wrapper = mount(SeatMap, {
            props: { hall, seats, selectedSeatIds: [] },
        });

        await wrapper.findAll('button')[1].trigger('click');

        expect(wrapper.emitted('toggle-seat')).toBeUndefined();
    });

    it('renders one seat cell per seat', () => {
        const wrapper = mount(SeatMap, {
            props: { hall, seats, selectedSeatIds: [] },
        });

        expect(wrapper.findAll('button')).toHaveLength(2);
    });
});
