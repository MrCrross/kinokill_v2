import type { Meta, StoryObj } from '@storybook/vue3-vite';
import AppIcon from './AppIcon.vue';
import type { AppIconName } from './icons';

const meta: Meta<typeof AppIcon> = {
    title: 'Shared/UI/AppIcon',
    component: AppIcon,
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof AppIcon>;

const iconNames: AppIconName[] = [
    'armchair',
    'book-marked',
    'calendar-days',
    'check',
    'chevron-down',
    'clapperboard',
    'image-plus',
    'layout-dashboard',
    'plus',
    'save',
    'send',
    'trash-2',
    'users',
];

export const Gallery: Story = {
    render: () => ({
        components: { AppIcon },
        setup: () => ({ iconNames }),
        template: `
            <div class="flex flex-wrap items-center gap-4 text-white">
                <div
                    v-for="name in iconNames"
                    :key="name"
                    class="flex w-24 flex-col items-center gap-2 rounded bg-white/5 p-3"
                >
                    <AppIcon :name="name" :size="20" />
                    <span class="text-center text-xs text-neutral-400">{{ name }}</span>
                </div>
            </div>
        `,
    }),
};
