import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath } from 'node:url';
import type { StorybookConfig } from '@storybook/vue3-vite';
import { mergeConfig } from 'vite';

const config: StorybookConfig = {
    stories: ['../resources/js/**/*.stories.@(ts|js)'],
    addons: ['@storybook/addon-a11y', '@storybook/addon-docs'],
    framework: '@storybook/vue3-vite',
    async viteFinal(config) {
        return mergeConfig(config, {
            plugins: [tailwindcss()],
            resolve: {
                alias: {
                    '@': fileURLToPath(
                        new URL('../resources/js', import.meta.url),
                    ),
                },
            },
        });
    },
};

export default config;
