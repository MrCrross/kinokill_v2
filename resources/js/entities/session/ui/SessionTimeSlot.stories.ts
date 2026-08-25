import type { Meta, StoryObj } from '@storybook/vue3-vite';
import SessionTimeSlot from './SessionTimeSlot.vue';

const meta: Meta<typeof SessionTimeSlot> = {
    title: 'Entities/Session/SessionTimeSlot',
    component: SessionTimeSlot,
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof SessionTimeSlot>;

export const Default: Story = {
    args: {
        id: 1,
        startsAt: '2026-09-01T19:30:00',
        hallName: 'Зал 1',
    },
};
