import type { Meta, StoryObj } from '@storybook/vue3-vite';
import PrimaryButton from './PrimaryButton.vue';

const meta: Meta<typeof PrimaryButton> = {
    title: 'Shared/UI/PrimaryButton',
    component: PrimaryButton,
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof PrimaryButton>;

export const Default: Story = {
    render: () => ({
        components: { PrimaryButton },
        template: '<PrimaryButton>Забронировать</PrimaryButton>',
    }),
};
