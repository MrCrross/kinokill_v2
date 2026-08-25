import type { Meta, StoryObj } from '@storybook/vue3-vite';
import type { Seat } from '../model/types';
import SeatCell from './SeatCell.vue';

const baseSeat: Seat = {
    id: 1,
    row: 2,
    column: 3,
    type: 'standard',
    price: '250.00',
    available: true,
};

const meta: Meta<typeof SeatCell> = {
    title: 'Entities/Seat/SeatCell',
    component: SeatCell,
    tags: ['autodocs'],
    decorators: [
        () => ({
            template: '<div style="width: 3rem;"><story /></div>',
        }),
    ],
};

export default meta;
type Story = StoryObj<typeof SeatCell>;

export const Available: Story = {
    args: { seat: baseSeat, selected: false },
};

export const Selected: Story = {
    args: { seat: baseSeat, selected: true },
};

export const Occupied: Story = {
    args: { seat: { ...baseSeat, available: false }, selected: false },
};

export const Vip: Story = {
    args: {
        seat: { ...baseSeat, type: 'vip', price: '450.00' },
        selected: false,
    },
};
