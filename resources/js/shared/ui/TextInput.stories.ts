import type { Meta, StoryObj } from '@storybook/vue3-vite';
import { ref } from 'vue';
import TextInput from './TextInput.vue';

const meta: Meta<typeof TextInput> = {
    title: 'Shared/UI/TextInput',
    component: TextInput,
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof TextInput>;

export const Default: Story = {
    render: () => ({
        components: { TextInput },
        setup: () => ({ value: ref('') }),
        template:
            '<TextInput v-model="value" placeholder="email@example.com" />',
    }),
};
