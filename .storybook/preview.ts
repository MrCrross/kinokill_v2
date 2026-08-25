import type { Preview } from '@storybook/vue3-vite';
import { setup } from '@storybook/vue3-vite';
import '../resources/css/app.css';

/**
 * Domain components (FilmCard, SessionTimeSlot) render Inertia's <Link>,
 * which needs a live Inertia app instance we don't have in Storybook.
 * Stub it with a plain anchor so stories can render without one — the
 * same approach used in the Vitest component tests.
 */
setup((app) => {
    app.component('Link', {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    });
});

const preview: Preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
        backgrounds: {
            default: 'kinokill-dark',
            values: [{ name: 'kinokill-dark', value: '#0a0a0a' }],
        },
    },
    decorators: [
        () => ({
            template: '<div class="bg-neutral-950 p-6"><story /></div>',
        }),
    ],
};

export default preview;
